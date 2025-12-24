<?php
$pageTitle = 'Admin Dashboard';
require 'header.php';
require 'db.php';

requireAdmin('index.php');

$stmt = $pdo->query('SELECT COUNT(*) FROM products WHERE active = TRUE');
$activeProducts = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'");
$pendingOrders = $stmt->fetchColumn();

$stmt = $pdo->query('SELECT COUNT(*) FROM users');
$totalUsers = $stmt->fetchColumn();

$stmt = $pdo->query(
    "SELECT SUM(total) FROM orders WHERE status != 'cancelled' AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)",
);
$monthlyRevenue = $stmt->fetchColumn() ?? 0;
?>

<main class="admin-dashboard">
    <div class="admin-container">
        <h1>Admin Dashboard</h1>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Active Products</h3>
                <p class="stat-number"><?= $activeProducts ?></p>
            </div>
            <div class="stat-card">
                <h3>Pending Orders</h3>
                <p class="stat-number"><?= $pendingOrders ?></p>
            </div>
            <div class="stat-card">
                <h3>Total Users</h3>
                <p class="stat-number"><?= $totalUsers ?></p>
            </div>
            <div class="stat-card">
                <h3>Monthly Revenue</h3>
                <p class="stat-number">$<?= number_format($monthlyRevenue, 2) ?></p>
            </div>
        </div>

        <div class="admin-nav">
            <a href="admin_products.php" class="btn btn-primary">Manage Products</a>
            <a href="admin_orders.php" class="btn btn-primary">View Orders</a>
            <a href="admin_users.php" class="btn btn-secondary">Manage Users</a>
        </div>
    </div>
</main>

<?php require 'footer.php'; ?>
