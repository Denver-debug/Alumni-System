<?php
/**
 * Events API - Check-in to Event
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
    $code = $data['code'] ?? '';
    
    if (!$eventId) {
        respondError('Event ID required', 400);
    }
    
    if (!$code || strlen($code) !== 6) {
        respondError('Valid attendance code required', 400);
    }
    
    // Check event exists and is active
    $stmt = $db->prepare("SELECT * FROM events WHERE id = :id");
    $stmt->execute(['id' => $eventId]);
    $event = $stmt->fetch();
    
    if (!$event) {
        respondError('Event not found', 404);
    }
    
    if (!in_array($event['status'], ['upcoming', 'ongoing'])) {
        respondError('Check-in is not available for this event', 400);
    }
    
    // Verify attendance code
    $stmt = $db->prepare("
        SELECT * FROM event_attendance_codes 
        WHERE event_id = :event_id AND code = :code AND expires_at > NOW()
    ");
    $stmt->execute([
        'event_id' => $eventId,
        'code' => strtoupper($code)
    ]);
    
    if (!$stmt->fetch()) {
        respondError('Invalid or expired attendance code', 400);
    }
    
    // Check if registered
    $stmt = $db->prepare("
        SELECT * FROM event_attendances 
        WHERE event_id = :event_id AND user_id = :user_id
    ");
    $stmt->execute([
        'event_id' => $eventId,
        'user_id' => $user['id']
    ]);
    $attendance = $stmt->fetch();
    
    if (!$attendance) {
        respondError('You are not registered for this event', 400);
    }
    
    if ($attendance['status'] === 'attended') {
        respondError('You have already checked in', 400);
    }
    
    $db->beginTransaction();
    
    // Update attendance
    $stmt = $db->prepare("
        UPDATE event_attendances 
        SET status = 'attended', 
            check_in_time = NOW(),
            points_awarded = :points
        WHERE event_id = :event_id AND user_id = :user_id
    ");
    $stmt->execute([
        'event_id' => $eventId,
        'user_id' => $user['id'],
        'points' => $event['points_reward']
    ]);
    
    // Award points
    $stmt = $db->prepare("
        INSERT INTO point_transactions (user_id, event_id, points, type, description, created_at)
        VALUES (:user_id, :event_id, :points, 'earned', :description, NOW())
    ");
    $stmt->execute([
        'user_id' => $user['id'],
        'event_id' => $eventId,
        'points' => $event['points_reward'],
        'description' => 'Attended: ' . $event['title']
    ]);
    
    // Update total points and badge level
    $stmt = $db->prepare("
        UPDATE alumni_profiles 
        SET total_points = total_points + :points,
            badge_level = CASE 
                WHEN total_points + :points >= 5000 THEN 'Diamond'
                WHEN total_points + :points >= 1000 THEN 'Platinum'
                WHEN total_points + :points >= 500 THEN 'Gold'
                WHEN total_points + :points >= 100 THEN 'Silver'
                ELSE 'Bronze'
            END
        WHERE user_id = :user_id
    ");
    $stmt->execute([
        'points' => $event['points_reward'],
        'user_id' => $user['id']
    ]);
    
    $db->commit();
    
    respondSuccess([
        'message' => 'Check-in successful!',
        'points_awarded' => $event['points_reward'],
        'event' => $event['title']
    ]);
    
} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    respondError('Check-in failed: ' . $e->getMessage(), 500);
}
