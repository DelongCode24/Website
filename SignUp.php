<?php
$pageTitle = 'Sign Up';
require 'header.php';
?>

<div class="auth-page">
  <div class="auth-container">
    <h1>Create Account</h1>
    <p class="auth-subtitle">Join Rok World today</p>

    <?php if (get('error')): ?>
      <div class="message error">
        <?php if (get('error') === 'exists'): ?>
          Username or email already exists.
        <?php elseif (get('error') === 'password_short'): ?>
          Password must be at least 8 characters.
        <?php else: ?>
          Please fill in all fields.
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <form class="auth-form" method="POST" action="process_signup.php">
      <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">

      <div class="form-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required autofocus>
      </div>

      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required minlength="8">
        <small>Must be at least 8 characters</small>
      </div>

      <button type="submit" class="btn btn-primary">Sign Up</button>
    </form>

    <div class="auth-links">
      <p>Already have an account? <a href="login.php" class="link-primary">Log in</a></p>
    </div>
  </div>
</div>

</body>
</html>