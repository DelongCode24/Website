<?php
require "db.php";
session_start();
if (
  !isset($_POST['csrf_token']) ||
  $_POST['csrf_token'] !== $_SESSION['csrf_token']
) {
  die("Invalid request");
}

$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (!$username || !$email || !$password) {
  header("Location: SignUp.php?error=1");
  exit;
}

$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare(
  "INSERT INTO users (username, email, password) VALUES (?, ?, ?)"
);

try {
  $stmt->execute([$username, $email, $hash]);
} catch (PDOException $e) {
  header("Location: SignUp.php?error=exists");
  exit;
}

$_SESSION['user_id'] = $pdo->lastInsertId();
header("Location: index.php");
exit;
