<?php
/**
 * Admin Login API
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../utils/helpers.php';
require_once __DIR__ . '/../../middleware/auth.php';

// Rate limit login attempts
rateLimit('admin_login', 5, 300); // 5 attempts per 5 minutes

try {
    $db = Database::getInstance()->getConnection();
    $data = getRequestBody();
    
    $email = sanitize($data['email'] ?? '');
    $password = $data['password'] ?? '';
    
    if (!$email || !$password) {
        respondError('Email and password are required', 400);
    }
    
    // Find admin user
    $stmt = $db->prepare("
        SELECT * FROM users 
        WHERE email = :email 
        AND role IN ('admin', 'system_admin')
        AND status = 'active'
    ");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();
    
    if (!$user || !Password::verify($password, $user['password'])) {
        logSecurityEvent($user['id'] ?? null, 'admin_login_failed', [
            'email' => $email,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
        respondError('Invalid credentials', 401);
    }
    
    // Generate token
    $token = JWT::generate([
        'user_id' => $user['id'],
        'email' => $user['email'],
        'role' => $user['role']
    ]);
    
    // Update last login
    $stmt = $db->prepare("UPDATE users SET last_login = NOW() WHERE id = :id");
    $stmt->execute(['id' => $user['id']]);
    
    // Log successful login
    logSecurityEvent($user['id'], 'admin_login_success', [
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ]);
    
    // Log admin activity
    $stmt = $db->prepare("
        INSERT INTO admin_activities (admin_id, action, target_type, description, ip_address, created_at)
        VALUES (:admin_id, 'login', 'auth', 'Admin logged in', :ip, NOW())
    ");
    $stmt->execute([
        'admin_id' => $user['id'],
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ]);
    
    // Remove sensitive data
    unset($user['password'], $user['verification_code'], $user['reset_token']);
    
    respondSuccess([
        'token' => $token,
        'user' => $user
    ]);
    
} catch (Exception $e) {
    respondError('Login failed: ' . $e->getMessage(), 500);
}
