<?php
$pageTitle = "My Account";
require "header.php";
require "db.php";

/* Protect page */
if (!isset($_SESSION['user_id'])) {
  header("Location: Login.php");
  exit;
}

/* Fetch user data */
$stmt = $pdo->prepare(
  "SELECT username, email, created_at FROM users WHERE id = ?"
);
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<main class="account-page">

  <h1>My Account</h1>

  <!-- ACCOUNT OVERVIEW -->
  <section class="account-section">
    <h2>Account Overview</h2>
    <p><strong>Username:</strong> <?= htmlspecialchars($user['username']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
    <p><strong>Member since:</strong> <?= date("F Y", strtotime($user['created_at'])) ?></p>
  </section>

  <!-- EDIT ACCOUNT INFO -->
  <section class="account-section">
    <h2>Edit Account Information</h2>

    <form action="process_update_account.php" method="POST">
      <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

      <label>Email</label>
      <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

      <button type="submit">Update Information</button>
    </form>
  </section>

  <!-- CHANGE PASSWORD -->
  <section class="account-section">
    <h2>Change Password</h2>

    <form action="process_change_password.php" method="POST">
      <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

      <label>Current Password</label>
      <input type="password" name="current_password" required>

      <label>New Password</label>
      <input type="password" name="new_password" required>

      <button type="submit">Change Password</button>
    </form>
  </section>

  <!-- PAYMENTS (STRIPE INFO ONLY) -->
  <section class="account-section">
    <h2>Payments</h2>
    <p>Payment methods are securely handled by Stripe.</p>
    <p>You can manage payment methods during checkout.</p>
  </section>

  <!-- ORDER HISTORY (PLACEHOLDER) -->
  <section class="account-section">
    <h2>Order History</h2>
    <p>No orders yet.</p>
  </section>

</main>

<?php require "footer.php"; ?>
