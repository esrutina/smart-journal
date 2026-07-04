<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$fullName = trim($_POST['full_name'] ?? '');
$password = $_POST['password'] ?? '';

// Validation
if (!$username || !$email || !$password) {
    header('Location: index.php?error=All fields are required');
    exit;
}

if (strlen($password) < 6) {
    header('Location: index.php?error=Password must be at least 6 characters');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: index.php?error=Invalid email address');
    exit;
}

try {
    $db = getDB();
    
    // Check if username or email already exists
    $stmt = $db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    if ($stmt->fetch()) {
        header('Location: index.php?error=Username or email already exists');
        exit;
    }
    
    // Hash password
    $passwordHash = password_hash($password, PASSWORD_BCRYPT);
    
    // Insert user
    $stmt = $db->prepare("INSERT INTO users (username, email, full_name, password_hash) VALUES (?, ?, ?, ?)");
    $stmt->execute([$username, $email, $fullName, $passwordHash]);
    
    $userId = $db->lastInsertId();
    
    // Initialize streak
    $stmt = $db->prepare("INSERT INTO streaks (user_id, current_streak, longest_streak) VALUES (?, 0, 0)");
    $stmt->execute([$userId]);
    
    // Auto login
    $_SESSION['user_id'] = $userId;
    $_SESSION['username'] = $username;
    
    header('Location: dashboard.php');
    exit;
    
} catch (PDOException $e) {
    header('Location: index.php?error=Registration failed. Please try again.');
    exit;
}