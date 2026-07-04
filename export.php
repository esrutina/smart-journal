<?php
require_once 'config.php';

if (!isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$db = getDB();
$userId = getUserId();

// Get all user entries
$stmt = $db->prepare("SELECT id, title, content, mood, mood_intensity, categories, photo_path, is_favorite, is_archived, word_count, created_at, updated_at FROM entries WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$userId]);
$entries = $stmt->fetchAll();

// Get user info
$stmt = $db->prepare("SELECT username, email, full_name, created_at as user_since FROM users WHERE id = ?");
$stmt->execute([$userId]);
$userInfo = $stmt->fetch();

// Build export data
$exportData = [
    'exported_at' => date('Y-m-d H:i:s'),
    'user' => $userInfo,
    'total_entries' => count($entries),
    'entries' => []
];

foreach ($entries as $entry) {
    $exportData['entries'][] = [
        'id' => $entry['id'],
        'title' => $entry['title'],
        'content' => $entry['content'],
        'mood' => $entry['mood'],
        'mood_intensity' => $entry['mood_intensity'],
        'categories' => json_decode($entry['categories'] ?? '[]', true),
        'photo_path' => $entry['photo_path'],
        'is_favorite' => (bool)$entry['is_favorite'],
        'is_archived' => (bool)$entry['is_archived'],
        'word_count' => $entry['word_count'],
        'created_at' => $entry['created_at'],
        'updated_at' => $entry['updated_at']
    ];
}

// Set headers for JSON download
header('Content-Type: application/json');
header('Content-Disposition: attachment; filename="smart-journal-export-' . date('Y-m-d') . '.json"');
header('Cache-Control: no-cache, must-revalidate');

echo json_encode($exportData, JSON_PRETTY_PRINT);
exit;