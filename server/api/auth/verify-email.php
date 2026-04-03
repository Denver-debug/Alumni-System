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
    'code' => 'required|min:6|max:6'
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
        'status' => 'active'
    ]);
    
    // Generate alumni ID (default college code until profile is completed)
    $alumniId = generateAlumniId('GEN');
    $db->update('users', ['alumni_id' => $alumniId], 'id = ?', [$userId]);
    
    // Create alumni profile
    $db->insert('alumni_profiles', [
        'user_id' => $userId,
        'first_name' => explode(' ', $pending['name'])[0],
        'last_name' => implode(' ', array_slice(explode(' ', $pending['name']), 1)) ?: null
    ]);
    
    // Delete pending registration
    $db->delete('pending_registrations', 'email = ?', [$pending['email']]);
    
    // Award first login points
    $db->insert('point_transactions', [
        'user_id' => $userId,
        'points' => POINTS_FIRST_LOGIN,
        'type' => 'earned',
        'source' => 'first_login',
        'description' => 'Welcome bonus for joining the alumni network',
        'balance_after' => POINTS_FIRST_LOGIN
    ]);
    
    // Update total points
    $db->update('alumni_profiles', ['total_points' => POINTS_FIRST_LOGIN], 'user_id = ?', [$userId]);
    
    $db->commit();
    
    // Generate JWT token
    $token = JWT::generate([
        'user_id' => $userId,
        'email' => $pending['email'],
        'role' => 'alumni'
    ]);
    
    // Get user data
    $user = $db->fetchOne("SELECT id, alumni_id, email, name, role, profile_image, status FROM users WHERE id = ?", [$userId]);
    
    // Log security event
    logSecurityEvent('email_verified', 'Email verified successfully', $userId, $pending['email']);
    
    // Send welcome email
    $emailService = new EmailService();
    $emailService->sendWelcomeEmail($pending['email'], $pending['name'], $alumniId);
    
    success([
        'user' => $user,
        'token' => $token,
        'alumniId' => $alumniId
    ], 'Email verified successfully. Welcome to the alumni network!');
    
} catch (Exception $e) {
    $db->rollback();
    error_log("Email verification error: " . $e->getMessage());
    serverError('Verification failed. Please try again.');
}
