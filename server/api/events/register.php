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
    syncEventStatuses($db);
    $user = getCurrentUser();
    $data = getRequestBody();

    $eventId = $GLOBALS['url_params']['id'] ?? ($data['event_id'] ?? ($_GET['id'] ?? null));

    if (!$eventId) {
        respondError('Event ID required', 400);
    }

    $stmt = $db->prepare("SELECT * FROM events WHERE id = :id");
    $stmt->execute(['id' => $eventId]);
    $event = $stmt->fetch();

    if (!$event) {
        respondError('Event not found', 404);
    }

    if ($event['status'] !== 'upcoming') {
        respondError('Registration is closed for this event', 400);
    }

    if ($event['registration_deadline'] && strtotime($event['registration_deadline']) < time()) {
        respondError('Registration deadline has passed', 400);
    }

    if ($event['max_attendees']) {
        $stmt = $db->prepare("SELECT COUNT(*) FROM event_rsvps WHERE event_id = :event_id AND status IN ('going', 'maybe')");
        $stmt->execute(['event_id' => $eventId]);
        $currentCount = (int)$stmt->fetchColumn();

        if ($currentCount >= (int)$event['max_attendees']) {
            respondError('Event is full', 400);
        }
    }

    $stmt = $db->prepare("\n        SELECT id, status\n        FROM event_rsvps\n        WHERE event_id = :event_id AND user_id = :user_id\n        LIMIT 1\n    ");
    $stmt->execute([
        'event_id' => $eventId,
        'user_id' => $user['id']
    ]);
    $existing = $stmt->fetch();

    if ($existing && in_array($existing['status'], ['going', 'maybe'], true)) {
        respondError('Already registered for this event', 400);
    }

    if ($existing) {
        $stmt = $db->prepare("UPDATE event_rsvps SET status = 'going', updated_at = NOW() WHERE id = :id");
        $stmt->execute(['id' => $existing['id']]);
    } else {
        $stmt = $db->prepare("\n            INSERT INTO event_rsvps (event_id, user_id, status, created_at, updated_at)\n            VALUES (:event_id, :user_id, 'going', NOW(), NOW())\n        ");
        $stmt->execute([
            'event_id' => $eventId,
            'user_id' => $user['id']
        ]);
    }

    respondSuccess([
        'message' => 'Successfully registered for the event',
        'event' => $event
    ], 201);
} catch (Exception $e) {
    respondError('Registration failed: ' . $e->getMessage(), 500);
}
