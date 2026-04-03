<?php
/**
 * Admin Delete Event API
 * DELETE /api/admin/events/{id} - Delete event
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

$eventId = $GLOBALS['url_params']['id'] ?? null;

if (!$eventId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Event ID required']);
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    
    $stmt = $db->prepare("SELECT id, title FROM events WHERE id = ?");
    $stmt->execute([$eventId]);
    $event = $stmt->fetch();
    
    if (!$event) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Event not found']);
        exit;
    }
    
    $db->beginTransaction();
    
    // Delete related records
    $stmt = $db->prepare("DELETE FROM event_attendance_codes WHERE event_id = ?");
    $stmt->execute([$eventId]);
    
    $stmt = $db->prepare("DELETE FROM event_attendances WHERE event_id = ?");
    $stmt->execute([$eventId]);
    
    $stmt = $db->prepare("DELETE FROM events WHERE id = ?");
    $stmt->execute([$eventId]);
    
    $db->commit();
    
    $admin = getCurrentUser();
    $stmt = $db->prepare("INSERT INTO admin_activities (admin_id, action, target_type, target_id, details, ip_address, created_at) VALUES (?, 'delete', 'event', ?, ?, ?, NOW())");
    $stmt->execute([$admin['id'], $eventId, json_encode(['title' => $event['title']]), $_SERVER['REMOTE_ADDR'] ?? null]);
    
    echo json_encode(['success' => true, 'message' => 'Event deleted']);
    
} catch (PDOException $e) {
    if (isset($db) && $db->inTransaction()) $db->rollBack();
    error_log("Admin Delete Event Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
