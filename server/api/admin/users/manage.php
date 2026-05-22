<?php
/**
 * Admin User Manage API
 * PUT /api/admin/users/{id}
 * DELETE /api/admin/users/{id}
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../utils/helpers.php';
require_once __DIR__ . '/../../../middleware/auth.php';

$admin = requireTopAdmin();

$userId = $GLOBALS['url_params']['id'] ?? null;
if (!$userId) {
    respondError('User ID required', 400);
}

try {
    $db = Database::getInstance()->getConnection();
    $method = $_SERVER['REQUEST_METHOD'];

    if ($method === 'PUT') {
        $data = getRequestBody();
        $updates = [];
        $params = [];

        if (isset($data['status'])) {
            if (!in_array($data['status'], ['active', 'inactive', 'blocked'], true)) {
                respondError('Invalid status value', 400);
            }
            $updates[] = 'status = ?';
            $params[] = $data['status'];
        }

        if (isset($data['role'])) {
            if (!in_array($data['role'], ['alumni', 'admin', 'system_admin'], true)) {
                respondError('Invalid role value', 400);
            }
            $updates[] = 'role = ?';
            $params[] = $data['role'];
        }

        if (empty($updates)) {
            respondError('No changes provided', 400);
        }

        $updates[] = 'updated_at = NOW()';
        $params[] = $userId;

        $stmt = $db->prepare('UPDATE users SET ' . implode(', ', $updates) . ' WHERE id = ?');
        $stmt->execute($params);

        respondSuccess(['id' => (int) $userId, 'message' => 'User updated']);
    }

    if ($method === 'DELETE') {
        // Soft delete to avoid breaking foreign-key references.
        $stmt = $db->prepare("UPDATE users SET status = 'inactive', updated_at = NOW() WHERE id = ?");
        $stmt->execute([$userId]);

        respondSuccess(['id' => (int) $userId, 'message' => 'User deactivated']);
    }

    respondError('Method not allowed', 405);
} catch (Exception $e) {
    respondError('User update failed: ' . $e->getMessage(), 500);
}
