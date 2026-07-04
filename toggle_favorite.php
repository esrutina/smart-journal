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
    
    // Verify entry belongs to user
    $stmt = $db->prepare("SELECT is_favorite FROM entries WHERE id = ? AND user_id = ? AND is_deleted = 0");
    $stmt->execute([$entryId, $userId]);
    $entry = $stmt->fetch();
    
    if (!$entry) {
        header('Location: dashboard.php');
        exit;
    }
    
    // Toggle favorite
    $newFavorite = $entry['is_favorite'] ? 0 : 1;
    $stmt = $db->prepare("UPDATE entries SET is_favorite = ? WHERE id = ?");
    $stmt->execute([$newFavorite, $entryId]);
    
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'dashboard.php'));
    exit;
    
} catch (PDOException $e) {
    header('Location: dashboard.php');
    exit;
}