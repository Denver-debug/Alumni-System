<?php
/**
 * Admin Login API
 * POST /api/v1/admin/login
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../utils/helpers.php';
require_once __DIR__ . '/../../middleware/auth.php';

// Tight rate limit for admin login attempts.
rateLimit(5, 300);

try {
    $data = getRequestBody();

    $email = strtolower(trim((string)($data['email'] ?? '')));
    $password = (string)($data['password'] ?? '');

    if ($email === '' || $password === '') {
        respondError('Email and password are required', 400);
    }

    if (!validateEmail($email)) {
        respondError('Please enter a valid email address', 422);
    }

    checkLockout($email);

    $db = Database::getInstance();
    $user = $db->fetchOne(
        "SELECT * FROM users WHERE email = ? AND role IN ('admin', 'system_admin', 'campus_admin', 'staff') AND auth_provider = 'email' LIMIT 1",
        [$email]
    );

    if (!$user || !Password::verify($password, $user['password'])) {
        recordFailedLogin($email);
        logSecurityEvent('admin_login_failed', 'Failed admin login attempt', $user['id'] ?? null, $email);
        respondError('Invalid credentials', 401);
    }

    if (($user['status'] ?? '') === 'blocked') {
        logSecurityEvent('blocked_admin_login_attempt', 'Blocked admin attempted login', (int)$user['id'], $email);
        respondError('Your account has been blocked. Please contact support.', 403);
    }

    if (($user['status'] ?? '') === 'inactive') {
        respondError('Your account is inactive. Please contact support.', 403);
    }

    resetLoginAttempts((int)$user['id']);

    $token = JWT::generate([
        'user_id' => $user['id'],
        'email' => $user['email'],
        'role' => $user['role'],
    ]);

    logSecurityEvent('admin_login_success', 'Admin logged in successfully', (int)$user['id'], $email);

    // Keep activity logging non-blocking in case schema drifts.
    try {
        logAdminActivity((int)$user['id'], 'login', 'Admin logged in', 'auth', null);
    } catch (Exception $ignored) {
    }

    $userData = [
        'id' => $user['id'],
        'alumni_id' => $user['alumni_id'],
        'email' => $user['email'],
        'name' => $user['name'],
        'role' => $user['role'],
        'profile_image' => $user['profile_image'],
        'status' => $user['status'],
    ];

    respondSuccess([
        'token' => $token,
        'user' => $userData,
    ], 200, 'Login successful');
} catch (Throwable $e) {
    if (stripos($e->getMessage(), 'Database connection failed') !== false) {
        respondError('Database connection failed. Update DB_USERNAME and DB_PASSWORD in server/.env and restart the backend server.', 500);
    }

    respondError('Login failed: ' . $e->getMessage(), 500);
}
