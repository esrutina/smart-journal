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

// Get stats
$stmt = $db->prepare("SELECT COUNT(*) as total FROM entries WHERE user_id = ? AND is_deleted = 0 AND is_archived = 0");
$stmt->execute([getUserId()]);
$totalEntries = $stmt->fetch()['total'];

$stmt = $db->prepare("SELECT COALESCE(current_streak, 0) as streak FROM streaks WHERE user_id = ?");
$stmt->execute([getUserId()]);
$streak = $stmt->fetch()['streak'] ?? 0;

$stmt = $db->prepare("SELECT COALESCE(SUM(word_count), 0) as total FROM entries WHERE user_id = ? AND is_deleted = 0");
$stmt->execute([getUserId()]);
$totalWords = $stmt->fetch()['total'] ?? 0;

$stmt = $db->prepare("SELECT AVG(mood_intensity) as avg FROM entries WHERE user_id = ? AND is_deleted = 0 AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
$stmt->execute([getUserId()]);
$avgMood = $stmt->fetch()['avg'] ?? 0;

// Recent entries
$stmt = $db->prepare("SELECT * FROM entries WHERE user_id = ? AND is_deleted = 0 AND is_archived = 0 ORDER BY created_at DESC LIMIT 5");
$stmt->execute([getUserId()]);
$recentEntries = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — Smart Journal</title>
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
                <a href="dashboard.php" class="nav-item active">
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
                <a href="trash.php" class="nav-item">
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
                    <h1>Welcome back, <span class="gradient-text"><?= sanitize($user['username']) ?></span> 💜</h1>
                </div>
                <a href="new-entry.php" class="btn btn-primary">
                    <span>✨</span> Write New Entry
                </a>
            </header>

            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card card">
                    <div class="stat-icon" style="background: rgba(99, 102, 241, 0.15);">📝</div>
                    <div class="stat-value" id="statEntries"><?= $totalEntries ?></div>
                    <div class="stat-label">Entries (30d)</div>
                </div>
                <div class="stat-card card">
                    <div class="stat-icon" style="background: rgba(245, 158, 11, 0.15);">🔥</div>
                    <div class="stat-value" id="statStreak"><?= $streak ?></div>
                    <div class="stat-label">Day Streak</div>
                </div>
                <div class="stat-card card">
                    <div class="stat-icon" style="background: rgba(16, 185, 129, 0.15);">✍️</div>
                    <div class="stat-value" id="statWords"><?= number_format($totalWords) ?></div>
                    <div class="stat-label">Words Written</div>
                </div>
                <div class="stat-card card">
                    <div class="stat-icon" style="background: rgba(236, 72, 153, 0.15);">😊</div>
                    <div class="stat-value" id="statMood"><?= number_format($avgMood, 1) ?></div>
                    <div class="stat-label">Avg. Mood</div>
                </div>
            </div>

            <!-- Safe Space Notice -->
            <div class="safe-space card">
                <span class="safe-space-icon">🔒</span>
                <div>
                    <strong style="color: var(--secondary-light);">Your Safe Space</strong>
                    <span style="color: var(--text-muted); margin-left: 0.5rem;">All entries are private. We never read, sell, or share your data.</span>
                </div>
            </div>

            <!-- Recent Entries -->
            <div class="recent-section">
                <div class="section-header-row">
                    <h2>Recent Entries</h2>
                    <a href="entries.php" class="btn btn-ghost btn-sm">View All →</a>
                </div>
                
                <?php if (empty($recentEntries)): ?>
                <div class="empty-state card">
                    <div class="empty-state-icon animate-float">📝</div>
                    <h3 class="empty-state-title">No entries yet</h3>
                    <p class="empty-state-desc">Start writing your first journal entry.</p>
                    <a href="new-entry.php" class="btn btn-primary" style="margin-top: 1rem;">Write New Entry</a>
                </div>
                <?php else: ?>
                <div class="entries-grid">
                    <?php foreach ($recentEntries as $entry): 
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
                    <div class="entry-card card">
                        <div class="entry-header">
                            <div class="entry-mood" style="color:<?= $mood['color'] ?>">
                                <span class="mood-emoji"><?= $mood['emoji'] ?></span>
                                <span><?= ucfirst($entry['mood']) ?></span>
                            </div>
                            <div class="entry-actions">
                                <a href="view_entry.php?id=<?php echo $entry['id']; ?>" class="btn btn-sm btn-view" title="View">
        👁️
    </a>
    <a href="edit_entry.php?id=<?php echo $entry['id']; ?>" class="btn btn-sm btn-edit" title="Edit">
        ✏️
    </a>
    <a href="download_entry.php?id=<?php echo $entry['id']; ?>" class="btn btn-sm btn-download" title="Download JSON">
        ⬇️
    </a>
                                <a href="toggle_favorite.php?id=<?= $entry['id'] ?>" class="entry-favorite-btn" style="color:<?= $entry['is_favorite'] ? '#fbbf24' : 'var(--text-muted)' ?>">
                                    <?= $entry['is_favorite'] ? '⭐' : '☆' ?>
                                </a>
                                <a href="archive.php?id=<?= $entry['id'] ?>" class="entry-archive-btn">📦</a>
                                <a href="delete.php?id=<?= $entry['id'] ?>" class="entry-delete-btn" onclick="return confirm('Move to trash?')">🗑️</a>
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
                                <?php if ($entry['word_count']): ?>• 📝 <?= $entry['word_count'] ?> words<?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script src="app.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const stats = ['statEntries', 'statStreak', 'statWords', 'statMood'];
            stats.forEach(id => {
                const el = document.getElementById(id);
                if (el && window.app) {
                    const target = parseFloat(el.textContent.replace(/,/g, ''));
                    el.textContent = '0';
                    setTimeout(() => app.animateNumber(id, target), 300);
                }
            });
        });
    </script>
</body>
</html>