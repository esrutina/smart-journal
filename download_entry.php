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
    
    // Get entry
    $stmt = $db->prepare("SELECT * FROM entries WHERE id = ? AND user_id = ? AND is_deleted = 0");
    $stmt->execute([$entryId, $userId]);
    $entry = $stmt->fetch();
    
    if (!$entry) {
        header('Location: entries.php?error=Entry not found');
        exit;
    }
    
    // Build download data
    $downloadData = [
        'title' => $entry['title'],
        'content' => strip_tags($entry['content']),
        'mood' => $entry['mood'],
        'mood_intensity' => $entry['mood_intensity'],
        'categories' => json_decode($entry['categories'] ?? '[]', true),
        'word_count' => $entry['word_count'],
        'created_at' => $entry['created_at'],
        'exported_at' => date('Y-m-d H:i:s')
    ];
    
    // Set headers for JSON download
    $safeTitle = preg_replace('/[^a-zA-Z0-9]/', '-', $entry['title']);
    $filename = "entry-{$safeTitle}-" . date('Y-m-d') . ".json";
    
    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: no-cache, must-revalidate');
    
    echo json_encode($downloadData, JSON_PRETTY_PRINT);
    exit;
    
} catch (PDOException $e) {
    header('Location: entries.php?error=Failed to download entry');
    exit;
}