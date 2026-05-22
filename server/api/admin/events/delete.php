<?php
/**
 * Admin Delete Event API
 * DELETE /api/admin/events/{id} - Delete event
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../middleware/auth.php';
require_once __DIR__ . '/../../../config/auth.php';
require_once __DIR__ . '/../../../utils/helpers.php';

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
    
    $stmt = $db->prepare("SELECT id, title, cover_image FROM events WHERE id = ?");
    $stmt->execute([$eventId]);
    $event = $stmt->fetch();
    
    if (!$event) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Event not found']);
        exit;
    }
    
    $db->beginTransaction();
    
    // Delete related records
    $stmt = $db->prepare("DELETE FROM event_rsvps WHERE event_id = ?");
    $stmt->execute([$eventId]);
    
    $stmt = $db->prepare("DELETE FROM event_attendances WHERE event_id = ?");
    $stmt->execute([$eventId]);
    
    $stmt = $db->prepare("DELETE FROM events WHERE id = ?");
    $stmt->execute([$eventId]);
    
    $db->commit();

    if (isLocalUploadPath($event['cover_image'] ?? null)) {
        deleteLocalImage($event['cover_image']);
    }
    
    $admin = getCurrentUser();
    logAdminActivity((int)$admin['id'], 'delete', 'Deleted event: ' . $event['title'], 'event', (int)$eventId);
    
    echo json_encode(['success' => true, 'message' => 'Event deleted']);
    
} catch (PDOException $e) {
    if (isset($db) && $db->inTransaction()) $db->rollBack();
    error_log("Admin Delete Event Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
