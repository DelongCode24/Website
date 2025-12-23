<?php
require "db.php";
session_start();

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

$stmt = $pdo->prepare(
  "SELECT id, password FROM users WHERE username = ?"
);
$stmt->execute([$username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || !password_verify($password, $user['password'])) {
  header("Location: Login.php?error=1");
  exit;
}

$_SESSION['user_id'] = $user['id'];
header("Location: index.php");
exit;
