<?php
require 'db.php';
require 'functions.php';
session_start();

// Validate CSRF
if (!validateCSRF()) {
    die('Invalid request');
}

// Get sanitized input
$username = post('username');
$email = post('email');
$password = post('password');

// Validate required fields
if (!$username || !$email || !$password) {
    redirectWithError('signup.php', 'missing_fields');
}

// Password length check
if (strlen($password) < 8) {
    redirectWithError('signup.php', 'password_short');
}

// Hash password
$hash = password_hash($password, PASSWORD_DEFAULT);

// Insert user
$stmt = $pdo->prepare('INSERT INTO users (username, email, password) VALUES (?, ?, ?)');

try {
    $stmt->execute([$username, $email, $hash]);
} catch (PDOException $e) {
    redirectWithError('signup.php', 'exists');
}

// Auto-login after signup
$_SESSION['user_id'] = $pdo->lastInsertId();
redirectWithSuccess('index.php', 'account_created');
