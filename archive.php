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
    
    // Verify entry belongs to user and is not deleted
    $stmt = $db->prepare("SELECT id FROM entries WHERE id = ? AND user_id = ? AND is_deleted = 0");
    $stmt->execute([$entryId, $userId]);
    
    if (!$stmt->fetch()) {
        header('Location: dashboard.php');
        exit;
    }
    
    // Archive the entry
    $stmt = $db->prepare("UPDATE entries SET is_archived = 1 WHERE id = ?");
    $stmt->execute([$entryId]);
    
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'dashboard.php'));
    exit;
    
} catch (PDOException $e) {
    header('Location: dashboard.php');
    exit;
}