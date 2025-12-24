<?php
$pageTitle = 'Update Order';
require 'header.php';
require 'db.php';

// Protect page - admin only
requireAdmin('index.php');

$orderId = (int) get('id');

// Get order
$stmt = $pdo->prepare('SELECT * FROM orders WHERE id = ?');
$stmt->execute([$orderId]);
$order = $stmt->fetch();

if (!$order) {
    redirectWithError('admin_orders.php', 'not_found');
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && validateCSRF()) {
    $status = post('status');
    $trackingNumber = post('tracking_number');

    $stmt = $pdo->prepare("
        UPDATE orders
        SET status = ?, tracking_number = ?
        WHERE id = ?
    ");

    try {
        $stmt->execute([$status, $trackingNumber, $orderId]);
        redirectWithSuccess('admin_order_view.php?id=' . $orderId, 'updated');
    } catch (PDOException $e) {
        $error = 'Failed to update order.';
    }
}
?>

<main class="admin-page">
    <div class="admin-container">
        <div class="admin-header">
            <h1>Update Order #<?= $order['id'] ?></h1>
            <a href="admin_order_view.php?id=<?= $order['id'] ?>"
               class="btn btn-secondary">
                Back to Order
            </a>
        </div>

        <?php if (isset($error)): ?>
            <div class="message error">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="admin-form">
            <input type="hidden"
                   name="csrf_token"
                   value="<?= e($_SESSION['csrf_token']) ?>">

            <div class="form-group">
                <label for="status">Order Status *</label>
                <select id="status" name="status" required>
                    <option value="pending" <?= $order['status'] === 'pending'
                        ? 'selected'
                        : '' ?>>Pending</option>
                    <option value="processing" <?= $order['status'] === 'processing'
                        ? 'selected'
                        : '' ?>>Processing</option>
                    <option value="shipped" <?= $order['status'] === 'shipped'
                        ? 'selected'
                        : '' ?>>Shipped</option>
                    <option value="delivered" <?= $order['status'] === 'delivered'
                        ? 'selected'
                        : '' ?>>Delivered</option>
                    <option value="cancelled" <?= $order['status'] === 'cancelled'
                        ? 'selected'
                        : '' ?>>Cancelled</option>
                </select>
            </div>

            <div class="form-group">
                <label for="tracking_number">
                    Tracking Number (Optional)
                </label>
                <input type="text"
                       id="tracking_number"
                       name="tracking_number"
                       value="<?= e($order['tracking_number']) ?>"
                       placeholder="e.g., 1Z999AA10123456784">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    Update Order
                </button>
                <a href="admin_order_view.php?id=<?= $order['id'] ?>"
                   class="btn btn-secondary">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</main>

<?php require 'footer.php'; ?>
