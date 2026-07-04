<?php
require_once 'config.php';

if (!isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$entryId = intval($_GET['id'] ?? 0);
$userId = getUserId();

if (!$entryId) {
    header('Location: entries.php');
    exit;
}

try {
    $db = getDB();

    $stmt = $db->prepare("SELECT * FROM entries WHERE id = ? AND user_id = ? AND is_deleted = 0");
    $stmt->execute([$entryId, $userId]);
    $entry = $stmt->fetch();

    if (!$entry) {
        header('Location: entries.php?error=Entry not found');
        exit;
    }

    $categories = json_decode($entry['categories'] ?? '[]', true);
    if (!is_array($categories)) $categories = [];

} catch (PDOException $e) {
    header('Location: entries.php?error=Failed to load entry');
    exit;
}

// Get user data and streak
$db = getDB();
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([getUserId()]);
$user = $stmt->fetch();

$stmt = $db->prepare("SELECT COALESCE(current_streak, 0) as streak FROM streaks WHERE user_id = ?");
$stmt->execute([getUserId()]);
$streak = $stmt->fetch()['streak'] ?? 0;

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
$catColors = ['work' => '#3b82f6', 'personal' => '#ec4899', 'ideas' => '#f59e0b', 'health' => '#10b981', 'gratitude' => '#8b5cf6'];

// Check for images
$images = [];
if (!empty($entry['image'])) {
    $decoded = json_decode($entry['image'], true);
    if (is_array($decoded)) {
        $images = $decoded;
    } else {
        $images = array_map('trim', explode(',', $entry['image']));
    }
}
if (empty($images) && !empty($entry['images'])) {
    $decoded = json_decode($entry['images'], true);
    if (is_array($decoded)) {
        $images = $decoded;
    } else {
        $images = array_map('trim', explode(',', $entry['images']));
    }
}
$images = array_filter($images);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= sanitize($entry['title']) ?> — Smart Journal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="dashboard.css">
    <style>
        /* Layout only - colors inherit from .card and body */
        .view-entry-card {
            padding: 40px;
        }
        .view-entry-title {
            font-size: 32px;
            font-weight: 700;
            margin: 0 0 16px 0;
            font-family: 'Poppins', sans-serif;
        }
        .view-entry-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            align-items: center;
            font-size: 14px;
            opacity: 0.7;
        }
        .view-entry-content {
            font-size: 17px;
            line-height: 1.9;
            margin: 24px 0;
            white-space: pre-wrap;
            font-family: 'Inter', sans-serif;
        }
        .view-entry-header {
            margin-bottom: 24px;
            padding-bottom: 24px;
            border-bottom: 2px solid rgba(128,128,128,0.2);
        }
        .view-entry-mood {
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: 600;
            font-size: 15px;
        }
        .view-entry-images {
            margin: 24px 0;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 16px;
        }
        .view-entry-images img {
            width: 100%;
            border-radius: 12px;
            object-fit: cover;
            max-height: 400px;
        }
        .view-entry-actions {
            display: flex;
            gap: 12px;
            padding-top: 24px;
            border-top: 2px solid rgba(128,128,128,0.2);
            flex-wrap: wrap;
        }
        .view-entry-categories {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 8px;
        }
        .intensity-badge {
            padding: 2px 10px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 500;
            opacity: 0.8;
        }
        .btn-view-action {
            padding: 10px 20px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: none;
            cursor: pointer;
        }
        .btn-view-edit { background: #3b82f6; color: white; }
        .btn-view-edit:hover { background: #2563eb; }
        .btn-view-download { background: #10b981; color: white; }
        .btn-view-download:hover { background: #059669; }
        .btn-view-back { 
            background: rgba(128,128,128,0.15); 
        }
        .btn-view-back:hover { background: rgba(128,128,128,0.25); }
    </style>
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
                    <h1>View Entry 👁️</h1>
                </div>
                <a href="new-entry.php" class="btn btn-primary">
                    <span>✨</span> New Entry
                </a>
            </header>

            <div class="view-entry-card card">
                <div class="view-entry-header">
                    <h1 class="view-entry-title"><?= sanitize($entry['title']) ?></h1>
                    <div class="view-entry-meta">
                        <div class="view-entry-mood" style="color:<?= $mood['color'] ?>">
                            <span style="font-size: 22px;"><?= $mood['emoji'] ?></span>
                            <span><?= ucfirst($entry['mood']) ?></span>
                        </div>
                        <?php if ($entry['mood_intensity']): ?>
                            <span class="intensity-badge">Intensity: <?= $entry['mood_intensity'] ?>/10</span>
                        <?php endif; ?>
                        <span>📅 <?= date('F j, Y \a\t g:i A', strtotime($entry['created_at'])) ?></span>
                        <?php if ($entry['word_count']): ?>
                            <span>📝 <?= $entry['word_count'] ?> words</span>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($categories)): ?>
                    <div class="view-entry-categories">
                        <?php foreach ($categories as $cat): ?>
                        <span class="badge" style="background:<?= $catColors[$cat] ?? '#94a3b8' ?>20;color:<?= $catColors[$cat] ?? '#94a3b8' ?>;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:600;"><?= ucfirst($cat) ?></span>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="view-entry-content">
                    <?= nl2br(html_entity_decode($entry['content'], ENT_QUOTES | ENT_HTML5, 'UTF-8')) ?>
                </div>

                <?php if (!empty($images)): ?>
                <div class="view-entry-images">
                    <?php foreach ($images as $img): ?>
                        <?php if (!empty($img)): ?>
                        <img src="<?= htmlspecialchars($img) ?>" alt="Entry image" loading="lazy">
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <div class="view-entry-actions">
                    <a href="entries.php" class="btn-view-action btn-view-back">← Back</a>
                    <a href="edit_entry.php?id=<?= $entryId ?>" class="btn-view-action btn-view-edit">✏️ Edit</a>
                    <a href="download_entry.php?id=<?= $entryId ?>" class="btn-view-action btn-view-download">⬇️ Download</a>
                </div>
            </div>
        </main>
    </div>

    <script src="app.js"></script>
</body>
</html>