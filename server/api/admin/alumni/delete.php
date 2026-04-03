<?php
/**
 * Admin Delete Alumni API
 * DELETE /api/admin/alumni/{id} - Deactivate or delete alumni
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../middleware/auth.php';
require_once __DIR__ . '/../../../config/auth.php';

header('Content-Type: application/json');

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$userId = $GLOBALS['url_params']['id'] ?? null;

if (!$userId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Alumni ID required']);
    exit;
}

$permanent = isset($_GET['permanent']) && $_GET['permanent'] === 'true';

try {
    $db = Database::getInstance()->getConnection();
    
    $stmt = $db->prepare("SELECT id, name, email, alumni_id FROM users WHERE id = ? AND role = 'alumni'");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    
    if (!$user) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Alumni not found']);
        exit;
    }
    
    $admin = getCurrentUser();
    
    if ($permanent) {
        if ($admin['role'] !== 'system_admin') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Only system admins can permanently delete']);
            exit;
        }
        
        $db->beginTransaction();
        
        $tables = ['point_transactions' => 'user_id', 'event_attendances' => 'user_id', 
                   'conversation_participants' => 'user_id', 'alumni_profiles' => 'user_id'];
        
        foreach ($tables as $table => $column) {
            $stmt = $db->prepare("DELETE FROM $table WHERE $column = ?");
            $stmt->execute([$userId]);
        }
        
        $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        
        $db->commit();
        $message = 'Alumni permanently deleted';
    } else {
        $stmt = $db->prepare("UPDATE users SET status = 'inactive', updated_at = NOW() WHERE id = ?");
        $stmt->execute([$userId]);
        $message = 'Alumni deactivated';
    }
    
    $stmt = $db->prepare("INSERT INTO admin_activities (admin_id, action, target_type, target_id, details, ip_address, created_at) VALUES (?, ?, 'alumni', ?, ?, ?, NOW())");
    $stmt->execute([$admin['id'], $permanent ? 'delete' : 'deactivate', $userId, json_encode(['name' => $user['name'], 'email' => $user['email']]), $_SERVER['REMOTE_ADDR'] ?? null]);
    
    echo json_encode(['success' => true, 'message' => $message]);
    
} catch (PDOException $e) {
    if (isset($db) && $db->inTransaction()) $db->rollBack();
    error_log("Admin Delete Alumni Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
