<?php
$pageTitle = 'Manage Orders';
require 'header.php';
require 'db.php';

// Protect page - admin only
requireAdmin('index.php');

// Get filter
$showAll = isset($_GET['show_all']);

// Build query
if ($showAll) {
    $query = "
        SELECT o.*, u.username, u.email
        FROM orders o
        JOIN users u ON o.user_id = u.id
        ORDER BY o.created_at DESC
    ";
} else {
    $query = "
        SELECT o.*, u.username, u.email
        FROM orders o
        JOIN users u ON o.user_id = u.id
        WHERE o.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        ORDER BY o.created_at DESC
    ";
}

$stmt = $pdo->query($query);
$orders = $stmt->fetchAll();

// Get count of older orders
$stmt = $pdo->query("
    SELECT COUNT(*) 
    FROM orders 
    WHERE created_at < DATE_SUB(NOW(), INTERVAL 30 DAY)
");
$olderOrdersCount = $stmt->fetchColumn();
?>

<main class="admin-page">
    <div class="admin-container">
        <div class="admin-header">
            <h1>Manage Orders</h1>
            <a href="admin.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>

        <div class="orders-filter">
            <?php if ($showAll): ?>
                <p>Showing all orders</p>
                <a href="admin_orders.php" class="btn btn-secondary">Show Last 30 Days</a>
            <?php else: ?>
                <p>Showing orders from last 30 days</p>
                <?php if ($olderOrdersCount > 0): ?>
                    <a href="admin_orders.php?show_all=1" class="btn btn-secondary">
                        Show All Orders (<?= $olderOrdersCount ?> older)
                    </a>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <div class="orders-table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>#<?= $order['id'] ?></td>
                            <td>
                                <strong><?= e($order['username']) ?></strong><br>
                                <small><?= e($order['email']) ?></small>
                            </td>
                            <td><?= date('M d, Y g:i A', strtotime($order['created_at'])) ?></td>
                            <td>$<?= number_format($order['total'], 2) ?></td>
                            <td>
                                <span class="status-badge status-<?= $order['status'] ?>">
                                    <?= ucfirst($order['status']) ?>
                                </span>
                            </td>
                            <td class="actions">
                                <a href="admin_order_view.php?id=<?= $order[
                                    'id'
                                ] ?>" class="btn-small btn-view">View</a>
                                <a href="admin_order_update.php?id=<?= $order[
                                    'id'
                                ] ?>" class="btn-small btn-edit">Update</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 40px;">
                                No orders found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if (!empty($orders)): ?>
            <?php
            $totalRevenue = array_sum(array_column($orders, 'total'));
            $pendingCount = count(array_filter($orders, fn($o) => $o['status'] === 'pending'));
            ?>
            <div class="orders-summary">
                <div class="summary-item">
                    <strong>Total Orders:</strong> <?= count($orders) ?>
                </div>
                <div class="summary-item">
                    <strong>Pending:</strong> <?= $pendingCount ?>
                </div>
                <div class="summary-item">
                    <strong>Total Revenue:</strong> $<?= number_format($totalRevenue, 2) ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php require 'footer.php'; ?>
