<?php
/**
 * Gamification API - Get My Points
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../utils/helpers.php';
require_once __DIR__ . '/../../middleware/auth.php';

requireAuth();

try {
    $db = Database::getInstance()->getConnection();
    $user = getCurrentUser();
    
    // Get points summary
    $stmt = $db->prepare("
        SELECT 
            COALESCE(ap.total_points, 0) as total_points,
            COALESCE(ap.badge_level, 'bronze') as badge_level,
            (SELECT COUNT(*) FROM event_attendances WHERE user_id = :attendance_user_id) as events_attended,
            (SELECT COUNT(*) FROM reward_redemptions WHERE user_id = :redemption_user_id AND status = 'claimed') as rewards_claimed
        FROM alumni_profiles ap
        WHERE ap.user_id = :profile_user_id
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
    
    // Get rank
    $currentPointsStmt = $db->prepare("SELECT COALESCE(total_points, 0) FROM alumni_profiles WHERE user_id = :user_id LIMIT 1");
    $currentPointsStmt->execute(['user_id' => $user['id']]);
    $currentPoints = (int)($currentPointsStmt->fetchColumn() ?: 0);

    $stmt = $db->prepare("
        SELECT COUNT(*) + 1 as user_rank
        FROM alumni_profiles 
        WHERE total_points > :current_points
    ");
    $stmt->execute(['current_points' => $currentPoints]);
    $summary['rank'] = $stmt->fetchColumn() ?: 1;
    
    respondSuccess($summary);
    
} catch (Exception $e) {
    respondError('Failed to load points: ' . $e->getMessage(), 500);
}
