<?php
require_once 'config.php';

if (!isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$db = getDB();
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([getUserId()]);
$user = $stmt->fetch();

$stmt = $db->prepare("SELECT COALESCE(current_streak, 0) as streak FROM streaks WHERE user_id = ?");
$stmt->execute([getUserId()]);
$streak = $stmt->fetch()['streak'] ?? 0;

// Get trashed entries (deleted within last 30 days)
$stmt = $db->prepare("SELECT * FROM entries WHERE user_id = ? AND is_deleted = 1 AND (deleted_at IS NULL OR deleted_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)) ORDER BY created_at DESC");
$stmt->execute([getUserId()]);
$trashed = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trash — Smart Journal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
    <button class="mobile-menu-btn" id="mobileMenuBtn" onclick="document.getElementById('sidebar').classList.toggle('mobile-open')">☰</button>
    <div class="app-layout">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
<div class="sidebar-header">
    <div class="sidebar-header-top">
        <div class="brand">
            <span class="brand-icon">📓</span>
            <span class="brand-text">Smart Journal</span>
        </div>
        <button class="theme-toggle-sidebar" id="themeToggle" onclick="toggleTheme()" title="Toggle Dark/Light Mode">🌙</button>
    </div>
    <div class="streak-badge">
        🔥 <?= $streak ?> day streak
    </div>
</div>
            
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="nav-item">
                    <span class="nav-icon">📊</span>
                    <span>Dashboard</span>
                </a>
                <a href="entries.php" class="nav-item">
                    <span class="nav-icon">📝</span>
                    <span>My Entries</span>
                </a>
                <a href="new-entry.php" class="nav-item">
                    <span class="nav-icon">✨</span>
                    <span>New Entry</span>
                </a>
                <a href="analytics.php" class="nav-item">
                    <span class="nav-icon">📈</span>
                    <span>Analytics</span>
                </a>
                <a href="favorites.php" class="nav-item">
                    <span class="nav-icon">⭐</span>
                    <span>Favorites</span>
                </a>
                <a href="archived.php" class="nav-item">
                    <span class="nav-icon">📦</span>
                    <span>Archived</span>
                </a>
                <a href="trash.php" class="nav-item active">
                    <span class="nav-icon">🗑️</span>
                    <span>Trash</span>
                </a>
            </nav>
            
            <div class="sidebar-footer">
                <a href="export.php" class="nav-item">
                    <span class="nav-icon">💾</span>
                    <span>Export All Data</span>
                </a>
                <div class="user-profile">
                    <div class="user-avatar"><?= strtoupper(substr($user['username'], 0, 2)) ?></div>
                    <div class="user-info">
                        <strong><?= sanitize($user['full_name'] ?: $user['username']) ?></strong>
                        <span><?= sanitize($user['email']) ?></span>
                    </div>
                </div>
                <a href="logout.php" class="nav-item logout">
                    <span class="nav-icon">🚪</span>
                    <span>Sign Out</span>
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="page-header">
                <div>
                    <h1>Trash 🗑️</h1>
                </div>
                <a href="new-entry.php" class="btn btn-primary">
                    <span>✨</span> New Entry
                </a>
            </header>

            <?php if (empty($trashed)): ?>
            <div class="empty-state card">
                <div class="empty-state-icon animate-float">🗑️</div>
                <h3 class="empty-state-title">Trash is empty</h3>
                <p class="empty-state-desc">Deleted entries will appear here for 30 days.</p>
            </div>
            <?php else: ?>
            <div class="entries-grid">
                <?php foreach ($trashed as $entry): 
                    $moods = [
                        'happy' => ['emoji' => '😊', 'color' => '#fbbf24'],
                        'excited' => ['emoji' => '🤩', 'color' => '#f472b6'],
                        'calm' => ['emoji' => '😌', 'color' => '#34d399'],
                        'neutral' => ['emoji' => '😐', 'color' => '#94a3b8'],
                        'sad' => ['emoji' => '😢', 'color' => '#60a5fa'],
                        'angry' => ['emoji' => '😠', 'color' => '#f87171'],
                        'anxious' => ['emoji' => '😰', 'color' => '#a78bfa'],
                        'tired' => ['emoji' => '😴', 'color' => '#9ca3af']
                    ];
                    $mood = $moods[$entry['mood']] ?? $moods['neutral'];
                    $cats = json_decode($entry['categories'] ?? '[]', true);
                ?>
                <div class="entry-card card" style="opacity: 0.7;">
                    <div class="entry-header">
                        <div class="entry-mood" style="color:<?= $mood['color'] ?>">
                            <span class="mood-emoji"><?= $mood['emoji'] ?></span>
                            <span><?= ucfirst($entry['mood']) ?></span>
                        </div>
                        <div class="entry-actions">
                            <a href="restore.php?id=<?= $entry['id'] ?>" class="entry-archive-btn" title="Restore">♻️</a>
                            <a href="permanent_delete.php?id=<?= $entry['id'] ?>" class="entry-delete-btn" onclick="return confirm('Delete permanently? This cannot be undone!')" title="Delete Forever">❌</a>
                        </div>
                    </div>
                    <h3 class="entry-title"><?= sanitize($entry['title']) ?></h3>
                    <div class="entry-content"><?= substr(strip_tags($entry['content']), 0, 150) ?>...</div>
                    <div class="entry-meta">
                        <div class="entry-categories">
                            <?php foreach ($cats as $cat): 
                                $catColors = ['work' => '#3b82f6', 'personal' => '#ec4899', 'ideas' => '#f59e0b', 'health' => '#10b981', 'gratitude' => '#8b5cf6'];
                            ?>
                            <span class="badge" style="background:<?= $catColors[$cat] ?? '#94a3b8' ?>20;color:<?= $catColors[$cat] ?? '#94a3b8' ?>"><?= ucfirst($cat) ?></span>
                            <?php endforeach; ?>
                        </div>
                        <div class="entry-date">
                            📅 <?= date('M j, Y', strtotime($entry['created_at'])) ?>
                            <?php if ($entry['deleted_at']): ?>
                            • 🗑️ Deleted <?= date('M j, Y', strtotime($entry['deleted_at'])) ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </main>
    </div>

    <script src="app.js"></script>
</body>
</html>