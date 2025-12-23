<?php
$pageTitle = "My Account";
$breadcrumbs = ['Account' => null];
require "header.php";
require "db.php";

// Protect page
requireAuth('login.php');

// Fetch user data
$stmt = $pdo->prepare("SELECT username, email, created_at FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    redirect('logout.php');
}
?>

<main class="account-page">

  <h1>My Account</h1>

  <?php if (get('success')): ?>
    <div class="message success">
      <?php if (get('success') === 'updated'): ?>
        Account information updated successfully!
      <?php elseif (get('success') === 'password'): ?>
        Password changed successfully!
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <?php if (get('error')): ?>
    <div class="message error">
      <?php if (get('error') === 'email'): ?>
        Invalid email address.
      <?php elseif (get('error') === 'password'): ?>
        Current password is incorrect.
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <!-- ACCOUNT OVERVIEW -->
  <section class="account-section">
    <h2>Account Overview</h2>
    <p><strong>Username:</strong> <?= e($user['username']) ?></p>
    <p><strong>Email:</strong> <?= e($user['email']) ?></p>
    <p><strong>Member since:</strong> <?= date("F Y", strtotime($user['created_at'])) ?></p>
  </section>

  <!-- EDIT ACCOUNT INFO -->
  <section class="account-section">
    <h2>Edit Account Information</h2>

    <form action="process_update_account.php" method="POST">
      <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">

      <label>Email</label>
      <input type="email" name="email" value="<?= e($user['email']) ?>" required>

      <button type="submit">Update Information</button>
    </form>
  </section>

  <!-- CHANGE PASSWORD -->
  <section class="account-section">
    <h2>Change Password</h2>

    <form action="process_change_password.php" method="POST">
      <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">

      <label>Current Password</label>
      <input type="password" name="current_password" required>

      <label>New Password</label>
      <input type="password" name="new_password" required minlength="8">

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