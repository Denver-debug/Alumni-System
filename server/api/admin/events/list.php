<?php
/**
 * Admin Events List API
 * GET /api/admin/events - List all events with filters
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../utils/helpers.php';
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

try {
    $db = Database::getInstance()->getConnection();
    syncEventStatuses($db);
    
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $limit = isset($_GET['limit']) ? min(100, max(10, intval($_GET['limit']))) : 20;
    $offset = ($page - 1) * $limit;
    
    $whereConditions = ['1=1'];
    $params = [];
    
    // Campus-based filtering for campus_admin and staff
    if (in_array($userRole, ['campus_admin', 'staff']) && $userCampusId) {
        $whereConditions[] = "(e.campus_id = ? OR e.campus_id IS NULL OR EXISTS (SELECT 1 FROM event_campuses ec WHERE ec.event_id = e.id AND ec.campus_id = ?))";
        $params[] = $userCampusId;
        $params[] = $userCampusId;
    }
    
    if (!empty($_GET['search'])) {
        $search = '%' . $_GET['search'] . '%';
        $whereConditions[] = "(e.title LIKE ? OR e.description LIKE ?)";
        $params[] = $search;
        $params[] = $search;
    }
    
    if (!empty($_GET['status']) && in_array($_GET['status'], ['draft', 'upcoming', 'ongoing', 'cancelled', 'completed'])) {
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
    
    // Month filter support: YYYY-MM format
    if (!empty($_GET['month'])) {
        $month = $_GET['month']; // Expected format: YYYY-MM
        if (preg_match('/^\d{4}-\d{2}$/', $month)) {
            $whereConditions[] = "DATE_FORMAT(e.event_date, '%Y-%m') = ?";
            $params[] = $month;
        }
    }
    
    $whereClause = implode(' AND ', $whereConditions);
    
    // Count
    $countStmt = $db->prepare("SELECT COUNT(*) as total FROM events e WHERE $whereClause");
    $countStmt->execute($params);
    $total = $countStmt->fetch()['total'];
    
    // Support sort/order parameters
    $sortField = 'e.event_date';
    $sortOrder = 'DESC';
    
    if (!empty($_GET['sort'])) {
        $validFields = ['title', 'event_date', 'status', 'registered_count', 'created_at'];
        $sortField = in_array($_GET['sort'], $validFields) ? "e.{$_GET['sort']}" : 'e.event_date';
    }
    
    if (!empty($_GET['order']) && in_array(strtoupper($_GET['order']), ['ASC', 'DESC'])) {
        $sortOrder = strtoupper($_GET['order']);
    }
    
    // Fetch events with stats
    $sql = "
        SELECT e.*,
            u.name as created_by_name,
            COALESCE((SELECT GROUP_CONCAT(DISTINCT c.name ORDER BY c.name SEPARATOR ', ') FROM event_campuses ec JOIN campuses c ON c.id = ec.campus_id WHERE ec.event_id = e.id), c.name) as campus_names,
            (SELECT COUNT(*) FROM event_rsvps WHERE event_id = e.id AND status IN ('going', 'maybe')) as registered_count,
            (SELECT COUNT(*) FROM event_attendances WHERE event_id = e.id) as attended_count
        FROM events e
        LEFT JOIN users u ON e.created_by = u.id
        LEFT JOIN campuses c ON e.campus_id = c.id
        WHERE $whereClause
        ORDER BY {$sortField} {$sortOrder}
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
