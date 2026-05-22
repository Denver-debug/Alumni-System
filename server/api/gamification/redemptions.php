<?php
/**
 * Gamification API - My Redemptions
 * GET /api/gamification/redemptions
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../utils/helpers.php';
require_once __DIR__ . '/../../middleware/auth.php';

requireAuth();

try {
    $db = Database::getInstance()->getConnection();
    $user = getCurrentUser();

    $stmt = $db->prepare("\n        SELECT rr.*, r.name as reward_name, r.points_cost, r.image_url\n        FROM reward_redemptions rr\n        JOIN rewards r ON r.id = rr.reward_id\n        WHERE rr.user_id = ?\n        ORDER BY rr.created_at DESC\n    ");
    $stmt->execute([$user['id']]);

    respondSuccess($stmt->fetchAll());
} catch (Exception $e) {
    respondError('Failed to load redemptions: ' . $e->getMessage(), 500);
}
