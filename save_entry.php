<?php
require_once 'config.php';

if (!isLoggedIn()) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: new-entry.php');
    exit;
}

$userId = getUserId();
$title = trim($_POST['title'] ?? '');
$content = $_POST['content'] ?? '';
$mood = $_POST['mood'] ?? 'neutral';
$intensity = intval($_POST['mood_intensity'] ?? 5);
$categories = $_POST['categories'] ?? '[]';
$isFavorite = isset($_POST['is_favorite']) ? 1 : 0;

if (!$title) {
    header('Location: new-entry.php?error=Title is required');
    exit;
}

// Calculate word count
$wordCount = str_word_count(strip_tags($content));

// Handle photo upload
$photoPath = null;
if (!empty($_FILES['photo']['tmp_name'])) {
    $allowedTypes = ['image/jpeg', 'image/png'];
    $fileType = $_FILES['photo']['type'];
    
    if (!in_array($fileType, $allowedTypes)) {
        header('Location: new-entry.php?error=Only JPG and PNG images allowed');
        exit;
    }
    
    if ($_FILES['photo']['size'] > 5 * 1024 * 1024) {
        header('Location: new-entry.php?error=Image must be less than 5MB');
        exit;
    }
    
    $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $ext;
    $uploadPath = UPLOAD_DIR . $filename;
    
    if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadPath)) {
        $photoPath = 'uploads/' . $filename;
    }
}

try {
    $db = getDB();
    
    // Insert entry
    $stmt = $db->prepare("INSERT INTO entries (user_id, title, content, mood, mood_intensity, categories, photo_path, is_favorite, word_count) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$userId, $title, $content, $mood, $intensity, $categories, $photoPath, $isFavorite, $wordCount]);
    
    // Update streak
    $today = date('Y-m-d');
    $stmt = $db->prepare("SELECT * FROM streaks WHERE user_id = ?");
    $stmt->execute([$userId]);
    $streak = $stmt->fetch();
    
    if ($streak) {
        $lastDate = $streak['last_entry_date'] ?? null;
        
        if ($lastDate === $today) {
            // Already wrote today - no streak change
        } elseif ($lastDate === date('Y-m-d', strtotime('-1 day'))) {
            // Consecutive day - increase streak
            $newStreak = $streak['current_streak'] + 1;
            $newLongest = max($streak['longest_streak'], $newStreak);
            $stmt = $db->prepare("UPDATE streaks SET current_streak = ?, longest_streak = ?, last_entry_date = ? WHERE user_id = ?");
            $stmt->execute([$newStreak, $newLongest, $today, $userId]);
        } else {
            // Streak broken - reset to 1
            $stmt = $db->prepare("UPDATE streaks SET current_streak = 1, last_entry_date = ? WHERE user_id = ?");
            $stmt->execute([$today, $userId]);
        }
    }
    
    // Clear draft from localStorage (will be handled by JS)
    header('Location: dashboard.php?success=Entry saved successfully');
    exit;
    
} catch (PDOException $e) {
    header('Location: new-entry.php?error=Failed to save entry');
    exit;
}