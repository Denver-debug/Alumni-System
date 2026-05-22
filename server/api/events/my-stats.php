<?php
/**
 * Events API - My Event Stats
 * GET /api/events/my-stats
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../utils/helpers.php';
require_once __DIR__ . '/../../middleware/auth.php';

requireAuth();

try {
    $db = Database::getInstance()->getConnection();
    $user = getCurrentUser();

    $stmt = $db->prepare("
        SELECT
            (SELECT COUNT(*)
             FROM event_rsvps r
             INNER JOIN events e ON e.id = r.event_id
             WHERE r.user_id = :upcoming_user_id
               AND r.status = 'going'
               AND e.event_date >= CURDATE()) as upcoming,
            (SELECT COUNT(*)
             FROM event_rsvps r
             WHERE r.user_id = :registered_user_id
               AND r.status IN ('going', 'maybe')) as registered,
            (SELECT COUNT(*)
             FROM event_attendances a
             WHERE a.user_id = :attended_user_id) as attended,
            (SELECT COALESCE(SUM(a.points_awarded), 0)
             FROM event_attendances a
             WHERE a.user_id = :points_user_id) as points_earned
    ");
    $stmt->execute([
        'upcoming_user_id' => $user['id'],
        'registered_user_id' => $user['id'],
        'attended_user_id' => $user['id'],
        'points_user_id' => $user['id']
    ]);
    $stats = $stmt->fetch() ?: [];

    $normalized = [
        'upcoming' => (int) ($stats['upcoming'] ?? 0),
        'registered' => (int) ($stats['registered'] ?? 0),
        'attended' => (int) ($stats['attended'] ?? 0),
        'points_earned' => (int) ($stats['points_earned'] ?? 0)
    ];

    respondSuccess($normalized);
} catch (Exception $e) {
    respondError('Failed to load event stats: ' . $e->getMessage(), 500);
}
