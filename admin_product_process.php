<?php
require 'db.php';
require 'functions.php';
session_start();

// Protect page - admin only
requireAdmin('index.php');

// Validate CSRF
if (!validateCSRF()) {
    die('Invalid request');
}

$action = post('action');

if ($action === 'add') {
    // Add new product
    $name = post('name');
    $sku = post('sku');
    $category = post('category');
    $price = post('price');
    $stock = post('stock');
    $description = post('description');
    $active = isset($_POST['active']) ? 1 : 0;

    // Validate required fields
    if (!$name || !$category || !$price) {
        redirectWithError('admin_product_add.php', 'missing_fields');
    }

    // Handle image upload
    $imagePath = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imagePath = uploadProductImage($_FILES['image']);
        if (!$imagePath) {
            redirectWithError('admin_product_add.php', 'image_upload_failed');
        }
    }

    // Insert product
    $stmt = $pdo->prepare("
        INSERT INTO products (sku, name, description, price, category, image_path, stock, active)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    try {
        $stmt->execute([$sku, $name, $description, $price, $category, $imagePath, $stock, $active]);
        redirectWithSuccess('admin_products.php', 'added');
    } catch (PDOException $e) {
        if ($imagePath) {
            deleteProductImage($imagePath);
        }
        error_log('Product add failed: ' . $e->getMessage());
        redirectWithError('admin_product_add.php', 'database_error');
    }
} elseif ($action === 'edit') {
    // Edit existing product
    $id = (int) post('id');
    $name = post('name');
    $sku = post('sku');
    $category = post('category');
    $price = post('price');
    $stock = post('stock');
    $description = post('description');
    $active = isset($_POST['active']) ? 1 : 0;
    $deleteImage = isset($_POST['delete_image']);

    // Validate required fields
    if (!$id || !$name || !$category || !$price) {
        redirectWithError('admin_product_edit.php?id=' . $id, 'missing_fields');
    }

    // Get current product
    $stmt = $pdo->prepare('SELECT image_path FROM products WHERE id = ?');
    $stmt->execute([$id]);
    $currentProduct = $stmt->fetch();

    if (!$currentProduct) {
        redirectWithError('admin_products.php', 'not_found');
    }

    $imagePath = $currentProduct['image_path'];

    // Handle image deletion
    if ($deleteImage && $imagePath) {
        deleteProductImage($imagePath);
        $imagePath = null;
    }

    // Handle new image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        if ($imagePath) {
            deleteProductImage($imagePath);
        }

        $imagePath = uploadProductImage($_FILES['image']);
        if (!$imagePath) {
            redirectWithError('admin_product_edit.php?id=' . $id, 'image_upload_failed');
        }
    }

    // Update product
    $stmt = $pdo->prepare("
        UPDATE products 
        SET sku = ?, name = ?, description = ?, price = ?, category = ?, 
            image_path = ?, stock = ?, active = ?
        WHERE id = ?
    ");

    try {
        $stmt->execute([
            $sku,
            $name,
            $description,
            $price,
            $category,
            $imagePath,
            $stock,
            $active,
            $id,
        ]);
        redirectWithSuccess('admin_products.php', 'updated');
    } catch (PDOException $e) {
        error_log('Product update failed: ' . $e->getMessage());
        redirectWithError('admin_product_edit.php?id=' . $id, 'database_error');
    }
} else {
    redirectWithError('admin_products.php', 'invalid_action');
}
