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
    
    return $user;
}

/**
 * Require admin role
 */
function requireAdmin(): array {
    $user = requireAuth();
    
    if (!in_array($user['role'], ['admin', 'system_admin'])) {
        forbidden('Admin access required');
    }
    
    return $user;
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
    // Get origin
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
    
    // Set CORS headers
    header("Access-Control-Allow-Origin: $origin");
    header("Access-Control-Allow-Methods: " . CORS_ALLOWED_METHODS);
    header("Access-Control-Allow-Headers: " . CORS_ALLOWED_HEADERS);
    header("Access-Control-Allow-Credentials: true");
    header("Access-Control-Max-Age: 86400");
    
    // Handle preflight
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
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
 * Check account lockout
 */
function checkLockout(string $email): void {
    $db = Database::getInstance();
    
    $user = $db->fetchOne(
        "SELECT locked_until, login_attempts FROM users WHERE email = ?",
        [$email]
    );
    
    if ($user && $user['locked_until']) {
        $lockedUntil = strtotime($user['locked_until']);
        if ($lockedUntil > time()) {
            $minutes = ceil(($lockedUntil - time()) / 60);
            error("Account is locked. Try again in $minutes minutes.", 429);
        } else {
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
    
    $user = $db->fetchOne("SELECT id, login_attempts FROM users WHERE email = ?", [$email]);
    
    if ($user) {
        $attempts = $user['login_attempts'] + 1;
        $updates = ['login_attempts' => $attempts];
        
        if ($attempts >= MAX_LOGIN_ATTEMPTS) {
            $updates['locked_until'] = date('Y-m-d H:i:s', time() + LOCKOUT_DURATION);
        }
        
        $db->update('users', $updates, 'id = ?', [$user['id']]);
    }
    
    // Log security event
    logSecurityEvent('failed_login', "Failed login attempt for: $email", $user['id'] ?? null, $email);
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
}
