<?php
/**
 * Email Verification API
 * POST /api/v1/auth/verify-email
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/mail.php';
require_once __DIR__ . '/../../utils/helpers.php';
require_once __DIR__ . '/../../utils/validators.php';

// Rate limit
rateLimit(10, 60);

$data = getRequestBody();

// Validate input
$errors = validate($data, [
    'email' => 'required|email',
    'code' => 'required|min:6|max:6|regex:/^[0-9]{6}$/'
]);

if (!empty($errors)) {
    validationError($errors);
}

$db = Database::getInstance();

// Find pending registration
$pending = $db->fetchOne(
    "SELECT * FROM pending_registrations WHERE email = ? AND verification_code = ?",
    [$data['email'], $data['code']]
);

if (!$pending) {
    error('Invalid verification code');
}

// Check expiration
if (strtotime($pending['verification_expires']) < time()) {
    error('Verification code has expired. Please request a new one.');
}

try {
    $db->beginTransaction();
    
    // Create user account
    $userId = $db->insert('users', [
        'email' => $pending['email'],
        'password' => $pending['password_hash'],
        'name' => $pending['name'],
        'role' => 'alumni',
        'auth_provider' => 'email',
        'email_verified' => true,
        'verification_status' => 'pending',
        'status' => 'active'
    ]);
    
    // Final Alumni ID is generated after campus, graduation year, and college are known.
    $alumniId = null;
    
    // Parse name into first and last name
    $nameParts = explode(' ', trim($pending['name']));
    $firstName = $nameParts[0];
    $lastName = count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : null;
    
    // Create alumni profile with basic name parsing
    $db->insert('alumni_profiles', [
        'user_id' => $userId,
        'first_name' => $firstName,
        'last_name' => $lastName
    ]);
    
    // Delete pending registration
    $db->delete('pending_registrations', 'email = ?', [$pending['email']]);
    
    $db->commit();
    
    // Log security event
    logSecurityEvent('email_verified', 'Email verified successfully', $userId, $pending['email']);

    $token = JWT::generate([
        'user_id' => $userId,
        'email' => $pending['email'],
        'role' => 'alumni',
    ]);

    $userData = [
        'id' => $userId,
        'alumni_id' => $alumniId,
        'email' => $pending['email'],
        'name' => $pending['name'],
        'role' => 'alumni',
        'profile_image' => null,
        'status' => 'active',
        'verification_status' => 'pending',
        'profile_completed' => false,
    ];
    
    success([
        'requiresProfileCompletion' => true,
        'email' => $pending['email'],
        'alumniId' => $alumniId,
        'token' => $token,
        'user' => $userData,
    ], 'Email verified successfully. Please complete your profile.');
    
} catch (Exception $e) {
    $db->rollback();
    error_log("Email verification error: " . $e->getMessage());
    serverError('Verification failed. Please try again.');
}
