<?php
$pageTitle = 'Add Product';
require 'header.php';
require 'db.php';

// Protect page - admin only
requireAdmin('index.php');
?>

<main class="admin-page">
    <div class="admin-container">
        <div class="admin-header">
            <h1>Add New Product</h1>
            <a href="admin_products.php" class="btn btn-secondary">Back to Products</a>
        </div>

        <form method="POST" action="admin_product_process.php" enctype="multipart/form-data" class="admin-form">
            <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
            <input type="hidden" name="action" value="add">

            <div class="form-group">
                <label for="name">Product Name *</label>
                <input type="text" id="name" name="name" required>
            </div>

            <div class="form-group">
                <label for="sku">SKU (Optional)</label>
                <input type="text" id="sku" name="sku" placeholder="e.g., CAN-LAV-001">
            </div>

            <div class="form-group">
                <label for="category">Category *</label>
                <select id="category" name="category" required>
                    <option value="">Select Category</option>
                    <option value="candle">Candle</option>
                    <option value="wax">Wax Melt</option>
                    <option value="oil">Essential Oil</option>
                </select>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="price">Price ($) *</label>
                    <input type="number" id="price" name="price" step="0.01" min="0" required>
                </div>

                <div class="form-group">
                    <label for="stock">Stock Quantity *</label>
                    <input type="number" id="stock" name="stock" min="0" value="0" required>
                </div>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="5"></textarea>
            </div>

            <div class="form-group">
                <label for="image">Product Image</label>
                <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/gif,image/webp">
                <small>Max 5MB. Supported: JPG, PNG, GIF, WebP</small>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="active" value="1" checked>
                    Active (visible to customers)
                </label>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Add Product</button>
                <a href="admin_products.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</main>

<?php require 'footer.php'; ?>
