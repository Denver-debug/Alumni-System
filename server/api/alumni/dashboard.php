<?php
/**
 * Alumni API - Dashboard Summary
 * GET /api/alumni/dashboard
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
            COALESCE(ap.total_points, 0) as total_points,
            COALESCE(ap.badge_level, 'bronze') as badge_level,
            (SELECT COUNT(*) FROM event_attendances WHERE user_id = :attendance_user_id) as events_attended,
            (SELECT COUNT(*) FROM reward_redemptions WHERE user_id = :redemption_user_id AND status IN ('approved', 'claimed')) as rewards_claimed
        FROM users u
        LEFT JOIN alumni_profiles ap ON ap.user_id = u.id
        WHERE u.id = :profile_user_id
        LIMIT 1
    ");
    $stmt->execute([
        'attendance_user_id' => $user['id'],
        'redemption_user_id' => $user['id'],
        'profile_user_id' => $user['id']
    ]);
    $summary = $stmt->fetch();

    if (!$summary) {
        $summary = [
            'total_points' => 0,
            'badge_level' => 'bronze',
            'events_attended' => 0,
            'rewards_claimed' => 0
        ];
    }

    respondSuccess($summary);
} catch (Exception $e) {
    respondError('Failed to load dashboard: ' . $e->getMessage(), 500);
}
