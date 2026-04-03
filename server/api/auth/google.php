<?php
/**
 * Google OAuth Login API
 * POST /api/v1/auth/google
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/mail.php';
require_once __DIR__ . '/../../utils/helpers.php';
require_once __DIR__ . '/../../utils/validators.php';

// Rate limit
rateLimit(20, 60);

$data = getRequestBody();

if (empty($data['idToken'])) {
    error('Google ID token is required');
}

// Verify Google ID token
$idToken = $data['idToken'];

// Fetch Google's public keys and verify token
$googlePayload = verifyGoogleToken($idToken);

if (!$googlePayload) {
    error('Invalid Google token');
}

$googleId = $googlePayload['sub'];
$email = $googlePayload['email'];
$name = $googlePayload['name'] ?? $googlePayload['email'];
$picture = $googlePayload['picture'] ?? null;

$db = Database::getInstance();

try {
    $db->beginTransaction();
    
    // Check if user exists by Google ID or email
    $user = $db->fetchOne(
        "SELECT * FROM users WHERE google_id = ? OR email = ?",
        [$googleId, $email]
    );
    
    if ($user) {
        // Update existing user
        if (!$user['google_id']) {
            // Link Google account to existing email account
            $db->update('users', [
                'google_id' => $googleId,
                'email_verified' => true,
                'profile_image' => $picture ?: $user['profile_image']
            ], 'id = ?', [$user['id']]);
            
            logSecurityEvent('google_account_linked', 'Google account linked to existing user', $user['id'], $email);
        } else {
            // Update profile image if changed
            if ($picture && $picture !== $user['profile_image']) {
                $db->update('users', ['profile_image' => $picture], 'id = ?', [$user['id']]);
            }
        }
        
        // Check user status
        if ($user['status'] === 'blocked') {
            $db->rollback();
            error('Your account has been blocked. Please contact support.');
        }
        
        $userId = $user['id'];
        
    } else {
        // Create new user
        $userId = $db->insert('users', [
            'email' => $email,
            'name' => $name,
            'google_id' => $googleId,
            'auth_provider' => 'google',
            'profile_image' => $picture,
            'email_verified' => true,
            'role' => 'alumni',
            'status' => 'active'
        ]);
        
        // Generate alumni ID
        $alumniId = generateAlumniId('GEN');
        $db->update('users', ['alumni_id' => $alumniId], 'id = ?', [$userId]);
        
        // Create alumni profile
        $nameParts = explode(' ', $name);
        $db->insert('alumni_profiles', [
            'user_id' => $userId,
            'first_name' => $nameParts[0],
            'last_name' => count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : null
        ]);
        
        // Award first login points
        $db->insert('point_transactions', [
            'user_id' => $userId,
            'points' => POINTS_FIRST_LOGIN,
            'type' => 'earned',
            'source' => 'first_login',
            'description' => 'Welcome bonus for joining the alumni network',
            'balance_after' => POINTS_FIRST_LOGIN
        ]);
        
        $db->update('alumni_profiles', ['total_points' => POINTS_FIRST_LOGIN], 'user_id = ?', [$userId]);
        
        logSecurityEvent('google_registration', 'New user registered via Google', $userId, $email);
        
        // Send welcome email
        $emailService = new EmailService();
        $emailService->sendWelcomeEmail($email, $name, $alumniId);
    }
    
    // Update last login
    $db->update('users', ['last_login' => date('Y-m-d H:i:s')], 'id = ?', [$userId]);
    
    $db->commit();
    
    // Get updated user data
    $userData = $db->fetchOne(
        "SELECT id, alumni_id, email, name, role, profile_image, status FROM users WHERE id = ?",
        [$userId]
    );
    
    // Generate JWT token
    $token = JWT::generate([
        'user_id' => $userId,
        'email' => $userData['email'],
        'role' => $userData['role']
    ]);
    
    logSecurityEvent('google_login', 'User logged in via Google', $userId, $email);
    
    success([
        'user' => $userData,
        'token' => $token,
        'isNewUser' => !$user
    ], $user ? 'Login successful' : 'Registration successful');
    
} catch (Exception $e) {
    $db->rollback();
    error_log("Google auth error: " . $e->getMessage());
    serverError('Authentication failed. Please try again.');
}

/**
 * Verify Google ID token
 */
function verifyGoogleToken(string $idToken): ?array {
    // Option 1: Use Google's tokeninfo endpoint (simple but adds latency)
    $response = @file_get_contents(
        'https://oauth2.googleapis.com/tokeninfo?id_token=' . urlencode($idToken)
    );
    
    if ($response === false) {
        return null;
    }
    
    $payload = json_decode($response, true);
    
    if (!$payload || !isset($payload['sub'])) {
        return null;
    }
    
    // Verify audience (client ID)
    if (GOOGLE_CLIENT_ID && isset($payload['aud']) && $payload['aud'] !== GOOGLE_CLIENT_ID) {
        return null;
    }
    
    // Check expiration
    if (isset($payload['exp']) && $payload['exp'] < time()) {
        return null;
    }
    
    return $payload;
}
