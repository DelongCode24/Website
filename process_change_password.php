<?php
require "db.php";
require "functions.php";
session_start();

// Protect page
requireAuth();

// Validate CSRF
if (!validateCSRF()) {
    die("Invalid request");
}

// Get input
$current = post('current_password');
$new = post('new_password');

// Validate password length
if (strlen($new) < 8) {
    redirectWithError('account.php', 'password_short');
}

// Verify current password
$stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user || !password_verify($current, $user['password'])) {
    redirectWithError('account.php', 'password');
}

// Update password
$newHash = password_hash($new, PASSWORD_DEFAULT);
$stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
$stmt->execute([$newHash, $_SESSION['user_id']]);

// Success
redirectWithSuccess('account.php', 'password');