<?php
require "db.php";
session_start();

if (!isset($_SESSION['user_id'])) {
  exit;
}

if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
  die("Invalid request");
}

$current = $_POST['current_password'] ?? '';
$new = $_POST['new_password'] ?? '';

$stmt = $pdo->prepare(
  "SELECT password FROM users WHERE id = ?"
);
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user || !password_verify($current, $user['password'])) {
  header("Location: account.php?error=password");
  exit;
}

$newHash = password_hash($new, PASSWORD_DEFAULT);

$stmt = $pdo->prepare(
  "UPDATE users SET password = ? WHERE id = ?"
);
$stmt->execute([$newHash, $_SESSION['user_id']]);

header("Location: account.php?success=password");
exit;
