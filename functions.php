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
function redirectWithError($location, $error) {
    header("Location: $location?error=$error");
    exit;
}

/**
 * Redirect to a page with a success message
 * 
 * @param string $location - The page to redirect to
 * @param string $message - Success code (e.g., 'updated')
 */
function redirectWithSuccess($location, $message) {
    header("Location: $location?success=$message");
    exit;
}

/**
 * Simple redirect without parameters
 * 
 * @param string $location - The page to redirect to
 */
function redirect($location) {
    header("Location: $location");
    exit;
}

/**
 * Escape output for safe HTML display
 * Shorthand for htmlspecialchars
 * 
 * @param string $string - The string to escape
 * @return string - Escaped string
 */
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Check if user is logged in
 * 
 * @return bool - True if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Require authentication - redirect to login if not logged in
 * 
 * @param string $redirectTo - Where to redirect if not logged in (default: login.php)
 */
function requireAuth($redirectTo = 'login.php') {
    if (!isLoggedIn()) {
        redirect($redirectTo);
    }
}

/**
 * Validate CSRF token
 * 
 * @return bool - True if valid, false otherwise
 */
function validateCSRF() {
    return isset($_POST['csrf_token']) && 
           $_POST['csrf_token'] === ($_SESSION['csrf_token'] ?? '');
}

/**
 * Get sanitized POST data
 * 
 * @param string $key - The POST key
 * @param mixed $default - Default value if key doesn't exist
 * @return mixed - Sanitized value
 */
function post($key, $default = '') {
    return trim($_POST[$key] ?? $default);
}

/**
 * Get sanitized GET data
 * 
 * @param string $key - The GET key
 * @param mixed $default - Default value if key doesn't exist
 * @return mixed - Sanitized value
 */
function get($key, $default = '') {
    return trim($_GET[$key] ?? $default);
}