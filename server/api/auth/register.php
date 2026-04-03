<?php
/**
 * User Registration API
 * POST /api/v1/auth/register
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/mail.php';
require_once __DIR__ . '/../../utils/helpers.php';
require_once __DIR__ . '/../../utils/validators.php';

// Rate limit
rateLimit(5, 60); // 5 requests per minute

// Get request data
$data = getRequestBody();

// Validate input
$errors = validate($data, [
    'name' => 'required|min:2|max:255',
    'email' => 'required|email|unique:users,email|unique:pending_registrations,email',
    'password' => 'required|password|confirmed'
]);

if (!empty($errors)) {
    validationError($errors);
}

$db = Database::getInstance();

// Generate verification code
$code = generateCode(VERIFICATION_CODE_LENGTH);
$expires = date('Y-m-d H:i:s', time() + VERIFICATION_CODE_EXPIRY);

try {
    $db->beginTransaction();
    
    // Delete any existing pending registration for this email
    $db->delete('pending_registrations', 'email = ?', [$data['email']]);
    
    // Create pending registration
    $db->insert('pending_registrations', [
        'email' => $data['email'],
        'password_hash' => Password::hash($data['password']),
        'name' => $data['name'],
        'verification_code' => $code,
        'verification_expires' => $expires
    ]);
    
    // Send verification email
    $emailService = new EmailService();
    $sent = $emailService->sendVerificationEmail($data['email'], $data['name'], $code);
    
    if (!$sent) {
        throw new Exception('Failed to send verification email');
    }
    
    $db->commit();
    
    // Log security event
    logSecurityEvent('registration_initiated', 'Registration started for: ' . $data['email'], null, $data['email']);
    
    success([
        'requiresVerification' => true,
        'email' => $data['email']
    ], 'Registration initiated. Please check your email for verification code.');
    
} catch (Exception $e) {
    $db->rollback();
    error_log("Registration error: " . $e->getMessage());
    serverError('Registration failed. Please try again.');
}
