<?php 
$pageTitle = "Login";
require "header.php"; 
?>

<div class="auth-page">
  <div class="auth-container">

    <h1>Welcome back</h1>
    <p class="auth-subtitle">Log in to your account</p>

    <?php if (get('error')): ?>
      <div class="message error">
        <?php if (get('error') === 'csrf'): ?>
          Your session expired. Please try logging in again.
        <?php elseif (get('error') === 'suspicious'): ?>
          Too many failed login attempts. Check your email for a password reset link.
        <?php else: ?>
          Invalid username or password.
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <?php if (get('reset')): ?>
      <div class="message success">
        Password reset successful. You may now log in.
      </div>
    <?php endif; ?>

    <form class="auth-form" method="POST" action="process_login.php">

      <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">

      <div class="form-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required autofocus>
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
      </div>

      <button type="submit" class="btn btn-primary">Login</button>
    </form>

    <div class="auth-links">
      <p>Don't have an account?
        <a href="signup.php" class="link-primary">Sign up</a>
      </p>
      <a href="index.php" class="link-secondary">Continue as guest</a>
    </div>

  </div>
</div>

</body>
</html>