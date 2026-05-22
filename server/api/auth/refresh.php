<?php
/**
 * Refresh Session Token API
 * POST /api/v1/auth/refresh
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../utils/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error('Method not allowed', 405);
}

$token = JWT::getTokenFromHeader();
if (!$token) {
    unauthorized('Authentication required');
}

$payload = JWT::decode($token, true);
if (!$payload || !isset($payload['user_id'])) {
    unauthorized('Invalid or expired token');
}

$db = Database::getInstance();
$user = $db->fetchOne(
    "SELECT id, alumni_id, email, name, role, profile_image, status
     FROM users
     WHERE id = ?",
    [$payload['user_id']]
);

if (!$user || $user['status'] !== 'active') {
    unauthorized('Session is no longer valid');
}

$newToken = JWT::generate([
    'user_id' => $user['id'],
    'email' => $user['email'],
    'role' => $user['role'],
]);

$userData = processUserData([
    'id' => $user['id'],
    'alumni_id' => $user['alumni_id'],
    'email' => $user['email'],
    'name' => $user['name'],
    'role' => $user['role'],
    'profile_image' => $user['profile_image'],
    'status' => $user['status'],
]);

success([
    'token' => $newToken,
    'user' => $userData,
], 'Token refreshed');
