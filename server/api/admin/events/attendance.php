<?php
/**
 * Admin Mark Attendance API
 * POST /api/admin/events/{id}/attendance - Mark alumni attendance
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../middleware/auth.php';
require_once __DIR__ . '/../../../config/auth.php';

header('Content-Type: application/json');

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
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
$userId = $data['user_id'] ?? null;
$status = $data['status'] ?? 'attended';

if (!$userId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'User ID required']);
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    
    // Get event
    $stmt = $db->prepare("SELECT id, points_reward FROM events WHERE id = ?");
    $stmt->execute([$eventId]);
    $event = $stmt->fetch();
    
    if (!$event) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Event not found']);
        exit;
    }
    
    // Check if attendance exists
    $stmt = $db->prepare("SELECT id, status FROM event_attendances WHERE event_id = ? AND user_id = ?");
    $stmt->execute([$eventId, $userId]);
    $attendance = $stmt->fetch();
    
    $db->beginTransaction();
    
    if ($attendance) {
        // Update
        $stmt = $db->prepare("UPDATE event_attendances SET status = ?, checked_in_at = NOW() WHERE id = ?");
        $stmt->execute([$status, $attendance['id']]);
        
        // Award points only if marking as attended for first time
        if ($status === 'attended' && $attendance['status'] !== 'attended') {
            $stmt = $db->prepare("UPDATE alumni_profiles SET total_points = total_points + ? WHERE user_id = ?");
            $stmt->execute([$event['points_reward'], $userId]);
            
            $stmt = $db->prepare("INSERT INTO point_transactions (user_id, points, type, description, event_id, created_at) VALUES (?, ?, 'earned', 'Event attendance', ?, NOW())");
            $stmt->execute([$userId, $event['points_reward'], $eventId]);
        }
    } else {
        // Create new attendance
        $stmt = $db->prepare("INSERT INTO event_attendances (event_id, user_id, status, checked_in_at, created_at) VALUES (?, ?, ?, NOW(), NOW())");
        $stmt->execute([$eventId, $userId, $status]);
        
        if ($status === 'attended') {
            $stmt = $db->prepare("UPDATE alumni_profiles SET total_points = total_points + ? WHERE user_id = ?");
            $stmt->execute([$event['points_reward'], $userId]);
            
            $stmt = $db->prepare("INSERT INTO point_transactions (user_id, points, type, description, event_id, created_at) VALUES (?, ?, 'earned', 'Event attendance', ?, NOW())");
            $stmt->execute([$userId, $event['points_reward'], $eventId]);
        }
    }
    
    $db->commit();
    
    echo json_encode(['success' => true, 'message' => 'Attendance updated']);
    
} catch (PDOException $e) {
    if (isset($db) && $db->inTransaction()) $db->rollBack();
    error_log("Admin Mark Attendance Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
