<?php
require_once 'config.php';

if (!isLoggedIn()) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: entries.php');
    exit;
}

$userId = getUserId();
$entryId = intval($_POST['entry_id'] ?? 0);
$title = trim($_POST['title'] ?? '');
$content = $_POST['content'] ?? '';
$mood = $_POST['mood'] ?? 'neutral';
$intensity = intval($_POST['mood_intensity'] ?? 5);
$categories = $_POST['categories'] ?? '[]';
$isFavorite = isset($_POST['is_favorite']) ? 1 : 0;
$removePhoto = isset($_POST['remove_photo']) ? 1 : 0;

if (!$entryId || !$title) {
    header('Location: entries.php?error=Invalid request');
    exit;
}

try {
    $db = getDB();
    
    // Verify entry belongs to user
    $stmt = $db->prepare("SELECT photo_path FROM entries WHERE id = ? AND user_id = ? AND is_deleted = 0");
    $stmt->execute([$entryId, $userId]);
    $entry = $stmt->fetch();
    
    if (!$entry) {
        header('Location: entries.php?error=Entry not found');
        exit;
    }
    
    $wordCount = str_word_count(strip_tags($content));
    $photoPath = $entry['photo_path'];
    
    // Handle photo removal
    if ($removePhoto && $photoPath) {
        if (file_exists($photoPath)) {
            unlink($photoPath);
        }
        $photoPath = null;
    }
    
    // Handle new photo upload
    if (!empty($_FILES['photo']['tmp_name'])) {
        $allowedTypes = ['image/jpeg', 'image/png'];
        $fileType = $_FILES['photo']['type'];
        
        if (!in_array($fileType, $allowedTypes)) {
            header("Location: edit_entry.php?id=$entryId&error=Only JPG and PNG images allowed");
            exit;
        }
        
        if ($_FILES['photo']['size'] > 5 * 1024 * 1024) {
            header("Location: edit_entry.php?id=$entryId&error=Image must be less than 5MB");
            exit;
        }
        
        // Remove old photo if exists
        if ($photoPath && file_exists($photoPath)) {
            unlink($photoPath);
        }
        
        $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $ext;
        $uploadPath = UPLOAD_DIR . $filename;
        
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadPath)) {
            $photoPath = 'uploads/' . $filename;
        }
    }
    
    // Update entry
    $stmt = $db->prepare("UPDATE entries SET title = ?, content = ?, mood = ?, mood_intensity = ?, categories = ?, photo_path = ?, is_favorite = ?, word_count = ? WHERE id = ? AND user_id = ?");
    $stmt->execute([$title, $content, $mood, $intensity, $categories, $photoPath, $isFavorite, $wordCount, $entryId, $userId]);
    
    header('Location: entries.php?success=Entry updated successfully');
    exit;
    
} catch (PDOException $e) {
    header('Location: entries.php?error=Failed to update entry');
    exit;
}