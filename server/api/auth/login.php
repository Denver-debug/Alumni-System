<?php
/**
 * User Login API
 * POST /api/v1/auth/login
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../utils/helpers.php';
require_once __DIR__ . '/../../utils/validators.php';
require_once __DIR__ . '/../../middleware/auth.php';

try {
    // Rate limit
    rateLimit(10, 60);

    $data = getRequestBody();

    // Validate input
    $errors = validate($data, [
        'email' => 'required|email',
        'password' => 'required'
    ]);

    if (!empty($errors)) {
        validationError($errors);
    }

    // Check lockout
    checkLockout($data['email']);

    $db = Database::getInstance();

    // Find user
    $user = $db->fetchOne(
        "SELECT * FROM users WHERE email = ? AND auth_provider = 'email'",
        [$data['email']]
    );

    if (!$user) {
        // Check if user exists with different auth provider
        $userAnyProvider = $db->fetchOne(
            "SELECT id, email, auth_provider FROM users WHERE email = ?",
            [$data['email']]
        );
        
        if ($userAnyProvider && $userAnyProvider['auth_provider'] !== 'email') {
            // User exists but uses different auth provider (e.g., Google)
            recordFailedLogin($data['email']);
            error('This account uses ' . ucfirst($userAnyProvider['auth_provider']) . ' sign-in. Please use the "Sign in with ' . ucfirst($userAnyProvider['auth_provider']) . '" button.');
        }
        
        // Record failed attempt (even for non-existent users to prevent enumeration)
        recordFailedLogin($data['email']);
        logSecurityEvent('failed_login', 'Login attempt for non-existent user', null, $data['email']);
        error('Invalid email or password');
    }

    // Check password
    if (!Password::verify($data['password'], $user['password'])) {
        recordFailedLogin($data['email']);
        error('Invalid email or password');
    }

    // Check user status
    if ($user['status'] === 'blocked') {
        logSecurityEvent('blocked_login_attempt', 'Blocked user attempted login', $user['id'], $data['email']);
        error('Your account has been blocked. Please contact support.');
    }

    if ($user['status'] === 'inactive') {
        error('Your account is inactive. Please contact support.');
    }

    // Check email verification
    if (!$user['email_verified']) {
        // Check if there's a pending registration
        $pending = $db->fetchOne(
            "SELECT * FROM pending_registrations WHERE email = ?",
            [$data['email']]
        );
        
        if ($pending) {
            error('Please verify your email before logging in.', 403, [
                'requiresVerification' => true,
                'email' => $data['email']
            ]);
        }
        
        error('Email not verified. Please verify your email.');
    }

    // Check admin approval (alumni only)
    if ($user['role'] === 'alumni') {
        $verificationStatus = $user['verification_status'] ?? 'pending';
        if ($verificationStatus !== 'verified') {
            $message = $verificationStatus === 'rejected'
                ? 'Your account was not approved. Please contact support.'
                : 'Your account is awaiting admin verification.';
            error($message, 403, [
                'requiresApproval' => true,
                'status' => $verificationStatus,
            ]);
        }
    }

    // Reset login attempts on successful login
    resetLoginAttempts($user['id']);

    // Generate JWT token
    $token = JWT::generate([
        'user_id' => $user['id'],
        'email' => $user['email'],
        'role' => $user['role']
    ]);

    // Log security event
    logSecurityEvent('successful_login', 'User logged in successfully', $user['id'], $data['email']);

    // Return user data (without sensitive info)
    $userData = processUserData([
        'id' => $user['id'],
        'alumni_id' => $user['alumni_id'],
        'email' => $user['email'],
        'name' => $user['name'],
        'role' => $user['role'],
        'profile_image' => $user['profile_image'],
        'status' => $user['status'],
        'verification_status' => $user['verification_status'] ?? null,
    ]);

    success([
        'user' => $userData,
        'token' => $token
    ], 'Login successful');
} catch (Throwable $e) {
    if (stripos($e->getMessage(), 'Database connection failed') !== false) {
        error('Database connection failed. Update DB_USERNAME and DB_PASSWORD in server/.env and restart the backend server.', 500);
    }

    error('Login failed: ' . $e->getMessage(), 500);
}
