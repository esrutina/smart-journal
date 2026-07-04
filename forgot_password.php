<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: index.php?error=Invalid email address');
        exit;
    }
    
    // In a real app, send email here. For now, show success message
    header('Location: index.php?success=Password reset link sent to your email');
    exit;
}

header('Location: index.php');
exit;