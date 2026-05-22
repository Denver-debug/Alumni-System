<?php
/**
 * Admin Announcements List API
 * GET /api/admin/announcements - List all announcements
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../middleware/auth.php';

header('Content-Type: application/json');

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get current admin user for campus filtering
$currentUser = getCurrentUser();
$userRole = $currentUser['role'] ?? 'alumni';
$userCampusId = $currentUser['campus_id'] ?? null;

if (in_array($userRole, ['campus_admin', 'staff'], true) && !$userCampusId) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Campus assignment required']);
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $limit = isset($_GET['limit']) ? min(100, max(10, intval($_GET['limit']))) : 20;
    $offset = ($page - 1) * $limit;
    
    $whereConditions = ['1=1'];
    $params = [];
    
    // Campus-based filtering for campus_admin and staff
    if (in_array($userRole, ['campus_admin', 'staff']) && $userCampusId) {
        $whereConditions[] = "(a.campus_id = ? OR a.campus_id IS NULL OR EXISTS (SELECT 1 FROM announcement_campuses ac WHERE ac.announcement_id = a.id AND ac.campus_id = ?))";
        $params[] = $userCampusId;
        $params[] = $userCampusId;
    }
    
    if (!empty($_GET['status']) && in_array($_GET['status'], ['draft', 'published', 'archived'])) {
        $whereConditions[] = "a.status = ?";
        $params[] = $_GET['status'];
    }
    
    if (!empty($_GET['priority'])) {
        $whereConditions[] = "a.priority = ?";
        $params[] = $_GET['priority'];
    }
    
    $whereClause = implode(' AND ', $whereConditions);
    
    $countStmt = $db->prepare("SELECT COUNT(*) as total FROM announcements a WHERE $whereClause");
    $countStmt->execute($params);
    $total = $countStmt->fetch()['total'];
    
    $sql = "
        SELECT a.*, u.name as created_by_name,
            COALESCE((SELECT GROUP_CONCAT(DISTINCT c2.name ORDER BY c2.name SEPARATOR ', ') FROM announcement_campuses ac JOIN campuses c2 ON c2.id = ac.campus_id WHERE ac.announcement_id = a.id), c.name) as campus_names,
            c.name as campus_name,
            (SELECT COUNT(*) FROM announcement_reads WHERE announcement_id = a.id) as read_count
        FROM announcements a
        LEFT JOIN users u ON a.created_by = u.id
        LEFT JOIN campuses c ON a.campus_id = c.id
        WHERE $whereClause
        ORDER BY a.is_pinned DESC, a.created_at DESC
        LIMIT ? OFFSET ?
    ";
    
    $params[] = $limit;
    $params[] = $offset;
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $announcements = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'data' => [
            'announcements' => $announcements,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => intval($total),
                'total_pages' => ceil($total / $limit)
            ]
        ]
    ]);
    
} catch (PDOException $e) {
    error_log("Admin Announcements List Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
