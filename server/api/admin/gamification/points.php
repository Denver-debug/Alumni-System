<?php
/**
 * Admin Gamification Points API
 * GET /api/admin/gamification/points
 * POST /api/admin/gamification/points/adjust
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../middleware/auth.php';

header('Content-Type: application/json');

$admin = requireAdmin();

try {
    $db = Database::getInstance()->getConnection();

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $stats = [];

        $stmt = $db->query("SELECT COALESCE(SUM(points), 0) as total FROM point_transactions WHERE type IN ('earned', 'bonus')");
        $stats['total_earned'] = (int)($stmt->fetch()['total'] ?? 0);

        $stmt = $db->query("SELECT COALESCE(SUM(points), 0) as total FROM point_transactions WHERE type = 'redeemed'");
        $stats['total_redeemed'] = (int)($stmt->fetch()['total'] ?? 0);

        $stmt = $db->query(
            "SELECT badge_level, COUNT(*) as count
             FROM alumni_profiles
             GROUP BY badge_level"
        );
        $stats['badge_distribution'] = $stmt->fetchAll();

        $stmt = $db->query(
            "SELECT u.id, u.name, u.alumni_id, u.profile_image, ap.total_points, ap.badge_level
             FROM users u
             JOIN alumni_profiles ap ON u.id = ap.user_id
             WHERE u.role = 'alumni'
             ORDER BY ap.total_points DESC
             LIMIT 10"
        );
        $stats['top_earners'] = $stmt->fetchAll();

        $stmt = $db->query(
            "SELECT pt.*, u.name, u.alumni_id,
                    e.title as event_title,
                    rw.name as reward_name
             FROM point_transactions pt
             JOIN users u ON pt.user_id = u.id
             LEFT JOIN events e ON pt.reference_type = 'event' AND pt.reference_id = e.id
             LEFT JOIN rewards rw ON pt.reference_type IN ('reward', 'redemption') AND pt.reference_id = rw.id
             ORDER BY pt.created_at DESC
             LIMIT 20"
        );
        $stats['recent_transactions'] = $stmt->fetchAll();

        echo json_encode(['success' => true, 'data' => $stats]);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true) ?? [];

        if (empty($data['user_id']) || !isset($data['points'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'user_id and points are required']);
            exit;
        }

        $userId = (int)$data['user_id'];
        $requestedDelta = (int)$data['points'];
        $reason = trim((string)($data['reason'] ?? 'Admin adjustment'));

        $stmt = $db->prepare('SELECT id FROM users WHERE id = ? LIMIT 1');
        $stmt->execute([$userId]);
        if (!$stmt->fetch()) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'User not found']);
            exit;
        }

        $db->beginTransaction();

        $stmt = $db->prepare(
            "INSERT INTO alumni_profiles (user_id, total_points, badge_level, created_at, updated_at)
             VALUES (?, 0, 'bronze', NOW(), NOW())
             ON DUPLICATE KEY UPDATE updated_at = NOW()"
        );
        $stmt->execute([$userId]);

        $stmt = $db->prepare('SELECT COALESCE(total_points, 0) as total_points FROM alumni_profiles WHERE user_id = ? FOR UPDATE');
        $stmt->execute([$userId]);
        $beforePoints = (int)($stmt->fetch()['total_points'] ?? 0);

        $stmt = $db->prepare('UPDATE alumni_profiles SET total_points = GREATEST(total_points + ?, 0), updated_at = NOW() WHERE user_id = ?');
        $stmt->execute([$requestedDelta, $userId]);

        $stmt = $db->prepare('SELECT COALESCE(total_points, 0) as total_points FROM alumni_profiles WHERE user_id = ?');
        $stmt->execute([$userId]);
        $afterPoints = (int)($stmt->fetch()['total_points'] ?? 0);

        $actualDelta = $afterPoints - $beforePoints;
        $badgeLevel = BadgeLevel::getForPoints($afterPoints);

        $stmt = $db->prepare('UPDATE alumni_profiles SET badge_level = ?, updated_at = NOW() WHERE user_id = ?');
        $stmt->execute([$badgeLevel, $userId]);

        if ($actualDelta !== 0) {
            $type = $actualDelta > 0 ? 'bonus' : 'penalty';
            $pointsValue = abs($actualDelta);

            $stmt = $db->prepare(
                "INSERT INTO point_transactions (
                    user_id, points, type, source, description, balance_after, created_at
                ) VALUES (
                    ?, ?, ?, 'admin_bonus', ?, ?, NOW()
                )"
            );
            $stmt->execute([
                $userId,
                $pointsValue,
                $type,
                $reason . ' (by ' . ($admin['name'] ?? 'Admin') . ')',
                $afterPoints,
            ]);
        }

        $db->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Points adjusted',
            'data' => [
                'user_id' => $userId,
                'before_points' => $beforePoints,
                'after_points' => $afterPoints,
                'delta' => $actualDelta,
                'badge_level' => $badgeLevel,
            ],
        ]);
        exit;
    }

    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
} catch (PDOException $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    error_log('Admin Points Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}

