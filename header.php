<?php
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.use_strict_mode', 1);

if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
    ini_set('session.cookie_secure', 1);
}

session_start();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Load config and functions
require_once 'config.php';
require_once 'functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="<?= SITE_DESCRIPTION ?>">
  <title><?= isset($pageTitle) ? e($pageTitle) . ' - ' . SITE_NAME : SITE_NAME ?></title>
  <link rel="icon" href="favicon.ico">
  <link rel="stylesheet" href="styles.css">
  <script src="script.js" defer></script>
</head>
<body>

<header class="header">
  <div class="header-left">
    <nav class="navbar">
      <button class="hamburger" aria-label="Menu">☰</button>
      <ul class="menu">
        <li><a href="index.php">Home</a></li>
        <li><a href="candles.php">Candles</a></li>
        <li><a href="wax.php">Wax</a></li>
        <li><a href="oils.php">Oils</a></li>
        <li><a href="contact.php">Contact</a></li>
        <li><a href="reviews.php">Reviews</a></li>
      </ul>
    </nav>
  </div>

  <div class="site-name"><?= SITE_NAME ?></div>

  <div class="header-right">
    <?php if (isLoggedIn()): ?>
      <a href="account.php" class="login-link">Account</a>
      <span class="header-divider">|</span>
      <a href="logout.php" class="login-link">Logout</a>
    <?php else: ?>
      <a href="login.php" class="login-link">Login</a>
    <?php endif; ?>
  </div>
</header>

<hr>