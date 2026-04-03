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
            ap.total_points,
            ap.badge_level,
            (SELECT COUNT(*) FROM event_attendances WHERE user_id = :user_id AND status = 'attended') as events_attended,
            (SELECT COUNT(*) FROM reward_redemptions WHERE user_id = :user_id AND status = 'claimed') as rewards_claimed
        FROM alumni_profiles ap
        WHERE ap.user_id = :user_id
    ");
    $stmt->execute(['user_id' => $user['id']]);
    $summary = $stmt->fetch();
    
    if (!$summary) {
        $summary = [
            'total_points' => 0,
            'badge_level' => 'Bronze',
            'events_attended' => 0,
            'rewards_claimed' => 0
        ];
    }
    
    // Get rank
    $stmt = $db->prepare("
        SELECT COUNT(*) + 1 as rank
        FROM alumni_profiles 
        WHERE total_points > (
            SELECT COALESCE(total_points, 0) FROM alumni_profiles WHERE user_id = :user_id
        )
    ");
    $stmt->execute(['user_id' => $user['id']]);
    $summary['rank'] = $stmt->fetchColumn() ?: 1;
    
    respondSuccess($summary);
    
} catch (Exception $e) {
    respondError('Failed to load points: ' . $e->getMessage(), 500);
}
