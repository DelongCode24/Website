<?php
/**
 * Helper Functions
 * Reusable utility functions for the application
 */

/**
 * Redirect to a page with an error message
 *
 * @param string $location - The page to redirect to (e.g., 'login.php')
 * @param string $error - Error code (e.g., 'invalid_credentials')
 */
function redirectWithError($location, $error)
{
    header("Location: $location?error=$error");
    exit();
}

/**
 * Redirect to a page with a success message
 *
 * @param string $location - The page to redirect to
 * @param string $message - Success code (e.g., 'updated')
 */
function redirectWithSuccess($location, $message)
{
    header("Location: $location?success=$message");
    exit();
}

/**
 * Simple redirect without parameters
 *
 * @param string $location - The page to redirect to
 */
function redirect($location)
{
    header("Location: $location");
    exit();
}

/**
 * Escape output for safe HTML display
 * Shorthand for htmlspecialchars
 *
 * @param string $string - The string to escape
 * @return string - Escaped string
 */
function e($string)
{
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Check if user is logged in
 *
 * @return bool - True if user is logged in
 */
function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

/**
 * Require authentication - redirect to login if not logged in
 *
 * @param string $redirectTo - Where to redirect if not logged in (default: login.php)
 */
function requireAuth($redirectTo = 'login.php')
{
    if (!isLoggedIn()) {
        redirect($redirectTo);
    }
}

/**
 * Validate CSRF token
 *
 * @return bool - True if valid, false otherwise
 */
function validateCSRF()
{
    return isset($_POST['csrf_token']) && $_POST['csrf_token'] === ($_SESSION['csrf_token'] ?? '');
}

/**
 * Get sanitized POST data
 *
 * @param string $key - The POST key
 * @param mixed $default - Default value if key doesn't exist
 * @return mixed - Sanitized value
 */
function post($key, $default = '')
{
    return trim($_POST[$key] ?? $default);
}

/**
 * Get sanitized GET data
 *
 * @param string $key - The GET key
 * @param mixed $default - Default value if key doesn't exist
 * @return mixed - Sanitized value
 */
function get($key, $default = '')
{
    return trim($_GET[$key] ?? $default);
}

/**
 * Check if user is admin
 *
 * @return bool - True if user is admin
 */
function isAdmin()
{
    if (!isLoggedIn()) {
        return false;
    }

    global $pdo;
    $stmt = $pdo->prepare('SELECT is_admin FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    return $user && $user['is_admin'];
}

/**
 * Require admin authentication
 *
 * @param string $redirectTo - Where to redirect if not admin
 */
function requireAdmin($redirectTo = 'index.php')
{
    if (!isAdmin()) {
        redirectWithError($redirectTo, 'unauthorized');
    }
}

/**
 * Handle file upload for product images
 *
 * @param array $file - $_FILES['image']
 * @return string|false - Path to uploaded file or false on failure
 */
function uploadProductImage($file)
{
    // Validate file
    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return false;
    }

    // Check file type
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $fileType = mime_content_type($file['tmp_name']);

    if (!in_array($fileType, $allowedTypes)) {
        return false;
    }

    // Check file size (max 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        return false;
    }

    // Create uploads directory if it doesn't exist
    $uploadDir = 'uploads/products/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('product_') . '.' . $extension;
    $filepath = $uploadDir . $filename;

    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return $filepath;
    }

    return false;
}

/**
 * Delete product image file
 *
 * @param string $filepath - Path to image file
 * @return bool - True if deleted successfully
 */
function deleteProductImage($filepath)
{
    if (file_exists($filepath)) {
        return unlink($filepath);
    }
    return true; // Already deleted
}
