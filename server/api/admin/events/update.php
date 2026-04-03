<?php
/**
 * Admin Update Event API
 * PUT /api/admin/events/{id} - Update event
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

$eventId = $GLOBALS['url_params']['id'] ?? null;

if (!$eventId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Event ID required']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

try {
    $db = Database::getInstance()->getConnection();
    
    $stmt = $db->prepare("SELECT id FROM events WHERE id = ?");
    $stmt->execute([$eventId]);
    if (!$stmt->fetch()) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Event not found']);
        exit;
    }
    
    $updates = [];
    $params = [];
    
    $fields = ['title', 'description', 'event_type', 'event_date', 'end_date', 'location',
               'venue_details', 'is_virtual', 'virtual_link', 'max_attendees', 
               'points_reward', 'registration_deadline', 'status', 'image_url'];
    
    foreach ($fields as $field) {
        if (isset($data[$field])) {
            $updates[] = "$field = ?";
            $params[] = $data[$field];
        }
    }
    
    // JSON fields
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
    $params[] = $eventId;
    
    $stmt = $db->prepare("UPDATE events SET " . implode(', ', $updates) . " WHERE id = ?");
    $stmt->execute($params);
    
    $admin = getCurrentUser();
    $stmt = $db->prepare("INSERT INTO admin_activities (admin_id, action, target_type, target_id, details, ip_address, created_at) VALUES (?, 'update', 'event', ?, ?, ?, NOW())");
    $stmt->execute([$admin['id'], $eventId, json_encode(['fields' => array_keys($data)]), $_SERVER['REMOTE_ADDR'] ?? null]);
    
    echo json_encode(['success' => true, 'message' => 'Event updated']);
    
} catch (PDOException $e) {
    error_log("Admin Update Event Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
