<?php
/**
 * Admin Events List API
 * GET /api/admin/events - List all events with filters
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
    
    if (!empty($_GET['search'])) {
        $search = '%' . $_GET['search'] . '%';
        $whereConditions[] = "(e.title LIKE ? OR e.description LIKE ?)";
        $params[] = $search;
        $params[] = $search;
    }
    
    if (!empty($_GET['status']) && in_array($_GET['status'], ['draft', 'published', 'cancelled', 'completed'])) {
        $whereConditions[] = "e.status = ?";
        $params[] = $_GET['status'];
    }
    
    if (!empty($_GET['event_type'])) {
        $whereConditions[] = "e.event_type = ?";
        $params[] = $_GET['event_type'];
    }
    
    if (!empty($_GET['date_from'])) {
        $whereConditions[] = "e.event_date >= ?";
        $params[] = $_GET['date_from'];
    }
    
    if (!empty($_GET['date_to'])) {
        $whereConditions[] = "e.event_date <= ?";
        $params[] = $_GET['date_to'];
    }
    
    $whereClause = implode(' AND ', $whereConditions);
    
    // Count
    $countStmt = $db->prepare("SELECT COUNT(*) as total FROM events e WHERE $whereClause");
    $countStmt->execute($params);
    $total = $countStmt->fetch()['total'];
    
    // Fetch events with stats
    $sql = "
        SELECT e.*,
            u.name as created_by_name,
            (SELECT COUNT(*) FROM event_attendances WHERE event_id = e.id AND status = 'registered') as registered_count,
            (SELECT COUNT(*) FROM event_attendances WHERE event_id = e.id AND status = 'attended') as attended_count
        FROM events e
        LEFT JOIN users u ON e.created_by = u.id
        WHERE $whereClause
        ORDER BY e.event_date DESC
        LIMIT ? OFFSET ?
    ";
    
    $params[] = $limit;
    $params[] = $offset;
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $events = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'data' => [
            'events' => $events,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => intval($total),
                'total_pages' => ceil($total / $limit)
            ]
        ]
    ]);
    
} catch (PDOException $e) {
    error_log("Admin Events List Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
