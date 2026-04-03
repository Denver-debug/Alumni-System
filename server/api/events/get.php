<?php
/**
 * Events API - Get Single Event
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../utils/helpers.php';

$user = null;
try {
    $user = getCurrentUser();
} catch (Exception $e) {}

try {
    $db = Database::getInstance()->getConnection();
    
    $eventId = $_GET['id'] ?? null;
    
    if (!$eventId) {
        respondError('Event ID required', 400);
    }
    
    // Get event details
    $stmt = $db->prepare("
        SELECT 
            e.*,
            (SELECT COUNT(*) FROM event_attendances WHERE event_id = e.id) as registered_count,
            u.name as created_by_name
        FROM events e
        LEFT JOIN users u ON e.created_by = u.id
        WHERE e.id = :id
    ");
    $stmt->execute(['id' => $eventId]);
    $event = $stmt->fetch();
    
    if (!$event) {
        respondError('Event not found', 404);
    }
    
    // Check if current user is registered
    if ($user) {
        $stmt = $db->prepare("
            SELECT status, check_in_time 
            FROM event_attendances 
            WHERE event_id = :event_id AND user_id = :user_id
        ");
        $stmt->execute([
            'event_id' => $eventId,
            'user_id' => $user['id']
        ]);
        $registration = $stmt->fetch();
        $event['user_registration'] = $registration ?: null;
    }
    
    respondSuccess($event);
    
} catch (Exception $e) {
    respondError('Failed to load event: ' . $e->getMessage(), 500);
}
