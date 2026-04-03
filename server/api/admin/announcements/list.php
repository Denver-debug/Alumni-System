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

try {
    $db = Database::getInstance()->getConnection();
    
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $limit = isset($_GET['limit']) ? min(100, max(10, intval($_GET['limit']))) : 20;
    $offset = ($page - 1) * $limit;
    
    $whereConditions = ['1=1'];
    $params = [];
    
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
            (SELECT COUNT(*) FROM announcement_reads WHERE announcement_id = a.id) as read_count
        FROM announcements a
        LEFT JOIN users u ON a.created_by = u.id
        WHERE $whereClause
        ORDER BY a.is_pinned DESC, a.created_at DESC
        LIMIT ? OFFSET ?
    ";
    
    $params[] = $limit;
    $params[] = $offset;
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $announcements = $stmt->fetchAll();
    
    // Parse JSON fields
    foreach ($announcements as &$a) {
        foreach (['target_colleges', 'target_programs', 'target_batch_years'] as $field) {
            if ($a[$field]) $a[$field] = json_decode($a[$field], true);
        }
    }
    
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
