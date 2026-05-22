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
    
    // Check if development mode is enabled (skip email verification)
    $devMode = getenv('DEV_SKIP_EMAIL_VERIFICATION') === 'true';
    
    // Log for debugging
    error_log("DEV_SKIP_EMAIL_VERIFICATION value: " . var_export(getenv('DEV_SKIP_EMAIL_VERIFICATION'), true));
    error_log("Dev mode enabled: " . var_export($devMode, true));
    
    if ($devMode) {
        // Development mode: Create user directly without email verification
        $userId = $db->insert('users', [
            'email' => $data['email'],
            'password' => Password::hash($data['password']),
            'name' => $data['name'],
            'role' => 'alumni',
            'auth_provider' => 'email',
            'email_verified' => true,
            'verification_status' => 'pending',
            'status' => 'active'
        ]);
        
        // Parse name into first and last name
        $nameParts = explode(' ', trim($data['name']));
        $firstName = $nameParts[0];
        $lastName = count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : null;
        
        // Create alumni profile
        $db->insert('alumni_profiles', [
            'user_id' => $userId,
            'first_name' => $firstName,
            'last_name' => $lastName
        ]);
        
        $db->commit();
        
        // Log security event
        logSecurityEvent('registration_completed_dev', 'Registration completed in dev mode: ' . $data['email'], $userId, $data['email']);
        
        // Generate token
        $token = JWT::generate([
            'user_id' => $userId,
            'email' => $data['email'],
            'role' => 'alumni',
        ]);
        
        $userData = [
            'id' => $userId,
            'alumni_id' => null,
            'email' => $data['email'],
            'name' => $data['name'],
            'role' => 'alumni',
            'profile_image' => null,
            'status' => 'active',
            'verification_status' => 'pending',
            'profile_completed' => false,
        ];
        
        success([
            'requiresProfileCompletion' => true,
            'requiresVerification' => false,
            'token' => $token,
            'user' => $userData,
        ], 'Registration successful! Please complete your profile.');
        
    } else {
        // Production mode: Require email verification
        // Generate verification code
        $code = generateCode(VERIFICATION_CODE_LENGTH);
        $expires = date('Y-m-d H:i:s', time() + VERIFICATION_CODE_EXPIRY);
        
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
            throw new Exception('Failed to send verification email. Please check email configuration.');
        }
        
        $db->commit();
        
        // Log security event
        logSecurityEvent('registration_initiated', 'Registration started for: ' . $data['email'], null, $data['email']);
        
        success([
            'requiresVerification' => true,
            'email' => $data['email']
        ], 'Registration initiated. Please check your email for verification code.');
    }
    
} catch (Exception $e) {
    $db->rollback();
    error_log("Registration error: " . $e->getMessage());
    serverError('Registration failed. Please try again.');
}
