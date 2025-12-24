<?php
require 'db.php';
require 'functions.php';
session_start();

// Protect page - admin only
requireAdmin('index.php');

$id = (int) get('id');

if (!$id) {
    redirectWithError('admin_products.php', 'invalid_id');
}

// Get product to delete image
$stmt = $pdo->prepare('SELECT image_path FROM products WHERE id = ?');
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    redirectWithError('admin_products.php', 'not_found');
}

// Delete image file
if ($product['image_path']) {
    deleteProductImage($product['image_path']);
}

// Delete product
$stmt = $pdo->prepare('DELETE FROM products WHERE id = ?');

try {
    $stmt->execute([$id]);
    redirectWithSuccess('admin_products.php', 'deleted');
} catch (PDOException $e) {
    error_log('Product delete failed: ' . $e->getMessage());
    redirectWithError('admin_products.php', 'delete_failed');
}
