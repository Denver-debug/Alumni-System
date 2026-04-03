<?php
/**
 * Admin Update Announcement API
 * PUT /api/admin/announcements/{id} - Update announcement
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../middleware/auth.php';
require_once __DIR__ . '/../../../config/auth.php';

header('Content-Type: application/json');

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$announcementId = $GLOBALS['url_params']['id'] ?? null;

if (!$announcementId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Announcement ID required']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

try {
    $db = Database::getInstance()->getConnection();
    
    $stmt = $db->prepare("SELECT id FROM announcements WHERE id = ?");
    $stmt->execute([$announcementId]);
    if (!$stmt->fetch()) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Announcement not found']);
        exit;
    }
    
    $updates = [];
    $params = [];
    
    $fields = ['title', 'content', 'priority', 'status', 'is_pinned', 'image_url', 'publish_at', 'expires_at'];
    
    foreach ($fields as $field) {
        if (isset($data[$field])) {
            $updates[] = "$field = ?";
            $params[] = $data[$field];
        }
    }
    
    foreach (['target_colleges', 'target_programs', 'target_batch_years'] as $field) {
        if (isset($data[$field])) {
            $updates[] = "$field = ?";
            $params[] = json_encode($data[$field]);
        }
    }
    
    if (empty($updates)) {
        echo json_encode(['success' => true, 'message' => 'No changes']);
        exit;
    }
    
    $updates[] = "updated_at = NOW()";
    $params[] = $announcementId;
    
    $stmt = $db->prepare("UPDATE announcements SET " . implode(', ', $updates) . " WHERE id = ?");
    $stmt->execute($params);
    
    $admin = getCurrentUser();
    $stmt = $db->prepare("INSERT INTO admin_activities (admin_id, action, target_type, target_id, details, ip_address, created_at) VALUES (?, 'update', 'announcement', ?, ?, ?, NOW())");
    $stmt->execute([$admin['id'], $announcementId, json_encode(['fields' => array_keys($data)]), $_SERVER['REMOTE_ADDR'] ?? null]);
    
    echo json_encode(['success' => true, 'message' => 'Announcement updated']);
    
} catch (PDOException $e) {
    error_log("Admin Update Announcement Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
