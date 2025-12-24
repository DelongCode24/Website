<?php
$pageTitle = 'Edit Product';
require 'header.php';
require 'db.php';

// Protect page - admin only
requireAdmin('index.php');

// Get product
$productId = (int) get('id');
$stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
$stmt->execute([$productId]);
$product = $stmt->fetch();

if (!$product) {
    redirectWithError('admin_products.php', 'not_found');
}
?>

<main class="admin-page">
    <div class="admin-container">
        <div class="admin-header">
            <h1>Edit Product</h1>
            <a href="admin_products.php" class="btn btn-secondary">Back to Products</a>
        </div>

        <form method="POST" action="admin_product_process.php" enctype="multipart/form-data" class="admin-form">
            <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" value="<?= $product['id'] ?>">

            <div class="form-group">
                <label for="name">Product Name *</label>
                <input type="text" id="name" name="name" value="<?= e(
                    $product['name'],
                ) ?>" required>
            </div>

            <div class="form-group">
                <label for="sku">SKU (Optional)</label>
                <input type="text" id="sku" name="sku" value="<?= e(
                    $product['sku'],
                ) ?>" placeholder="e.g., CAN-LAV-001">
            </div>

            <div class="form-group">
                <label for="category">Category *</label>
                <select id="category" name="category" required>
                    <option value="">Select Category</option>
                    <option value="candle" <?= $product['category'] === 'candle'
                        ? 'selected'
                        : '' ?>>Candle</option>
                    <option value="wax" <?= $product['category'] === 'wax'
                        ? 'selected'
                        : '' ?>>Wax Melt</option>
                    <option value="oil" <?= $product['category'] === 'oil'
                        ? 'selected'
                        : '' ?>>Essential Oil</option>
                </select>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="price">Price ($) *</label>
                    <input type="number" id="price" name="price" step="0.01" min="0" value="<?= $product[
                        'price'
                    ] ?>" required>
                </div>

                <div class="form-group">
                    <label for="stock">Stock Quantity *</label>
                    <input type="number" id="stock" name="stock" min="0" value="<?= $product[
                        'stock'
                    ] ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="5"><?= e(
                    $product['description'],
                ) ?></textarea>
            </div>

            <div class="form-group">
                <label>Current Image</label>
                <?php if ($product['image_path']): ?>
                    <div class="current-image">
                        <img src="<?= e($product['image_path']) ?>" alt="<?= e(
    $product['name'],
) ?>">
                        <label>
                            <input type="checkbox" name="delete_image" value="1">
                            Delete this image
                        </label>
                    </div>
                <?php else: ?>
                    <p>No image uploaded</p>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="image">Upload New Image (optional)</label>
                <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/gif,image/webp">
                <small>Max 5MB. Supported: JPG, PNG, GIF, WebP</small>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="active" value="1" <?= $product['active']
                        ? 'checked'
                        : '' ?>>
                    Active (visible to customers)
                </label>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Update Product</button>
                <a href="admin_products.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</main>

<?php require 'footer.php'; ?>
