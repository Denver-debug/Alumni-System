<?php
/**
 * Admin Gamification Points Management API
 * GET /api/admin/gamification/points - Get points overview
 * POST /api/admin/gamification/points/adjust - Adjust user points
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../middleware/auth.php';
require_once __DIR__ . '/../../../config/auth.php';

header('Content-Type: application/json');

requireAdmin();

try {
    $db = Database::getInstance()->getConnection();
    
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Get points overview
        $stats = [];
        
        // Total points distributed
        $stmt = $db->query("SELECT SUM(points) as total FROM point_transactions WHERE type = 'earned'");
        $stats['total_earned'] = intval($stmt->fetch()['total'] ?? 0);
        
        // Total points redeemed
        $stmt = $db->query("SELECT SUM(points) as total FROM point_transactions WHERE type = 'redeemed'");
        $stats['total_redeemed'] = intval($stmt->fetch()['total'] ?? 0);
        
        // Badge distribution
        $stmt = $db->query("
            SELECT badge_level, COUNT(*) as count
            FROM alumni_profiles
            GROUP BY badge_level
        ");
        $stats['badge_distribution'] = $stmt->fetchAll();
        
        // Top earners
        $stmt = $db->query("
            SELECT u.id, u.name, u.alumni_id, u.profile_image, ap.total_points, ap.badge_level
            FROM users u
            JOIN alumni_profiles ap ON u.id = ap.user_id
            WHERE u.role = 'alumni'
            ORDER BY ap.total_points DESC
            LIMIT 10
        ");
        $stats['top_earners'] = $stmt->fetchAll();
        
        // Recent transactions
        $stmt = $db->query("
            SELECT pt.*, u.name, u.alumni_id, e.title as event_title
            FROM point_transactions pt
            JOIN users u ON pt.user_id = u.id
            LEFT JOIN events e ON pt.event_id = e.id
            ORDER BY pt.created_at DESC
            LIMIT 20
        ");
        $stats['recent_transactions'] = $stmt->fetchAll();
        
        echo json_encode(['success' => true, 'data' => $stats]);
        
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (empty($data['user_id']) || !isset($data['points'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'user_id and points are required']);
            exit;
        }
        
        $userId = $data['user_id'];
        $points = intval($data['points']);
        $reason = $data['reason'] ?? 'Admin adjustment';
        
        $db->beginTransaction();
        
        // Update profile
        $stmt = $db->prepare("UPDATE alumni_profiles SET total_points = total_points + ?, updated_at = NOW() WHERE user_id = ?");
        $stmt->execute([$points, $userId]);
        
        // Log transaction
        $admin = getCurrentUser();
        $stmt = $db->prepare("
            INSERT INTO point_transactions (user_id, points, type, description, created_at)
            VALUES (?, ?, ?, ?, NOW())
        ");
        $stmt->execute([
            $userId,
            abs($points),
            $points > 0 ? 'earned' : 'redeemed',
            $reason . " (by " . $admin['name'] . ")"
        ]);
        
        // Update badge level
        $stmt = $db->prepare("SELECT total_points FROM alumni_profiles WHERE user_id = ?");
        $stmt->execute([$userId]);
        $totalPoints = $stmt->fetch()['total_points'] ?? 0;
        
        $badgeLevel = 'bronze';
        if ($totalPoints >= 5000) $badgeLevel = 'diamond';
        elseif ($totalPoints >= 1000) $badgeLevel = 'platinum';
        elseif ($totalPoints >= 500) $badgeLevel = 'gold';
        elseif ($totalPoints >= 100) $badgeLevel = 'silver';
        
        $stmt = $db->prepare("UPDATE alumni_profiles SET badge_level = ? WHERE user_id = ?");
        $stmt->execute([$badgeLevel, $userId]);
        
        $db->commit();
        
        echo json_encode(['success' => true, 'message' => 'Points adjusted']);
        
    } else {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    }
    
} catch (PDOException $e) {
    if (isset($db) && $db->inTransaction()) $db->rollBack();
    error_log("Admin Points Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
