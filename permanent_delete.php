<?php
require_once 'config.php';

if (!isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$entryId = intval($_GET['id'] ?? 0);
$userId = getUserId();

if (!$entryId) {
    header('Location: dashboard.php');
    exit;
}

try {
    $db = getDB();
    
    // Verify entry belongs to user and is deleted
    $stmt = $db->prepare("SELECT photo_path FROM entries WHERE id = ? AND user_id = ? AND is_deleted = 1");
    $stmt->execute([$entryId, $userId]);
    $entry = $stmt->fetch();
    
    if (!$entry) {
        header('Location: trash.php');
        exit;
    }
    
    // Delete photo file if exists
    if ($entry['photo_path'] && file_exists($entry['photo_path'])) {
        unlink($entry['photo_path']);
    }
    
    // Permanently delete entry
    $stmt = $db->prepare("DELETE FROM entries WHERE id = ?");
    $stmt->execute([$entryId]);
    
    header('Location: trash.php');
    exit;
    
} catch (PDOException $e) {
    header('Location: trash.php');
    exit;
}