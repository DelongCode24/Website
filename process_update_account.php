<?php
require "db.php";
session_start();

if (!isset($_SESSION['user_id'])) {
  exit;
}

if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
  die("Invalid request");
}

$email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
if (!$email) {
  header("Location: account.php?error=email");
  exit;
}

$stmt = $pdo->prepare(
  "UPDATE users SET email = ? WHERE id = ?"
);
$stmt->execute([$email, $_SESSION['user_id']]);

header("Location: account.php?success=updated");
exit;
