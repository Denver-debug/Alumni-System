<?php
/**
 * Admin User Password Reset API
 * POST /api/admin/users/{id}/reset-password
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../config/auth.php';
require_once __DIR__ . '/../../../utils/helpers.php';
require_once __DIR__ . '/../../../middleware/auth.php';

requireTopAdmin();

$userId = $GLOBALS['url_params']['id'] ?? null;
if (!$userId) {
    respondError('User ID required', 400);
}

try {
    $db = Database::getInstance()->getConnection();

    $stmt = $db->prepare('SELECT id, email, name FROM users WHERE id = ? LIMIT 1');
    $stmt->execute([$userId]);
    $target = $stmt->fetch();

    if (!$target) {
        respondError('User not found', 404);
    }

    $resetCode = generateCode(6);
    $expiresAt = date('Y-m-d H:i:s', time() + 3600);

    $stmt = $db->prepare('UPDATE users SET reset_code = ?, reset_expires = ?, updated_at = NOW() WHERE id = ?');
    $stmt->execute([$resetCode, $expiresAt, $userId]);

    respondSuccess([
        'id' => (int) $userId,
        'message' => 'Password reset code generated',
        // Temporary for development visibility; remove in production if needed.
        'reset_code' => $resetCode,
        'expires_at' => $expiresAt,
    ]);
} catch (Exception $e) {
    respondError('Failed to generate reset password code: ' . $e->getMessage(), 500);
}
