<?php
/**
 * Admin Delete Announcement API
 * DELETE /api/admin/announcements/{id} - Delete announcement
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

$announcementId = $GLOBALS['url_params']['id'] ?? null;

if (!$announcementId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Announcement ID required']);
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    
    $stmt = $db->prepare("SELECT id, title FROM announcements WHERE id = ?");
    $stmt->execute([$announcementId]);
    $announcement = $stmt->fetch();
    
    if (!$announcement) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Announcement not found']);
        exit;
    }
    
    $db->beginTransaction();
    
    $stmt = $db->prepare("DELETE FROM announcement_reads WHERE announcement_id = ?");
    $stmt->execute([$announcementId]);
    
    $stmt = $db->prepare("DELETE FROM announcements WHERE id = ?");
    $stmt->execute([$announcementId]);
    
    $db->commit();
    
    $admin = getCurrentUser();
    $stmt = $db->prepare("INSERT INTO admin_activities (admin_id, action, target_type, target_id, details, ip_address, created_at) VALUES (?, 'delete', 'announcement', ?, ?, ?, NOW())");
    $stmt->execute([$admin['id'], $announcementId, json_encode(['title' => $announcement['title']]), $_SERVER['REMOTE_ADDR'] ?? null]);
    
    echo json_encode(['success' => true, 'message' => 'Announcement deleted']);
    
} catch (PDOException $e) {
    if (isset($db) && $db->inTransaction()) $db->rollBack();
    error_log("Admin Delete Announcement Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
