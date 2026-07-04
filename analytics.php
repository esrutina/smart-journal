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

// Get analytics data
$userId = getUserId();

// Total entries
$stmt = $db->prepare("SELECT COUNT(*) as total FROM entries WHERE user_id = ? AND is_deleted = 0");
$stmt->execute([$userId]);
$totalEntries = $stmt->fetch()['total'];

// Total words
$stmt = $db->prepare("SELECT COALESCE(SUM(word_count), 0) as total FROM entries WHERE user_id = ? AND is_deleted = 0");
$stmt->execute([$userId]);
$totalWords = $stmt->fetch()['total'];

// Avg words per entry
$avgWords = $totalEntries > 0 ? round($totalWords / $totalEntries) : 0;

// Current streak
$stmt = $db->prepare("SELECT current_streak FROM streaks WHERE user_id = ?");
$stmt->execute([$userId]);
$currentStreak = $stmt->fetch()['current_streak'] ?? 0;

// Mood distribution
$stmt = $db->prepare("SELECT mood, COUNT(*) as count FROM entries WHERE user_id = ? AND is_deleted = 0 GROUP BY mood");
$stmt->execute([$userId]);
$moodData = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

// Category distribution
$stmt = $db->prepare("SELECT categories FROM entries WHERE user_id = ? AND is_deleted = 0");
$stmt->execute([$userId]);
$allCats = $stmt->fetchAll(PDO::FETCH_COLUMN);
$catCounts = [];
foreach ($allCats as $catJson) {
    $cats = json_decode($catJson ?? '[]', true);
    foreach ($cats as $cat) {
        $catCounts[$cat] = ($catCounts[$cat] ?? 0) + 1;
    }
}

// Weekly activity (last 7 days)
$activity = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $dayName = date('D', strtotime("-$i days"));
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM entries WHERE user_id = ? AND DATE(created_at) = ? AND is_deleted = 0");
    $stmt->execute([$userId, $date]);
    $activity[$dayName] = $stmt->fetch()['count'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics — Smart Journal</title>
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
                <a href="analytics.php" class="nav-item active">
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
                    <h1>Your Journey 📈</h1>
                </div>
                <select class="form-select" style="width: auto;">
                    <option>Last 30 Days</option>
                    <option>Last 7 Days</option>
                    <option>All Time</option>
                </select>
            </header>

            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card card">
                    <div class="stat-icon" style="background: rgba(99, 102, 241, 0.15);">📝</div>
                    <div class="stat-value"><?= $totalEntries ?></div>
                    <div class="stat-label">Entries</div>
                </div>
                <div class="stat-card card">
                    <div class="stat-icon" style="background: rgba(16, 185, 129, 0.15);">✍️</div>
                    <div class="stat-value"><?= number_format($totalWords) ?></div>
                    <div class="stat-label">Total Words</div>
                </div>
                <div class="stat-card card">
                    <div class="stat-icon" style="background: rgba(139, 92, 246, 0.15);">📊</div>
                    <div class="stat-value"><?= $avgWords ?></div>
                    <div class="stat-label">Avg Words/Entry</div>
                </div>
                <div class="stat-card card">
                    <div class="stat-icon" style="background: rgba(245, 158, 11, 0.15);">🔥</div>
                    <div class="stat-value"><?= $currentStreak ?></div>
                    <div class="stat-label">Current Streak</div>
                </div>
            </div>

            <!-- Charts Row -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <!-- Mood Distribution -->
                <div class="card">
                    <h3 style="margin-bottom: 1.5rem;">Mood Distribution</h3>
                    <?php if (empty($moodData)): ?>
                    <p style="text-align: center; color: var(--text-muted); padding: 3rem 0;">No mood data yet</p>
                    <?php else: ?>
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        <?php 
                        $moodColors = [
                            'happy' => '#fbbf24', 'excited' => '#f472b6', 'calm' => '#34d399',
                            'neutral' => '#94a3b8', 'sad' => '#60a5fa', 'angry' => '#f87171',
                            'anxious' => '#a78bfa', 'tired' => '#9ca3af'
                        ];
                        $totalMoods = array_sum($moodData);
                        foreach ($moodData as $mood => $count): 
                            $percent = ($count / $totalMoods) * 100;
                        ?>
                        <div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                <span style="text-transform: capitalize; color: var(--text-secondary);"><?= $mood ?></span>
                                <span style="color: var(--text-muted);"><?= $count ?> (<?= round($percent) ?>%)</span>
                            </div>
                            <div style="background: rgba(255,255,255,0.05); border-radius: 999px; height: 8px; overflow: hidden;">
                                <div style="width: <?= $percent ?>%; height: 100%; background: <?= $moodColors[$mood] ?? '#94a3b8' ?>; border-radius: 999px; transition: width 1s ease;"></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Categories -->
                <div class="card">
                    <h3 style="margin-bottom: 1.5rem;">Categories</h3>
                    <?php if (empty($catCounts)): ?>
                    <p style="text-align: center; color: var(--text-muted); padding: 3rem 0;">No category data yet</p>
                    <?php else: ?>
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        <?php 
                        $catColors = ['work' => '#3b82f6', 'personal' => '#ec4899', 'ideas' => '#f59e0b', 'health' => '#10b981', 'gratitude' => '#8b5cf6'];
                        $totalCats = array_sum($catCounts);
                        foreach ($catCounts as $cat => $count): 
                            $percent = ($count / $totalCats) * 100;
                        ?>
                        <div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                <span style="text-transform: capitalize; color: var(--text-secondary);"><?= $cat ?></span>
                                <span style="color: var(--text-muted);"><?= $count ?> (<?= round($percent) ?>%)</span>
                            </div>
                            <div style="background: rgba(255,255,255,0.05); border-radius: 999px; height: 8px; overflow: hidden;">
                                <div style="width: <?= $percent ?>%; height: 100%; background: <?= $catColors[$cat] ?? '#94a3b8' ?>; border-radius: 999px; transition: width 1s ease;"></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Writing Activity -->
            <div class="card">
                <h3 style="margin-bottom: 1.5rem;">Writing Activity</h3>
                <?php if (array_sum($activity) === 0): ?>
                <p style="text-align: center; color: var(--text-muted); padding: 3rem 0;">No activity data yet</p>
                <?php else: ?>
                <div style="display: flex; align-items: flex-end; gap: 1rem; height: 200px; padding: 1rem 0;">
                    <?php 
                    $maxActivity = max(1, ...array_values($activity));
                    foreach ($activity as $day => $count): 
                        $height = ($count / $maxActivity) * 100;
                        $opacity = 0.3 + ($height / 100) * 0.7;
                    ?>
                    <div style="flex: 1; display: flex; flex-direction: column; align-items: center; gap: 0.5rem;">
                        <div style="width: 100%; background: linear-gradient(to top, rgba(99, 102, 241, <?= $opacity ?>), rgba(139, 92, 246, <?= $opacity ?>)); height: <?= $height ?>%; border-radius: 8px 8px 0 0; min-height: 4px; transition: height 0.5s ease;"></div>
                        <span style="font-size: 0.75rem; color: var(--text-muted);"><?= $day ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script src="app.js"></script>
</body>
</html>