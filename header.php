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
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Rok World</title>
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

  <div class="site-name">Rok World LLC</div>

  <div class="header-right">
    <a href="login.php" class="login-link">Login</a>
  </div>
</header>

<hr>
