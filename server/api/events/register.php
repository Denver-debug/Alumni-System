<?php
/**
 * Events API - Register for Event
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../utils/helpers.php';
require_once __DIR__ . '/../../middleware/auth.php';

requireAuth();

try {
    $db = Database::getInstance()->getConnection();
    $user = getCurrentUser();
    $data = getRequestBody();
    
    $eventId = $data['event_id'] ?? $_GET['id'] ?? null;
    
    if (!$eventId) {
        respondError('Event ID required', 400);
    }
    
    // Check event exists and is open for registration
    $stmt = $db->prepare("SELECT * FROM events WHERE id = :id");
    $stmt->execute(['id' => $eventId]);
    $event = $stmt->fetch();
    
    if (!$event) {
        respondError('Event not found', 404);
    }
    
    if ($event['status'] !== 'upcoming') {
        respondError('Registration is closed for this event', 400);
    }
    
    // Check registration deadline
    if ($event['registration_deadline'] && strtotime($event['registration_deadline']) < time()) {
        respondError('Registration deadline has passed', 400);
    }
    
    // Check max attendees
    if ($event['max_attendees']) {
        $stmt = $db->prepare("SELECT COUNT(*) FROM event_attendances WHERE event_id = :event_id");
        $stmt->execute(['event_id' => $eventId]);
        $currentCount = $stmt->fetchColumn();
        
        if ($currentCount >= $event['max_attendees']) {
            respondError('Event is full', 400);
        }
    }
    
    // Check if already registered
    $stmt = $db->prepare("
        SELECT id FROM event_attendances 
        WHERE event_id = :event_id AND user_id = :user_id
    ");
    $stmt->execute([
        'event_id' => $eventId,
        'user_id' => $user['id']
    ]);
    
    if ($stmt->fetch()) {
        respondError('Already registered for this event', 400);
    }
    
    // Register
    $stmt = $db->prepare("
        INSERT INTO event_attendances (event_id, user_id, status, created_at)
        VALUES (:event_id, :user_id, 'registered', NOW())
    ");
    $stmt->execute([
        'event_id' => $eventId,
        'user_id' => $user['id']
    ]);
    
    respondSuccess([
        'message' => 'Successfully registered for the event',
        'event' => $event
    ], 201);
    
} catch (Exception $e) {
    respondError('Registration failed: ' . $e->getMessage(), 500);
}
