<?php
$pageTitle = 'Manage Products';
require 'header.php';
require 'db.php';

// Protect page - admin only
requireAdmin('index.php');

// Get all products
$stmt = $pdo->query("
    SELECT * FROM products 
    ORDER BY created_at DESC
");
$products = $stmt->fetchAll();
?>

<main class="admin-page">
    <div class="admin-container">
        <div class="admin-header">
            <h1>Manage Products</h1>
            <a href="admin_product_add.php" class="btn btn-primary">Add New Product</a>
        </div>

        <?php if (get('success')): ?>
            <div class="message success">
                <?php if (get('success') === 'added'): ?>
                    Product added successfully!
                <?php elseif (get('success') === 'updated'): ?>
                    Product updated successfully!
                <?php elseif (get('success') === 'deleted'): ?>
                    Product deleted successfully!
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if (get('error')): ?>
            <div class="message error">
                <?php if (get('error') === 'delete_failed'): ?>
                    Failed to delete product.
                <?php else: ?>
                    An error occurred. Please try again.
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="products-table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td>
                                <?php if ($product['image_path']): ?>
                                    <img src="<?= e($product['image_path']) ?>" alt="<?= e(
    $product['name'],
) ?>" class="product-thumbnail">
                                <?php else: ?>
                                    <div class="no-image">No Image</div>
                                <?php endif; ?>
                            </td>
                            <td><?= e($product['name']) ?></td>
                            <td><?= ucfirst(e($product['category'])) ?></td>
                            <td>$<?= number_format($product['price'], 2) ?></td>
                            <td><?= $product['stock'] ?></td>
                            <td>
                                <span class="status-badge <?= $product['active']
                                    ? 'active'
                                    : 'inactive' ?>">
                                    <?= $product['active'] ? 'Active' : 'Inactive' ?>
                                </span>
                            </td>
                            <td class="actions">
                                <a href="admin_product_edit.php?id=<?= $product[
                                    'id'
                                ] ?>" class="btn-small btn-edit">Edit</a>
                                <a href="admin_product_delete.php?id=<?= $product['id'] ?>" 
                                   class="btn-small btn-delete" 
                                   onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    
                    <?php if (empty($products)): ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 40px;">
                                No products found. <a href="admin_product_add.php">Add your first product</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php require 'footer.php'; ?>
