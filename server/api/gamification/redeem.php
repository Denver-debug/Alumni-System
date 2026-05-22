<?php
/**
 * Gamification API - Redeem Reward
 * POST /api/gamification/rewards/{id}/redeem
 * POST /api/rewards/{id}/redeem
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../utils/helpers.php';
require_once __DIR__ . '/../../middleware/auth.php';

requireAuth();

try {
    $db = Database::getInstance()->getConnection();
    $user = getCurrentUser();

    $rewardId = $GLOBALS['url_params']['id'] ?? ($_GET['id'] ?? null);
    if (!$rewardId) {
        respondError('Reward ID required', 400);
    }

    $stmt = $db->prepare('SELECT * FROM rewards WHERE id = ? AND status = \'active\' LIMIT 1');
    $stmt->execute([$rewardId]);
    $reward = $stmt->fetch();

    if (!$reward) {
        respondError('Reward not found or inactive', 404);
    }

    $stmt = $db->prepare('SELECT COALESCE(total_points, 0) as total_points FROM alumni_profiles WHERE user_id = ? LIMIT 1');
    $stmt->execute([$user['id']]);
    $profile = $stmt->fetch();
    $totalPoints = (int) ($profile['total_points'] ?? 0);

    $cost = (int) $reward['points_cost'];
    if ($totalPoints < $cost) {
        respondError('Insufficient points', 400);
    }

    if ($reward['quantity_available'] !== null) {
        $stmt = $db->prepare("SELECT COUNT(*) FROM reward_redemptions WHERE reward_id = ? AND status IN ('pending','approved','claimed')");
        $stmt->execute([$rewardId]);
        $current = (int) $stmt->fetchColumn();

        if ($current >= (int) $reward['quantity_available']) {
            respondError('Reward is out of stock', 400);
        }
    }

    $db->beginTransaction();

    $stmt = $db->prepare('UPDATE alumni_profiles SET total_points = total_points - ?, updated_at = NOW() WHERE user_id = ?');
    $stmt->execute([$cost, $user['id']]);

        $stmt = $db->prepare("\n        INSERT INTO reward_redemptions (user_id, reward_id, points_spent, status, created_at, updated_at)\n        VALUES (?, ?, ?, 'pending', NOW(), NOW())\n    ");
        $stmt->execute([$user['id'], $rewardId, $cost]);
    $redemptionId = (int) $db->lastInsertId();

        $stmt = $db->prepare('SELECT COALESCE(total_points, 0) FROM alumni_profiles WHERE user_id = ?');
        $stmt->execute([$user['id']]);
        $balanceAfter = (int)$stmt->fetchColumn();

        $stmt = $db->prepare("\n        INSERT INTO point_transactions (\n            user_id, points, type, source, reference_id, reference_type, description, balance_after, created_at\n        ) VALUES (\n            ?, ?, 'redeemed', 'reward_redemption', ?, 'reward', ?, ?, NOW()\n        )\n    ");
    $stmt->execute([
        $user['id'],
        $cost,
            $rewardId,
            'Reward redeemed: ' . ($reward['name'] ?? 'Reward'),
            $balanceAfter,
    ]);

    $db->commit();

    respondSuccess([
        'redemption_id' => $redemptionId,
        'reward_id' => (int) $rewardId,
        'remaining_points' => max(0, $totalPoints - $cost),
        'message' => 'Reward redeemed successfully'
    ], 201);
} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    respondError('Failed to redeem reward: ' . $e->getMessage(), 500);
}
