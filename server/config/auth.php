<?php
/**
 * JWT Authentication Configuration
 * Alumni Management System
 */

require_once __DIR__ . '/database.php';

// JWT Configuration
define('JWT_SECRET', getenv('JWT_SECRET') ?: 'your-super-secret-jwt-key-change-in-production');

// Supports JWT_EXPIRY values like 30, 30d, 12h, 45m, or 3600.
$jwtExpiryRaw = getenv('JWT_EXPIRY');
$jwtExpirySeconds = 86400 * 30;

if (is_string($jwtExpiryRaw) && trim($jwtExpiryRaw) !== '') {
    $jwtExpiryValue = strtolower(trim($jwtExpiryRaw));

    if (preg_match('/^(\d+)\s*([smhd]?)$/', $jwtExpiryValue, $matches)) {
        $amount = (int)$matches[1];
        $unit = $matches[2] ?? '';

        if ($unit === 'm') {
            $jwtExpirySeconds = $amount * 60;
        } elseif ($unit === 'h') {
            $jwtExpirySeconds = $amount * 3600;
        } elseif ($unit === 'd') {
            $jwtExpirySeconds = $amount * 86400;
        } elseif ($unit === 's') {
            $jwtExpirySeconds = $amount;
        } else {
            // Backward compatibility: plain small numbers are treated as days.
            $jwtExpirySeconds = $amount <= 365 ? $amount * 86400 : $amount;
        }
    }
}

define('JWT_EXPIRY', max(60, $jwtExpirySeconds));
define('JWT_ALGORITHM', 'HS256');

/**
 * JWT Token Handler
 */
class JWT {
    
    /**
     * Base64 URL encode
     */
    private static function base64UrlEncode(string $data): string {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
    
    /**
     * Base64 URL decode
     */
    private static function base64UrlDecode(string $data): string {
        return base64_decode(strtr($data, '-_', '+/') . str_repeat('=', 3 - (3 + strlen($data)) % 4));
    }
    
    /**
     * Generate JWT token
     */
    public static function generate(array $payload): string {
        $header = [
            'typ' => 'JWT',
            'alg' => JWT_ALGORITHM
        ];
        
        // Add standard claims
        $payload['iat'] = time();
        $payload['exp'] = time() + JWT_EXPIRY;
        
        $headerEncoded = self::base64UrlEncode(json_encode($header));
        $payloadEncoded = self::base64UrlEncode(json_encode($payload));
        
        $signature = hash_hmac('sha256', "$headerEncoded.$payloadEncoded", JWT_SECRET, true);
        $signatureEncoded = self::base64UrlEncode($signature);
        
        return "$headerEncoded.$payloadEncoded.$signatureEncoded";
    }
    
    /**
     * Decode JWT token and optionally ignore expiry validation.
     */
    public static function decode(string $token, bool $ignoreExpiry = false): ?array {
        $parts = explode('.', $token);
        
        if (count($parts) !== 3) {
            return null;
        }
        
        list($headerEncoded, $payloadEncoded, $signatureEncoded) = $parts;
        
        // Verify signature
        $signature = self::base64UrlDecode($signatureEncoded);
        $expectedSignature = hash_hmac('sha256', "$headerEncoded.$payloadEncoded", JWT_SECRET, true);
        
        if (!hash_equals($signature, $expectedSignature)) {
            return null;
        }
        
        // Decode payload
        $payload = json_decode(self::base64UrlDecode($payloadEncoded), true);
        
        if (!$payload) {
            return null;
        }
        
        // Check expiration
        if (!$ignoreExpiry && isset($payload['exp']) && $payload['exp'] < time()) {
            return null;
        }
        
        return $payload;
    }

    /**
     * Verify and decode JWT token
     */
    public static function verify(string $token): ?array {
        return self::decode($token, false);
    }
    
    /**
     * Get token from Authorization header
     */
    public static function getTokenFromHeader(): ?string {
        $authHeader = $_SERVER['HTTP_AUTHORIZATION']
            ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION']
            ?? '';

        if (!$authHeader && function_exists('getallheaders')) {
            $headers = getallheaders();
            $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';
        }

        if (!$authHeader && function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
            $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';
        }
        
        if (preg_match('/Bearer\s+(.+)$/i', $authHeader, $matches)) {
            return $matches[1];
        }
        
        return null;
    }
    
    /**
     * Refresh token (generate new with same payload)
     */
    public static function refresh(string $token, bool $allowExpired = false): ?string {
        $payload = self::decode($token, $allowExpired);
        
        if (!$payload) {
            return null;
        }

        // Reject very old expired tokens when refreshing with expiry bypass.
        if (
            $allowExpired &&
            isset($payload['exp']) &&
            $payload['exp'] < (time() - (86400 * 14))
        ) {
            return null;
        }
        
        // Remove old timestamps
        unset($payload['iat'], $payload['exp']);
        
        return self::generate($payload);
    }
}

/**
 * Password utilities
 */
class Password {
    
    /**
     * Hash password
     */
    public static function hash(string $password): string {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }
    
    /**
     * Verify password
     */
    public static function verify(string $password, string $hash): bool {
        return password_verify($password, $hash);
    }
    
    /**
     * Check if password needs rehash
     */
    public static function needsRehash(string $hash): bool {
        return password_needs_rehash($hash, PASSWORD_BCRYPT, ['cost' => 12]);
    }
    
    /**
     * Validate password strength
     */
    public static function validate(string $password): array {
        $errors = [];
        
        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters long';
        }
        
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password must contain at least one uppercase letter';
        }
        
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Password must contain at least one lowercase letter';
        }
        
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password must contain at least one number';
        }
        
        if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
            $errors[] = 'Password must contain at least one special character';
        }
        
        return $errors;
    }
}

/**
 * Generate verification/reset code
 */
function generateCode(int $length = 6): string {
    return str_pad((string) random_int(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
}

/**
 * Resolve Alumni ID prefix from settings with constant fallback.
 */
function getAlumniIdPrefix(): string {
    $fallback = defined('ALUMNI_ID_PREFIX') ? ALUMNI_ID_PREFIX : 'ALM';
    $fallback = strtoupper(trim((string)$fallback));
    if (!preg_match('/^[A-Z]{3}$/', $fallback)) {
        $fallback = 'ALM';
    }

    try {
        $db = Database::getInstance();
        $setting = $db->fetchOne(
            "SELECT content_value FROM site_content WHERE section = ? AND content_key = ? LIMIT 1",
            ['settings', 'alumni_id_prefix']
        );

        $prefix = strtoupper(trim((string)($setting['content_value'] ?? '')));
        if (preg_match('/^[A-Z]{3}$/', $prefix)) {
            return $prefix;
        }
    } catch (Throwable $e) {
        error_log('Alumni ID prefix lookup fallback: ' . $e->getMessage());
    }

    return $fallback;
}

/**
 * Generate legacy Alumni ID format used by older auth flows.
 *
 * The current profile-aware generator lives in utils/helpers.php as
 * generateAlumniId($db, $campusId, $collegeId, $graduationYear).
 */
function generateLegacyAlumniId(string $collegeCode): string {
    $db = Database::getInstance();
    $yearFormat = defined('ALUMNI_ID_YEAR_FORMAT') ? ALUMNI_ID_YEAR_FORMAT : 'Y';
    $year = date($yearFormat);
    $prefix = getAlumniIdPrefix();
    $sequenceLength = defined('ALUMNI_ID_SEQUENCE_LENGTH') ? (int)ALUMNI_ID_SEQUENCE_LENGTH : 5;
    if ($sequenceLength < 1) {
        $sequenceLength = 5;
    }
    
    // Get or create sequence
    $sequence = $db->fetchOne(
        "SELECT * FROM alumni_id_sequences WHERE year = ? AND college_code = ?",
        [$year, $collegeCode]
    );
    
    if ($sequence) {
        $newSeq = $sequence['last_sequence'] + 1;
        $db->update(
            'alumni_id_sequences',
            ['last_sequence' => $newSeq],
            'id = ?',
            [$sequence['id']]
        );
    } else {
        $newSeq = 1;
        $db->insert('alumni_id_sequences', [
            'year' => $year,
            'college_code' => $collegeCode,
            'last_sequence' => $newSeq
        ]);
    }
    
    return sprintf(
        '%s-%s-%s-%0' . $sequenceLength . 'd',
        $prefix,
        $year,
        strtoupper($collegeCode),
        $newSeq
    );
}

/**
 * Get current authenticated user
 */
function getCurrentUser(): ?array {
    $token = JWT::getTokenFromHeader();
    
    if (!$token) {
        return null;
    }
    
    $payload = JWT::verify($token);
    
    if (!$payload || !isset($payload['user_id'])) {
        return null;
    }
    
    $db = Database::getInstance();
    $user = $db->fetchOne(
        "SELECT id, alumni_id, email, name, role, profile_image, status, email_verified,
                verification_status, verified_at, rejection_reason, campus_id
         FROM users WHERE id = ? AND status = 'active'",
        [$payload['user_id']]
    );
    
    return $user;
}

/**
 * Check if current user is admin
 */
function isAdmin(): bool {
    $user = getCurrentUser();
    return $user && in_array($user['role'], ['admin', 'system_admin']);
}

/**
 * Check if current user is system admin
 */
function isSystemAdmin(): bool {
    $user = getCurrentUser();
    return $user && $user['role'] === 'system_admin';
}

/**
 * Log security event
 */
function logSecurityEvent(string $eventType, string $description, ?int $userId = null, ?string $email = null): void {
    $db = Database::getInstance();
    
    $db->insert('security_logs', [
        'event_type' => $eventType,
        'description' => $description,
        'user_id' => $userId,
        'email' => $email,
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
    ]);
}

/**
 * Log admin activity
 */
function logAdminActivity(int $userId, string $activityType, string $description, ?string $targetType = null, ?int $targetId = null): void {
    $db = Database::getInstance();
    
    $db->insert('admin_activities', [
        'user_id' => $userId,
        'activity_type' => $activityType,
        'description' => $description,
        'target_type' => $targetType,
        'target_id' => $targetId,
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
    ]);
}
