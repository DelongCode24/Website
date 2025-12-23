<?php
require "db.php";
require "functions.php";
session_start();

$token = post('token');
$password = post('password');
$confirm = post('confirm');

if ($password !== $confirm) {
  die("Passwords do not match.");
}

if (strlen($password) < 8) {
    die("Password must be at least 8 characters.");
}

$tokenHash = hash('sha256', $token);

$stmt = $pdo->prepare("
  SELECT user_id
  FROM password_resets
  WHERE token = ?
    AND expires_at > NOW()
");
$stmt->execute([$tokenHash]);
$reset = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$reset) {
  die("Invalid or expired reset token.");
}

$newHash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
$stmt->execute([$newHash, $reset['user_id']]);

// Remove used reset tokens
$stmt = $pdo->prepare("DELETE FROM password_resets WHERE user_id = ?");
$stmt->execute([$reset['user_id']]);

redirectWithSuccess('login.php', 'reset');