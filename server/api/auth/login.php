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
    // Record failed attempt (even for non-existent users to prevent enumeration)
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
$userData = [
    'id' => $user['id'],
    'alumni_id' => $user['alumni_id'],
    'email' => $user['email'],
    'name' => $user['name'],
    'role' => $user['role'],
    'profile_image' => $user['profile_image'],
    'status' => $user['status']
];

success([
    'user' => $userData,
    'token' => $token
], 'Login successful');
