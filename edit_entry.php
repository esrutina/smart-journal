<?php
require_once 'config.php';

if (!isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$db = getDB();
$userId = getUserId();

// Get entry to edit
$entryId = intval($_GET['id'] ?? 0);
$stmt = $db->prepare("SELECT * FROM entries WHERE id = ? AND user_id = ? AND is_deleted = 0");
$stmt->execute([$entryId, $userId]);
$entry = $stmt->fetch();

if (!$entry) {
    header('Location: entries.php');
    exit;
}

$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

$stmt = $db->prepare("SELECT COALESCE(current_streak, 0) as streak FROM streaks WHERE user_id = ?");
$stmt->execute([$userId]);
$streak = $stmt->fetch()['streak'] ?? 0;

// Get selected categories
$selectedCats = json_decode($entry['categories'] ?? '[]', true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Entry — Smart Journal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="entry.css">
</head>
<body>
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
                <a href="entries.php" class="nav-item active">
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
                    <h1>Edit Entry ✏️</h1>
                </div>
                <div style="display: flex; gap: 1rem;">
                    <a href="entries.php" class="btn btn-ghost">Cancel</a>
                    <button type="submit" form="editForm" class="btn btn-primary">
                        <span>💾</span> Save Changes
                    </button>
                </div>
            </header>

            <form id="editForm" action="update_entry.php" method="POST" enctype="multipart/form-data" class="entry-form">
                <input type="hidden" name="entry_id" value="<?= $entry['id'] ?>">
                
                <!-- Title -->
                <div class="form-group">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" id="entryTitle" class="form-input" value="<?= sanitize($entry['title']) ?>" required>
                </div>

                <!-- Mood Selector -->
                <div class="form-group">
                    <label class="form-label">How are you feeling?</label>
                    <div class="mood-selector">
                        <input type="hidden" name="mood" id="selectedMood" value="<?= $entry['mood'] ?>">
                        <?php 
                        $moods = ['happy','excited','calm','neutral','sad','angry','anxious','tired'];
                        $moodEmojis = ['happy'=>'😊','excited'=>'🤩','calm'=>'😌','neutral'=>'😐','sad'=>'😢','angry'=>'😠','anxious'=>'😰','tired'=>'😴'];
                        foreach ($moods as $m): 
                            $active = $entry['mood'] === $m ? 'active' : '';
                        ?>
                        <div class="mood-btn <?= $active ?>" data-mood="<?= $m ?>" onclick="selectMood('<?= $m ?>', this)">
                            <span class="mood-emoji"><?= $moodEmojis[$m] ?></span>
                            <span class="mood-label"><?= ucfirst($m) ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Mood Intensity -->
                <div class="form-group">
                    <label class="form-label">Mood Intensity (1-10)</label>
                    <div class="intensity-wrapper">
                        <span class="intensity-label">Low</span>
                        <input type="range" name="mood_intensity" id="moodIntensity" class="range-slider" min="1" max="10" value="<?= $entry['mood_intensity'] ?>">
                        <span class="intensity-value" id="intensityValue"><?= $entry['mood_intensity'] ?></span>
                        <span class="intensity-label">High</span>
                    </div>
                </div>

                <!-- Categories -->
                <div class="form-group">
                    <label class="form-label">Categories</label>
                    <div class="category-selector">
                        <input type="hidden" name="categories" id="selectedCategories" value='<?= $entry['categories'] ?? '[]' ?>'>
                        <?php 
                        $allCats = ['gratitude'=>'#8b5cf6','health'=>'#10b981','ideas'=>'#f59e0b','personal'=>'#ec4899','work'=>'#3b82f6'];
                        foreach ($allCats as $cat => $color): 
                            $active = in_array($cat, $selectedCats) ? 'active' : '';
                        ?>
                        <button type="button" class="cat-tag cat-<?= $cat ?> <?= $active ?>" data-category="<?= $cat ?>" onclick="toggleCategory('<?= $cat ?>', this)">
                            <span class="cat-dot" style="background:<?= $color ?>"></span> <?= ucfirst($cat) ?>
                        </button>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Photo -->
                <?php if ($entry['photo_path']): ?>
                <div class="form-group">
                    <label class="form-label">Current Photo</label>
                    <div style="border-radius: var(--radius-lg); overflow: hidden; max-width: 300px;">
                        <img src="<?= $entry['photo_path'] ?>" style="width: 100%; display: block;">
                    </div>
                    <label class="custom-checkbox" style="margin-top: 0.5rem;">
                        <input type="checkbox" name="remove_photo" value="1">
                        <span class="checkmark"></span>
                        <span style="color: var(--text-secondary); font-size: 0.9rem;">Remove photo</span>
                    </label>
                </div>
                <?php endif; ?>

                <div class="form-group">
                    <label class="form-label"><?= $entry['photo_path'] ? 'Replace Photo' : 'Capture a Memory 📷' ?></label>
                    <div class="dropzone" id="photoDropzone" onclick="document.getElementById('photoInput').click()">
                        <div class="dropzone-content">
                            <span class="dropzone-icon">📷</span>
                            <p>Click to upload a photo</p>
                            <small>JPG, PNG up to 5MB</small>
                        </div>
                        <div class="dropzone-preview hidden" id="photoPreview"></div>
                    </div>
                    <input type="file" name="photo" id="photoInput" accept="image/jpeg,image/png" style="display: none;" onchange="handlePhotoUpload(this)">
                </div>

                <!-- Rich Text Editor -->
                <div class="form-group">
                    <label class="form-label">Your Thoughts</label>
                    <div class="editor-toolbar">
                        <button type="button" onclick="document.execCommand('bold',false,null)" title="Bold"><b>B</b></button>
                        <button type="button" onclick="document.execCommand('italic',false,null)" title="Italic"><i>I</i></button>
                        <button type="button" onclick="document.execCommand('underline',false,null)" title="Underline"><u>U</u></button>
                        <button type="button" onclick="document.execCommand('insertUnorderedList',false,null)" title="Bullet List">• List</button>
                        <button type="button" onclick="document.execCommand('insertOrderedList',false,null)" title="Numbered List">1. List</button>
                        <button type="button" onclick="document.execCommand('formatBlock',false,'H2')" title="Heading">H2</button>
                        <button type="button" onclick="document.execCommand('formatBlock',false,'BLOCKQUOTE')" title="Quote">Quote</button>
                    </div>
                    <div class="editor-content" id="entryContent" contenteditable="true" placeholder="Write your thoughts here..."><?= $entry['content'] ?></div>
                    <textarea name="content" id="entryContentHidden" style="display: none;"></textarea>
                    <div class="editor-footer">
                        <span id="wordCount">0 words</span>
                        <label class="custom-checkbox" style="margin-left: auto;">
                            <input type="checkbox" name="is_favorite" value="1" <?= $entry['is_favorite'] ? 'checked' : '' ?>>
                            <span class="checkmark"></span>
                            <span style="color: var(--text-secondary); font-size: 0.9rem;">Mark as favorite ⭐</span>
                        </label>
                    </div>
                </div>
            </form>
        </main>
    </div>

    <script>
        function selectMood(mood, btn) {
            document.querySelectorAll('.mood-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            document.getElementById('selectedMood').value = mood;
        }

        function toggleCategory(category, btn) {
            btn.classList.toggle('active');
            const selected = Array.from(document.querySelectorAll('.cat-tag.active')).map(b => b.dataset.category);
            document.getElementById('selectedCategories').value = JSON.stringify(selected);
        }

        const slider = document.getElementById('moodIntensity');
        const valueDisplay = document.getElementById('intensityValue');
        if (slider && valueDisplay) {
            slider.addEventListener('input', (e) => {
                const val = e.target.value;
                valueDisplay.textContent = val;
                const percent = (val - 1) / 9 * 100;
                slider.style.background = `linear-gradient(to right, var(--primary) ${percent}%, rgba(255,255,255,0.1) ${percent}%)`;
            });
            slider.dispatchEvent(new Event('input'));
        }

        function handlePhotoUpload(input) {
            const file = input.files[0];
            if (!file) return;
            if (file.size > 5 * 1024 * 1024) {
                alert('File too large. Max 5MB.');
                return;
            }
            const reader = new FileReader();
            reader.onload = (e) => {
                const preview = document.getElementById('photoPreview');
                preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
                preview.classList.remove('hidden');
                document.querySelector('.dropzone-content').classList.add('hidden');
            };
            reader.readAsDataURL(file);
        }

        const dropzone = document.getElementById('photoDropzone');
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(event => {
            dropzone.addEventListener(event, (e) => {
                e.preventDefault();
                e.stopPropagation();
            });
        });
        ['dragenter', 'dragover'].forEach(event => {
            dropzone.addEventListener(event, () => dropzone.classList.add('dragover'));
        });
        ['dragleave', 'drop'].forEach(event => {
            dropzone.addEventListener(event, () => dropzone.classList.remove('dragover'));
        });
        dropzone.addEventListener('drop', (e) => {
            const files = e.dataTransfer.files;
            if (files.length) {
                document.getElementById('photoInput').files = files;
                handlePhotoUpload(document.getElementById('photoInput'));
            }
        });

        const editor = document.getElementById('entryContent');
        function updateWordCount() {
            const text = editor.innerText || '';
            const words = text.trim().split(/\s+/).filter(w => w.length > 0).length;
            document.getElementById('wordCount').textContent = `${words} word${words !== 1 ? 's' : ''}`;
            document.getElementById('entryContentHidden').value = editor.innerHTML;
        }
        editor.addEventListener('input', updateWordCount);
        updateWordCount();

        document.getElementById('editForm').addEventListener('submit', (e) => {
            document.getElementById('entryContentHidden').value = editor.innerHTML;
        });
    </script>
</body>
</html>