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
$email = strtolower(trim($googlePayload['email']));
$name = $googlePayload['name'] ?? $googlePayload['email'];
$picture = $googlePayload['picture'] ?? null;

// Check lockout for this email
checkLockout($email);

$db = Database::getInstance();

try {
    $db->beginTransaction();
    
    // Check if user exists by Google ID or email
    $user = $db->fetchOne(
        "SELECT * FROM users WHERE google_id = ? OR email = ?",
        [$googleId, $email]
    );
    
    $verificationStatus = null;
    $userRole = null;

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
            // Update profile image only if:
            // 1. User has no profile image yet, OR
            // 2. Current image is already from Google (refresh it)
            // This preserves custom uploaded images and doesn't overwrite them with Google picture
            $currentImage = $user['profile_image'] ?? '';
            $isCustomUpload = !empty($currentImage) && strpos($currentImage, '/uploads/') !== false;
            $isGoogleImage = !empty($currentImage) && (strpos($currentImage, 'googleusercontent') !== false || strpos($currentImage, 'lh') === 0);
            
            if ($picture && (empty($currentImage) || $isGoogleImage)) {
                $db->update('users', ['profile_image' => $picture], 'id = ?', [$user['id']]);
            }
        }
        
        // Check user status
        if ($user['status'] === 'blocked') {
            $db->rollback();
            error('Your account has been blocked. Please contact support.');
        }
        
        $userId = $user['id'];
        $verificationStatus = $user['verification_status'] ?? 'pending';
        $userRole = $user['role'] ?? 'alumni';
        
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
            'verification_status' => 'pending',
            'status' => 'active'
        ]);
        
        // Final Alumni ID is generated after campus, graduation year, and college are known.
        $alumniId = null;
        
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

        $verificationStatus = 'pending';
        $userRole = 'alumni';
        
        // Best effort: do not block Google auth if email sending fails.
        try {
            $emailService = new EmailService();
            $emailService->sendWelcomeEmail($email, $name, 'Pending profile completion');
        } catch (Throwable $mailError) {
            error_log('Google welcome email skipped: ' . $mailError->getMessage());
        }
    }
    
    $db->commit();

    if ($userRole === 'alumni' && $verificationStatus !== 'verified') {
        logSecurityEvent(
            'google_login_blocked',
            'Google login blocked pending admin approval',
            $userId,
            $email
        );
        error('Your account is awaiting admin verification.', 403, [
            'requiresApproval' => true,
            'status' => $verificationStatus,
        ]);
    }

    // Update last login
    $db->update('users', ['last_login' => date('Y-m-d H:i:s')], 'id = ?', [$userId]);
    
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
    
} catch (Throwable $e) {
    if ($db->getConnection()->inTransaction()) {
        $db->rollback();
    }
    error_log("Google auth error: " . $e->getMessage());
    serverError('Authentication failed. Please try again.');
}

/**
 * Verify Google ID token
 */
function verifyGoogleToken(string $idToken): ?array {
    // Firebase SDK returns a Firebase Auth ID token (not a raw Google OAuth token),
    // so verify against Firebase first.
    $firebasePayload = verifyFirebaseIdToken($idToken);
    if ($firebasePayload) {
        return $firebasePayload;
    }

    // Compatibility fallback for raw Google OAuth ID tokens.
    return verifyGoogleOauthIdToken($idToken);
}

/**
 * Verify Firebase Auth ID token using Firebase Identity Toolkit.
 */
function verifyFirebaseIdToken(string $idToken): ?array {
    $firebaseApiKey = getenv('FIREBASE_API_KEY') ?: '';
    if ($firebaseApiKey === '') {
        return null;
    }

    $payload = requestJson(
        'https://identitytoolkit.googleapis.com/v1/accounts:lookup?key=' . urlencode($firebaseApiKey),
        'POST',
        ['idToken' => $idToken]
    );

    if (!$payload || empty($payload['users']) || empty($payload['users'][0])) {
        return null;
    }

    $user = $payload['users'][0];
    $uid = (string)($user['localId'] ?? '');
    $email = (string)($user['email'] ?? '');

    if ($uid === '' || $email === '') {
        return null;
    }

    return [
        'sub' => $uid,
        'email' => $email,
        'name' => (string)($user['displayName'] ?? $email),
        'picture' => $user['photoUrl'] ?? null,
    ];
}

/**
 * Verify raw Google OAuth ID token via tokeninfo endpoint.
 */
function verifyGoogleOauthIdToken(string $idToken): ?array {
    $payload = requestJson(
        'https://oauth2.googleapis.com/tokeninfo?id_token=' . urlencode($idToken),
        'GET'
    );

    if (!$payload || !isset($payload['sub'])) {
        return null;
    }

    // Verify audience (OAuth Web client ID).
    if (GOOGLE_CLIENT_ID && isset($payload['aud']) && $payload['aud'] !== GOOGLE_CLIENT_ID) {
        return null;
    }

    // Check expiration.
    if (isset($payload['exp']) && (int)$payload['exp'] < time()) {
        return null;
    }

    return $payload;
}

/**
 * Lightweight JSON HTTP helper.
 */
function requestJson(string $url, string $method = 'GET', ?array $body = null): ?array {
    $method = strtoupper($method);
    $headers = "Accept: application/json\r\n";
    $content = null;

    if ($method === 'POST') {
        $headers .= "Content-Type: application/json\r\n";
        $content = json_encode($body ?? []);
    }

    $context = stream_context_create([
        'http' => [
            'method' => $method,
            'header' => $headers,
            'content' => $content,
            'timeout' => 8,
            'ignore_errors' => true,
        ],
    ]);

    $response = @file_get_contents($url, false, $context);
    if ($response === false) {
        return null;
    }

    $decoded = json_decode($response, true);
    return is_array($decoded) ? $decoded : null;
}
