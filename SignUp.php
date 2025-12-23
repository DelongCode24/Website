<?php
$pageTitle = "Sign Up";
require "header.php";
?>

<main>
  <h1>Create Account</h1>

  <form action="process_signup.php" method="POST">
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
    <input type="text" name="username" placeholder="Username" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>

    <button type="submit">Sign Up</button>
  </form>
</main>

<?php require "footer.php"; ?>
