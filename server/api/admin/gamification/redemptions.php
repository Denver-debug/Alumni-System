<?php
/**
 * Admin Redemptions API
 * GET /api/admin/gamification/redemptions - List all redemptions
 * PUT /api/admin/gamification/redemptions/{id} - Update redemption status
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../middleware/auth.php';

header('Content-Type: application/json');

requireAdmin();

$method = $_SERVER['REQUEST_METHOD'];
$redemptionId = $GLOBALS['url_params']['id'] ?? null;

try {
    $db = Database::getInstance()->getConnection();
    
    if ($method === 'GET') {
        $status = $_GET['status'] ?? null;
        
        $where = $status ? "WHERE rr.status = ?" : "";
        $params = $status ? [$status] : [];
        
        $stmt = $db->prepare("
            SELECT rr.*, r.name as reward_name, r.points_cost,
                u.name as user_name, u.email, u.alumni_id
            FROM reward_redemptions rr
            JOIN rewards r ON rr.reward_id = r.id
            JOIN users u ON rr.user_id = u.id
            $where
            ORDER BY rr.created_at DESC
        ");
        $stmt->execute($params);
        $redemptions = $stmt->fetchAll();
        
        echo json_encode(['success' => true, 'data' => $redemptions]);
        
    } elseif ($method === 'PUT') {
        if (!$redemptionId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Redemption ID required']);
            exit;
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        $newStatus = $data['status'] ?? null;
        
        if (!in_array($newStatus, ['pending', 'approved', 'claimed', 'rejected', 'expired'], true)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid status']);
            exit;
        }
        
        $db->beginTransaction();
        
        // Get redemption
        $stmt = $db->prepare("SELECT * FROM reward_redemptions WHERE id = ?");
        $stmt->execute([$redemptionId]);
        $redemption = $stmt->fetch();
        
        if (!$redemption) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Redemption not found']);
            exit;
        }
        
        // If rejecting, refund points
        if ($newStatus === 'rejected' && $redemption['status'] !== 'rejected') {
            $refund = (int)($redemption['points_spent'] ?? 0);

            $stmt = $db->prepare("UPDATE alumni_profiles SET total_points = total_points + ?, updated_at = NOW() WHERE user_id = ?");
            $stmt->execute([$refund, $redemption['user_id']]);

            $stmt = $db->prepare('SELECT COALESCE(total_points, 0) FROM alumni_profiles WHERE user_id = ?');
            $stmt->execute([$redemption['user_id']]);
            $balanceAfter = (int)$stmt->fetchColumn();

            $stmt = $db->prepare("\n                INSERT INTO point_transactions (\n                    user_id, points, type, source, reference_id, reference_type, description, balance_after, created_at\n                ) VALUES (\n                    ?, ?, 'bonus', 'reward_redemption', ?, 'redemption', 'Redemption refund', ?, NOW()\n                )\n            ");
            $stmt->execute([$redemption['user_id'], $refund, $redemptionId, $balanceAfter]);
        }

        if ($newStatus === 'claimed') {
            $stmt = $db->prepare("UPDATE reward_redemptions SET status = ?, claimed_at = NOW(), updated_at = NOW() WHERE id = ?");
            $stmt->execute([$newStatus, $redemptionId]);
        } else {
            $stmt = $db->prepare("UPDATE reward_redemptions SET status = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$newStatus, $redemptionId]);
        }
        
        $db->commit();
        
        echo json_encode(['success' => true, 'message' => 'Redemption updated']);
        
    } else {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    }
    
} catch (PDOException $e) {
    if (isset($db) && $db->inTransaction()) $db->rollBack();
    error_log("Admin Redemptions Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
