<?php
/**
 * Events API - Cancel Event Registration
 * POST /api/events/{id}/cancel-registration
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

    $eventId = $GLOBALS['url_params']['id'] ?? ($data['event_id'] ?? ($_GET['id'] ?? null));

    if (!$eventId) {
        respondError('Event ID required', 400);
    }

    $stmt = $db->prepare("SELECT id FROM event_rsvps WHERE event_id = :event_id AND user_id = :user_id LIMIT 1");
    $stmt->execute([
        'event_id' => $eventId,
        'user_id' => $user['id']
    ]);
    $registration = $stmt->fetch();

    if (!$registration) {
        respondError('You are not registered for this event', 404);
    }

    $db->beginTransaction();

    $stmt = $db->prepare("UPDATE event_rsvps SET status = 'not_going', updated_at = NOW() WHERE id = :id");
    $stmt->execute(['id' => $registration['id']]);

    $stmt = $db->prepare("DELETE FROM event_attendances WHERE event_id = :event_id AND user_id = :user_id");
    $stmt->execute([
        'event_id' => $eventId,
        'user_id' => $user['id']
    ]);

    $db->commit();

    respondSuccess([
        'event_id' => (int) $eventId,
        'message' => 'Registration cancelled successfully'
    ]);
} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    respondError('Failed to cancel registration: ' . $e->getMessage(), 500);
}
