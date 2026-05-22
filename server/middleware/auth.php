<?php
/**
 * Authentication Middleware
 * Alumni Management System
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../utils/helpers.php';

/**
 * Require authentication
 */
function requireAuth(): array {
    $user = getCurrentUser();
    
    if (!$user) {
        unauthorized('Authentication required');
    }
    
    if ($user['status'] === 'blocked') {
        unauthorized('Your account has been blocked');
    }
    
    if ($user['status'] === 'inactive') {
        unauthorized('Your account is inactive');
    }

    if ($user['role'] === 'alumni') {
        $verificationStatus = $user['verification_status'] ?? 'pending';
        if ($verificationStatus !== 'verified') {
            $message = $verificationStatus === 'rejected'
                ? 'Your account was not approved. Please contact support.'
                : 'Your account is awaiting admin verification.';
            forbidden($message);
        }
    }
    
    return $user;
}

/**
 * Require admin role (admin, campus_admin, staff, or system_admin)
 */
function requireAdmin(): array {
    $user = requireAuth();
    
    if (!in_array($user['role'], ['admin', 'campus_admin', 'staff', 'system_admin'])) {
        forbidden('Admin access required');
    }

    enforceAdminRouteAccess($user);
    
    return $user;
}

/**
 * Require elevated admin role (admin or system_admin only)
 */
function requireTopAdmin(): array {
    $user = requireAuth();

    if (!in_array($user['role'], ['admin', 'system_admin'], true)) {
        forbidden('Admin access required');
    }

    enforceAdminRouteAccess($user);

    return $user;
}

/**
 * Normalize the current request path for RBAC checks.
 */
function getRequestPath(): string {
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';
    $path = '/' . ltrim($path, '/');
    return rtrim($path, '/') === '' ? '/' : rtrim($path, '/');
}

/**
 * Check whether the request path matches any restricted prefix.
 */
function isPathRestricted(string $path, array $prefixes): bool {
    foreach ($prefixes as $prefix) {
        $normalized = rtrim($prefix, '/');
        if ($normalized === '') {
            continue;
        }

        // Exact match or starts with prefix followed by /
        if ($path === $normalized || str_starts_with($path, $normalized . '/')) {
            return true;
        }
    }

    return false;
}

/**
 * Enforce role-based access control for admin routes.
 */
function enforceAdminRouteAccess(array $user): void {
    $path = getRequestPath();

    // Skip non-admin routes
    if (!str_starts_with($path, '/admin') && !str_starts_with($path, '/api/v1/admin')) {
        return;
    }
    
    // Skip login page
    if ($path === '/admin/login' || $path === '/api/v1/admin/login') {
        return;
    }

    $role = (string)($user['role'] ?? '');

    // System admins and regular admins have full access
    if (in_array($role, ['admin', 'system_admin'], true)) {
        return;
    }

    // Campus admin restrictions - specific modules only
    if ($role === 'campus_admin') {
        $restricted = [
            '/admin/alumni-verification',
            '/admin/users',
            '/admin/form-builder',
            '/admin/settings',
            '/admin/security',
            '/admin/logs',
            '/admin/campuses',
            '/api/v1/admin/users',
            '/api/v1/admin/form-fields',
            '/api/v1/admin/settings',
            '/api/v1/admin/security',
            '/api/v1/admin/logs',
            '/api/v1/admin/campuses',
        ];
        
        if (isPathRestricted($path, $restricted)) {
            error_log("Campus admin blocked from: $path");
            forbidden('You do not have access to this admin module');
        }
        return;
    }

    // Staff restrictions - more modules blocked
    if ($role === 'staff') {
        $restricted = [
            '/admin/alumni-verification',
            '/admin/events',
            '/admin/announcements',
            '/admin/settings',
            '/admin/security',
            '/admin/logs',
            '/admin/campuses',
            '/admin/users',
            '/admin/form-builder',
            '/admin/gamification',
            '/admin/organization',
            '/admin/analytics',
            '/api/v1/admin/announcements',
            '/api/v1/admin/form-fields',
            '/api/v1/admin/settings',
            '/api/v1/admin/security',
            '/api/v1/admin/logs',
            '/api/v1/admin/campuses',
            '/api/v1/admin/users',
            '/api/v1/admin/gamification',
            '/api/v1/admin/organization',
            '/api/v1/admin/analytics',
        ];
        
        if (isPathRestricted($path, $restricted)) {
            error_log("Staff blocked from: $path");
            forbidden('You do not have access to this admin module');
        }
        return;
    }
}

/**
 * Require system admin role
 */
function requireSystemAdmin(): array {
    $user = requireAuth();
    
    if ($user['role'] !== 'system_admin') {
        forbidden('System admin access required');
    }
    
    return $user;
}

/**
 * Require campus admin role (admin, campus_admin, or system_admin)
 */
function requireCampusAdmin(): array {
    $user = requireAuth();
    
    if (!in_array($user['role'], ['admin', 'campus_admin', 'system_admin'])) {
        forbidden('Campus admin access required');
    }
    
    return $user;
}

/**
 * Require staff role (admin, staff, campus_admin, or system_admin)
 */
function requireStaff(): array {
    $user = requireAuth();
    
    if (!in_array($user['role'], ['admin', 'staff', 'campus_admin', 'system_admin'])) {
        forbidden('Staff access required');
    }
    
    return $user;
}

/**
 * Require verified email
 */
function requireVerifiedEmail(): array {
    $user = requireAuth();
    
    if (!$user['email_verified']) {
        error('Email verification required', 403);
    }
    
    return $user;
}

/**
 * Optional authentication (returns user if logged in, null otherwise)
 */
function optionalAuth(): ?array {
    return getCurrentUser();
}

/**
 * Rate limiting middleware
 */
function rateLimit(int $maxRequests = RATE_LIMIT_REQUESTS, int $window = RATE_LIMIT_WINDOW): void {
    $ip = getClientIp();
    $key = 'rate_limit_' . md5($ip);
    
    // Using file-based rate limiting (consider Redis for production)
    $cacheDir = sys_get_temp_dir() . '/alumni_rate_limit';
    if (!is_dir($cacheDir)) {
        mkdir($cacheDir, 0755, true);
    }
    
    $cacheFile = $cacheDir . '/' . $key;
    $data = [];
    
    if (file_exists($cacheFile)) {
        $data = json_decode(file_get_contents($cacheFile), true) ?? [];
    }
    
    // Clean old entries
    $now = time();
    $data = array_filter($data, fn($timestamp) => $timestamp > $now - $window);
    
    // Check rate limit
    if (count($data) >= $maxRequests) {
        http_response_code(429);
        header('Retry-After: ' . $window);
        error('Too many requests. Please try again later.', 429);
    }
    
    // Add current request
    $data[] = $now;
    file_put_contents($cacheFile, json_encode($data));
}

/**
 * CORS middleware
 */
function cors(): void {
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    $allowedOrigins = array_values(array_filter(array_map('trim', explode(',', CORS_ALLOWED_ORIGINS))));
    $allowOrigin = '';

    $isOriginAllowed = static function (string $requestOrigin, array $allowlist): bool {
        if ($requestOrigin === '') {
            return false;
        }

        $requestParts = parse_url($requestOrigin);
        if (!$requestParts || empty($requestParts['scheme']) || empty($requestParts['host'])) {
            return false;
        }

        foreach ($allowlist as $allowed) {
            if ($allowed === '*') {
                return true;
            }

            if ($allowed === $requestOrigin) {
                return true;
            }

            if (str_ends_with($allowed, ':*')) {
                $baseAllowed = substr($allowed, 0, -2);
                $allowedParts = parse_url($baseAllowed);
                if (
                    $allowedParts &&
                    ($allowedParts['scheme'] ?? '') === ($requestParts['scheme'] ?? '') &&
                    ($allowedParts['host'] ?? '') === ($requestParts['host'] ?? '')
                ) {
                    return true;
                }
                continue;
            }

            $allowedParts = parse_url($allowed);
            if (!$allowedParts || empty($allowedParts['scheme']) || empty($allowedParts['host'])) {
                continue;
            }

            if (
                ($allowedParts['scheme'] ?? '') !== ($requestParts['scheme'] ?? '') ||
                ($allowedParts['host'] ?? '') !== ($requestParts['host'] ?? '')
            ) {
                continue;
            }

            // If allowlist entry has no port, treat it as any port on same host/scheme.
            if (!isset($allowedParts['port'])) {
                return true;
            }

            if ((int)$allowedParts['port'] === (int)($requestParts['port'] ?? 0)) {
                return true;
            }
        }

        return false;
    };

    if (CORS_ALLOWED_ORIGINS === '*' || in_array('*', $allowedOrigins, true)) {
        $allowOrigin = $origin !== '' ? $origin : '*';
    } elseif ($origin !== '' && $isOriginAllowed($origin, $allowedOrigins)) {
        $allowOrigin = $origin;
    }

    if ($allowOrigin !== '') {
        header("Access-Control-Allow-Origin: $allowOrigin");
        header('Vary: Origin');
    }

    header("Access-Control-Allow-Methods: " . CORS_ALLOWED_METHODS);
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-CSRF-Token");
    if ($allowOrigin !== '' && $allowOrigin !== '*') {
        header("Access-Control-Allow-Credentials: true");
    }
    header("Access-Control-Max-Age: 86400");
    
    // Handle preflight
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        if ($origin !== '' && $allowOrigin === '') {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Origin is not allowed by CORS policy'
            ]);
            exit;
        }

        http_response_code(204);
        exit;
    }
}

/**
 * Request logging middleware
 */
function logRequest(): void {
    $method = $_SERVER['REQUEST_METHOD'];
    $uri = $_SERVER['REQUEST_URI'];
    $ip = getClientIp();
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
    
    error_log("[{$method}] {$uri} - IP: {$ip} - UA: {$userAgent}");
}

/**
 * Content type check
 */
function requireJson(): void {
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    
    if ($_SERVER['REQUEST_METHOD'] !== 'GET' && 
        strpos($contentType, 'application/json') === false &&
        strpos($contentType, 'multipart/form-data') === false) {
        error('Content-Type must be application/json', 415);
    }
}

/**
 * Security headers
 */
function securityHeaders(): void {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Content-Security-Policy: default-src \'self\'');
}

/**
 * Fetch a security setting with safe fallback.
 */
function getSecuritySetting(string $key, $fallback) {
    try {
        $db = Database::getInstance();
        $row = $db->fetchOne(
            "SELECT setting_value, setting_type FROM system_settings WHERE setting_key = ? LIMIT 1",
            [$key]
        );

        if (!$row) {
            return $fallback;
        }

        $value = $row['setting_value'];
        $type = $row['setting_type'] ?? 'string';

        if ($type === 'number') {
            return (int) $value;
        }

        if ($type === 'boolean') {
            // Convert string values to boolean properly
            $lowerValue = strtolower($value);
            return $lowerValue === 'true' || $value === '1' || $value === 1;
        }

        return $value;
    } catch (Throwable $e) {
        return $fallback;
    }
}

/**
 * Check account lockout
 */
function checkLockout(string $email): void {
    $db = Database::getInstance();

    $lockoutEnabled = getSecuritySetting('enable_login_lockout', true);
    
    error_log("checkLockout called for: $email");
    error_log("Lockout enabled: " . ($lockoutEnabled ? 'YES' : 'NO'));
    
    if (!$lockoutEnabled) {
        error_log("Lockout disabled - skipping check");
        return;
    }
    
    $user = $db->fetchOne(
        "SELECT locked_until, login_attempts FROM users WHERE email = ?",
        [$email]
    );
    
    if ($user) {
        error_log("User found - locked_until: " . ($user['locked_until'] ?? 'NULL') . ", attempts: " . ($user['login_attempts'] ?? 0));
    } else {
        error_log("User not found for lockout check");
    }
    
    if ($user && $user['locked_until']) {
        $lockedUntil = strtotime($user['locked_until']);
        $now = time();
        error_log("Locked until timestamp: $lockedUntil, Current time: $now");
        
        if ($lockedUntil > $now) {
            $minutes = ceil(($lockedUntil - $now) / 60);
            error_log("ACCOUNT IS LOCKED - $minutes minutes remaining");
            error("Account is locked. Try again in $minutes minutes.", 429);
        } else {
            error_log("Lock expired - resetting");
            // Reset lockout
            $db->update('users', 
                ['locked_until' => null, 'login_attempts' => 0], 
                'email = ?', 
                [$email]
            );
        }
    }
}

/**
 * Record failed login attempt
 */
function recordFailedLogin(string $email): void {
    $db = Database::getInstance();

    // Check if lockout is enabled
    $lockoutEnabled = getSecuritySetting('enable_login_lockout', true);
    
    $maxAttempts = (int) getSecuritySetting('max_login_attempts', MAX_LOGIN_ATTEMPTS);
    $lockoutMinutes = (int) getSecuritySetting('lockout_duration_minutes', (int) (LOCKOUT_DURATION / 60));
    if ($lockoutMinutes <= 0) {
        $lockoutMinutes = 15;
    }
    
    // Debug logging
    error_log("recordFailedLogin called for: $email");
    error_log("Lockout enabled: " . ($lockoutEnabled ? 'YES' : 'NO'));
    error_log("Max attempts: $maxAttempts");
    error_log("Lockout minutes: $lockoutMinutes");
    
    $user = $db->fetchOne("SELECT id, login_attempts FROM users WHERE email = ?", [$email]);
    
    if ($user) {
        error_log("User found - ID: {$user['id']}, Current attempts: " . ($user['login_attempts'] ?? 0));
        
        if ($lockoutEnabled) {
            $attempts = ($user['login_attempts'] ?? 0) + 1;
            $updates = ['login_attempts' => $attempts];

            error_log("New attempt count: $attempts");

            if ($attempts >= $maxAttempts) {
                $lockTime = date('Y-m-d H:i:s', time() + ($lockoutMinutes * 60));
                $updates['locked_until'] = $lockTime;
                error_log("LOCKING ACCOUNT until: $lockTime");
            }
            
            $db->update('users', $updates, 'id = ?', [$user['id']]);
            error_log("Database updated successfully");
        } else {
            error_log("Lockout disabled - not updating user record");
        }
    } else {
        error_log("User not found for email: $email");
    }

    // Always log the attempt regardless of lockout setting
    try {
        $db->insert('login_attempts', [
            'email' => $email,
            'ip_address' => getClientIp(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
            'success' => false,
            'failure_reason' => 'Invalid credentials'
        ]);
    } catch (Throwable $e) {
        error_log("Failed to log login attempt: " . $e->getMessage());
    }
    
    // Log security event
    try {
        logSecurityEvent('failed_login', "Failed login attempt for: $email", $user['id'] ?? null, $email);
    } catch (Throwable $e) {
        error_log("Failed to log security event: " . $e->getMessage());
    }
}

/**
 * Reset login attempts on successful login
 */
function resetLoginAttempts(int $userId): void {
    $db = Database::getInstance();
    
    $db->update('users', [
        'login_attempts' => 0,
        'locked_until' => null,
        'last_login' => date('Y-m-d H:i:s')
    ], 'id = ?', [$userId]);

    try {
        $user = $db->fetchOne("SELECT email FROM users WHERE id = ?", [$userId]);
        if ($user && $user['email']) {
            $db->insert('login_attempts', [
                'email' => $user['email'],
                'ip_address' => getClientIp(),
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
                'success' => true,
                'failure_reason' => null
            ]);
        }
    } catch (Throwable $e) {
        // Ignore logging failures to avoid blocking auth.
    }
}
