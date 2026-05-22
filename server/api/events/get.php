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
    syncEventStatuses($db);
    
    $eventId = $GLOBALS['url_params']['id'] ?? ($_GET['id'] ?? null);
    
    if (!$eventId) {
        respondError('Event ID required', 400);
    }
    
    // Get event details
    $stmt = $db->prepare("
        SELECT 
            e.*,
                (SELECT COUNT(*) FROM event_rsvps WHERE event_id = e.id AND status IN ('going', 'maybe')) as registered_count,
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
                SELECT 
                    r.status as rsvp_status,
                    a.check_in_time,
                    a.points_awarded,
                    CASE
                        WHEN a.id IS NOT NULL THEN 'attended'
                        WHEN r.status = 'not_going' THEN 'absent'
                        WHEN r.id IS NOT NULL THEN 'registered'
                        ELSE NULL
                    END as status
                FROM event_rsvps r
                LEFT JOIN event_attendances a ON a.event_id = r.event_id AND a.user_id = r.user_id
                WHERE r.event_id = :event_id AND r.user_id = :user_id
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
