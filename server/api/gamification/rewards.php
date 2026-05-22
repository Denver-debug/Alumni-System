<?php
/**
 * Gamification API - Rewards Catalog
 * GET /api/gamification/rewards
 * GET /api/rewards
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../utils/helpers.php';
require_once __DIR__ . '/../../middleware/auth.php';

requireAuth();

try {
    $db = Database::getInstance()->getConnection();
    $user = getCurrentUser();

    $stmt = $db->prepare('SELECT COALESCE(total_points, 0) as total_points FROM alumni_profiles WHERE user_id = ? LIMIT 1');
    $stmt->execute([$user['id']]);
    $myPoints = (int) ($stmt->fetch()['total_points'] ?? 0);

    $stmt = $db->query("\n        SELECT r.*,\n            (SELECT COUNT(*) FROM reward_redemptions rr WHERE rr.reward_id = r.id AND rr.status IN ('pending','approved','claimed')) as redeemed_count\n        FROM rewards r\n        WHERE r.status = 'active'\n        ORDER BY r.points_cost ASC\n    ");

    $rewards = $stmt->fetchAll();

    foreach ($rewards as &$reward) {
        $available = $reward['quantity_available'] === null
            ? null
            : max(0, (int) $reward['quantity_available'] - (int) $reward['redeemed_count']);

        $reward['remaining_quantity'] = $available;
        $reward['can_redeem'] = $myPoints >= (int) $reward['points_cost'] && ($available === null || $available > 0);
    }

    respondSuccess($rewards);
} catch (Exception $e) {
    respondError('Failed to load rewards: ' . $e->getMessage(), 500);
}
