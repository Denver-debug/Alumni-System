<?php
/**
 * Admin Gamification Rewards API
 * GET /api/admin/gamification/rewards - List rewards
 * POST /api/admin/gamification/rewards - Create reward
 * PUT /api/admin/gamification/rewards/{id} - Update reward
 * DELETE /api/admin/gamification/rewards/{id} - Delete reward
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../middleware/auth.php';

header('Content-Type: application/json');

requireAdmin();

$method = $_SERVER['REQUEST_METHOD'];
$rewardId = $GLOBALS['url_params']['id'] ?? null;

try {
    $db = Database::getInstance()->getConnection();
    
    switch ($method) {
        case 'GET':
            if ($rewardId) {
                $stmt = $db->prepare("
                    SELECT r.*,
                        (SELECT COUNT(*) FROM reward_redemptions WHERE reward_id = r.id) as redemption_count
                    FROM rewards r WHERE r.id = ?
                ");
                $stmt->execute([$rewardId]);
                $reward = $stmt->fetch();
                
                if (!$reward) {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'message' => 'Reward not found']);
                    exit;
                }
                
                // Get recent redemptions
                $stmt = $db->prepare("
                    SELECT rr.*, u.name, u.email, u.alumni_id
                    FROM reward_redemptions rr
                    JOIN users u ON rr.user_id = u.id
                    WHERE rr.reward_id = ?
                    ORDER BY rr.created_at DESC
                    LIMIT 20
                ");
                $stmt->execute([$rewardId]);
                $reward['redemptions'] = $stmt->fetchAll();
                
                echo json_encode(['success' => true, 'data' => $reward]);
            } else {
                $stmt = $db->query("
                    SELECT r.*,
                        (SELECT COUNT(*) FROM reward_redemptions WHERE reward_id = r.id) as redemption_count
                    FROM rewards r
                    ORDER BY r.points_cost ASC
                ");
                $rewards = $stmt->fetchAll();
                
                echo json_encode(['success' => true, 'data' => $rewards]);
            }
            break;
            
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (empty($data['name']) || empty($data['points_cost'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Name and points_cost are required']);
                exit;
            }
            
            $stmt = $db->prepare("
                INSERT INTO rewards (name, description, points_cost, quantity_available, image_url, status, created_at)
                VALUES (?, ?, ?, ?, ?, 'active', NOW())
            ");
            $stmt->execute([
                trim($data['name']),
                $data['description'] ?? null,
                intval($data['points_cost']),
                $data['quantity_available'] ?? null,
                $data['image_url'] ?? null
            ]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Reward created',
                'data' => ['id' => $db->lastInsertId()]
            ]);
            break;
            
        case 'PUT':
            if (!$rewardId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Reward ID required']);
                exit;
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            $updates = [];
            $params = [];
            
            foreach (['name', 'description', 'points_cost', 'quantity_available', 'image_url', 'status'] as $field) {
                if (isset($data[$field])) {
                    $updates[] = "$field = ?";
                    $params[] = $data[$field];
                }
            }
            
            if (!empty($updates)) {
                $updates[] = "updated_at = NOW()";
                $params[] = $rewardId;
                $stmt = $db->prepare("UPDATE rewards SET " . implode(', ', $updates) . " WHERE id = ?");
                $stmt->execute($params);
            }
            
            echo json_encode(['success' => true, 'message' => 'Reward updated']);
            break;
            
        case 'DELETE':
            if (!$rewardId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Reward ID required']);
                exit;
            }
            
            $stmt = $db->prepare("UPDATE rewards SET status = 'inactive' WHERE id = ?");
            $stmt->execute([$rewardId]);
            
            echo json_encode(['success' => true, 'message' => 'Reward deactivated']);
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    }
    
} catch (PDOException $e) {
    error_log("Admin Rewards Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
