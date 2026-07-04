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
    $stmt = $db->prepare("SELECT id FROM entries WHERE id = ? AND user_id = ?");
    $stmt->execute([$entryId, $userId]);
    
    if (!$stmt->fetch()) {
        header('Location: dashboard.php');
        exit;
    }
    
    // Restore entry (unarchive and undelete)
    $stmt = $db->prepare("UPDATE entries SET is_archived = 0, is_deleted = 0, deleted_at = NULL WHERE id = ?");
    $stmt->execute([$entryId]);
    
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'dashboard.php'));
    exit;
    
} catch (PDOException $e) {
    header('Location: dashboard.php');
    exit;
}