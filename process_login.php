<?php
require 'db.php';
require 'functions.php';
session_start();

$ipAddress = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

/* =======================
   CSRF VALIDATION
======================= */
if (!validateCSRF()) {
    redirectWithError('login.php', 'csrf');
}

/* =======================
   INPUT
======================= */
$username = post('username');
$password = post('password');

/* =======================
   HELPER FUNCTIONS
======================= */

function generateToken(): string
{
    return bin2hex(random_bytes(32));
}

function hashToken(string $token): string
{
    return hash('sha256', $token);
}

function recordLoginAttempt(PDO $pdo, string $username, string $ip): void
{
    $stmt = $pdo->prepare("
        INSERT INTO login_attempts (username, ip_address, attempts)
        VALUES (:username, :ip, 1)
        ON DUPLICATE KEY UPDATE
            attempts = attempts + 1,
            last_attempt = NOW()
    ");

    $stmt->execute([
        'username' => $username,
        'ip' => $ip,
    ]);
}

function getLoginAttempts(PDO $pdo, string $username, string $ip): int
{
    $stmt = $pdo->prepare("
        SELECT attempts
        FROM login_attempts
        WHERE username = ? AND ip_address = ?
    ");
    $stmt->execute([$username, $ip]);

    return (int) ($stmt->fetchColumn() ?? 0);
}

function clearLoginAttempts(PDO $pdo, string $username, string $ip): void
{
    $stmt = $pdo->prepare("
        DELETE FROM login_attempts
        WHERE username = ? AND ip_address = ?
    ");
    $stmt->execute([$username, $ip]);
}

function sendPasswordReset(PDO $pdo, array $user): void
{
    // Prevent multiple reset emails within 30 minutes
    $stmt = $pdo->prepare("
        SELECT 1 FROM password_resets
        WHERE user_id = ?
          AND expires_at > NOW()
        LIMIT 1
    ");
    $stmt->execute([$user['id']]);

    if ($stmt->fetch()) {
        return;
    }

    $token = generateToken();
    $tokenHash = hashToken($token);
    $expires = date('Y-m-d H:i:s', time() + 1800);

    $stmt = $pdo->prepare("
        INSERT INTO password_resets (user_id, token, expires_at)
        VALUES (?, ?, ?)
    ");
    $stmt->execute([$user['id'], $tokenHash, $expires]);

    $resetLink = SITE_URL . "/reset_password.php?token=$token";

    // TODO: Replace with proper SMTP (PHPMailer or similar) in production
    // The mail() function doesn't work on localhost XAMPP
    mail(
        $user['email'],
        'Reset your Rok World password',
        "We detected suspicious login attempts.\n\nReset your password:\n$resetLink",
    );
}

/* =======================
   USER LOOKUP
======================= */
$stmt = $pdo->prepare("
    SELECT id, username, password, email
    FROM users
    WHERE username = ?
");
$stmt->execute([$username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

/* =======================
   PASSWORD CHECK
======================= */
$hash = $user ? $user['password'] : '$2y$10$invalidinvalidinvalidinvalidinv';

if (!$user || !password_verify($password, $hash)) {
    recordLoginAttempt($pdo, $username, $ipAddress);
    $attempts = getLoginAttempts($pdo, $username, $ipAddress);

    if ($attempts >= 5 && $user && !empty($user['email'])) {
        sendPasswordReset($pdo, $user);
        redirectWithError('login.php', 'suspicious');
    } else {
        redirectWithError('login.php', 'invalid_credentials');
    }
}

/* =======================
   SUCCESSFUL LOGIN
======================= */
clearLoginAttempts($pdo, $username, $ipAddress);

session_regenerate_id(true);

$_SESSION['user_id'] = $user['id'];
$_SESSION['is_admin'] = (bool) $user['is_admin']; // ✅ REQUIRED

redirect('index.php');
exit();
