<?php
/**
 * Reset Password API
 * POST /api/v1/auth/reset-password
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../utils/helpers.php';
require_once __DIR__ . '/../../utils/validators.php';

// Rate limit
rateLimit(5, 60);

$data = getRequestBody();

$errors = validate($data, [
    'email' => 'required|email',
    'code' => 'required|min:6|max:6',
    'password' => 'required|password|confirmed'
]);

if (!empty($errors)) {
    validationError($errors);
}

$db = Database::getInstance();

// Find user with reset code
$user = $db->fetchOne(
    "SELECT * FROM users WHERE email = ? AND reset_code = ?",
    [$data['email'], $data['code']]
);

if (!$user) {
    error('Invalid reset code');
}

// Check expiration
if (!$user['reset_expires'] || strtotime($user['reset_expires']) < time()) {
    error('Reset code has expired. Please request a new one.');
}

// Update password
$db->update('users', [
    'password' => Password::hash($data['password']),
    'reset_code' => null,
    'reset_expires' => null,
    'login_attempts' => 0,
    'locked_until' => null
], 'id = ?', [$user['id']]);

logSecurityEvent('password_reset', 'Password reset successfully', $user['id'], $user['email']);

success([], 'Password reset successfully. You can now login with your new password.');
