<?php
$pageTitle = 'Order Details';
require 'header.php';
require 'db.php';

// Protect page - admin only
requireAdmin('index.php');

$orderId = (int) get('id');

// Get order with user info
$stmt = $pdo->prepare("
    SELECT o.*, u.username, u.email
    FROM orders o
    JOIN users u ON o.user_id = u.id
    WHERE o.id = ?
");
$stmt->execute([$orderId]);
$order = $stmt->fetch();

if (!$order) {
    redirectWithError('admin_orders.php', 'not_found');
}

// Get order items
$stmt = $pdo->prepare("
    SELECT oi.*, p.name as product_name
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ?
");
$stmt->execute([$orderId]);
$items = $stmt->fetchAll();
?>

<main class="admin-page">
    <div class="admin-container">
        <div class="admin-header">
            <h1>Order #<?= $order['id'] ?></h1>
            <div>
                <a href="admin_order_update.php?id=<?= $order[
                    'id'
                ] ?>" class="btn btn-primary">Update Status</a>
                <a href="admin_orders.php" class="btn btn-secondary">Back to Orders</a>
            </div>
        </div>

        <div class="order-details-grid">
            <!-- Customer Info -->
            <div class="detail-card">
                <h3>Customer Information</h3>
                <p><strong>Name:</strong> <?= e($order['username']) ?></p>
                <p><strong>Email:</strong> <?= e($order['email']) ?></p>
                <p><strong>Order Date:</strong> <?= date(
                    'F d, Y g:i A',
                    strtotime($order['created_at']),
                ) ?></p>
            </div>

            <!-- Order Status -->
            <div class="detail-card">
                <h3>Order Status</h3>
                <p><strong>Status:</strong> 
                    <span class="status-badge status-<?= $order['status'] ?>">
                        <?= ucfirst($order['status']) ?>
                    </span>
                </p>
                <p><strong>Total:</strong> $<?= number_format($order['total'], 2) ?></p>
                <?php if ($order['stripe_payment_id']): ?>
                    <p><strong>Payment ID:</strong> <?= e($order['stripe_payment_id']) ?></p>
                <?php endif; ?>
                <?php if ($order['tracking_number']): ?>
                    <p><strong>Tracking:</strong> <?= e($order['tracking_number']) ?></p>
                <?php endif; ?>
            </div>

            <!-- Shipping Address -->
            <?php if ($order['shipping_address']): ?>
                <div class="detail-card">
                    <h3>Shipping Address</h3><p><?= nl2br(e($order['shipping_address'])) ?></p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Order Items -->
    <div class="detail-card">
        <h3>Order Items</h3>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= e($item['product_name']) ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td>$<?= number_format($item['price'], 2) ?></td>
                        <td>$<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr class="total-row">
                    <td colspan="3" style="text-align: right;"><strong>Total:</strong></td>
                    <td><strong>$<?= number_format($order['total'], 2) ?></strong></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
</main>
<?php require 'footer.php'; ?>
