<?php
/**
 * Resend Verification Code API
 * POST /api/v1/auth/resend-verification
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

// Find pending registration
$pending = $db->fetchOne(
    "SELECT * FROM pending_registrations WHERE email = ?",
    [$data['email']]
);

if (!$pending) {
    // Check if user is already verified
    $user = $db->fetchOne("SELECT email_verified FROM users WHERE email = ?", [$data['email']]);
    
    if ($user && $user['email_verified']) {
        error('Email is already verified. Please login.');
    }
    
    error('No pending registration found for this email');
}

// Generate new code
$code = generateCode(VERIFICATION_CODE_LENGTH);
$expires = date('Y-m-d H:i:s', time() + VERIFICATION_CODE_EXPIRY);

// Update pending registration
$db->update('pending_registrations', [
    'verification_code' => $code,
    'verification_expires' => $expires
], 'email = ?', [$data['email']]);

// Send verification email
$emailService = new EmailService();
$sent = $emailService->sendVerificationEmail($data['email'], $pending['name'], $code);

if (!$sent) {
    error('Failed to send verification email. Please try again.');
}

logSecurityEvent('verification_resent', 'Verification code resent', null, $data['email']);

success([
    'email' => $data['email']
], 'Verification code sent. Please check your email.');
