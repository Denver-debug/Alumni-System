<?php
/**
 * Change Password API
 * POST /api/v1/auth/change-password
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../utils/helpers.php';
require_once __DIR__ . '/../../utils/validators.php';
require_once __DIR__ . '/../../middleware/auth.php';

// Rate limit
rateLimit(5, 60);

// Require authentication
$currentUser = requireAuth();

$data = getRequestBody();

$errors = validate($data, [
    'current_password' => 'required',
    'new_password' => 'required|password|confirmed'
]);

if (!empty($errors)) {
    validationError($errors);
}

$db = Database::getInstance();

// Get user with password
$user = $db->fetchOne(
    "SELECT id, password, auth_provider FROM users WHERE id = ?",
    [$currentUser['id']]
);

// Check if user uses Google auth
if ($user['auth_provider'] === 'google' && !$user['password']) {
    error('You signed up with Google. Please use Google Sign-In to manage your account.');
}

// Verify current password
if (!Password::verify($data['current_password'], $user['password'])) {
    error('Current password is incorrect');
}

// Check if new password is same as current
if (Password::verify($data['new_password'], $user['password'])) {
    error('New password must be different from current password');
}

// Update password
$db->update('users', [
    'password' => Password::hash($data['new_password'])
], 'id = ?', [$currentUser['id']]);

logSecurityEvent('password_changed', 'User changed their password', $currentUser['id'], $currentUser['email']);

success([], 'Password changed successfully');
