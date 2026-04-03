<?php
/**
 * Forgot Password API
 * POST /api/v1/auth/forgot-password
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/mail.php';
require_once __DIR__ . '/../../utils/helpers.php';
require_once __DIR__ . '/../../utils/validators.php';

// Rate limit (strict)
rateLimit(3, 300); // 3 requests per 5 minutes

$data = getRequestBody();

$errors = validate($data, [
    'email' => 'required|email'
]);

if (!empty($errors)) {
    validationError($errors);
}

$db = Database::getInstance();

// Find user
$user = $db->fetchOne(
    "SELECT id, name, email, auth_provider FROM users WHERE email = ?",
    [$data['email']]
);

// Always return success to prevent email enumeration
if (!$user) {
    success([], 'If an account exists with this email, you will receive a password reset code.');
}

// Check if user used Google to register
if ($user['auth_provider'] === 'google') {
    error('This account uses Google Sign-In. Please login with Google.');
}

// Generate reset code
$code = generateCode(VERIFICATION_CODE_LENGTH);
$expires = date('Y-m-d H:i:s', time() + VERIFICATION_CODE_EXPIRY);

// Update user with reset code
$db->update('users', [
    'reset_code' => $code,
    'reset_expires' => $expires
], 'id = ?', [$user['id']]);

// Send reset email
$emailService = new EmailService();
$sent = $emailService->sendPasswordResetEmail($user['email'], $user['name'], $code);

logSecurityEvent('password_reset_requested', 'Password reset requested', $user['id'], $user['email']);

success([], 'If an account exists with this email, you will receive a password reset code.');
