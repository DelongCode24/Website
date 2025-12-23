<?php
require "db.php";
require "functions.php";
session_start();

$token = get('token');
$tokenHash = hash('sha256', $token);

$stmt = $pdo->prepare("
  SELECT pr.user_id
  FROM password_resets pr
  WHERE pr.token = ?
    AND pr.expires_at > NOW()
");
$stmt->execute([$tokenHash]);
$reset = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$reset) {
  die("Invalid or expired reset link.");
}

$pageTitle = "Reset Password";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($pageTitle) ?> - <?= SITE_NAME ?></title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="auth-page">
  <div class="auth-container">
    <h1>Reset Password</h1>

    <form method="POST" action="process_reset_password.php" class="auth-form">
      <input type="hidden" name="token" value="<?= e($token) ?>">

      <div class="form-group">
        <label>New Password</label>
        <input type="password" name="password" required minlength="8">
        <small>Must be at least 8 characters</small>
      </div>

      <div class="form-group">
        <label>Confirm Password</label>
        <input type="password" name="confirm" required>
      </div>

      <button class="btn btn-primary">Reset Password</button>
    </form>
  </div>
</div>

</body>
</html>