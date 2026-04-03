<?php
/**
 * Admin Alumni List API
 * GET /api/admin/alumni - List all alumni with filters
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
    
    // Pagination
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $limit = isset($_GET['limit']) ? min(100, max(10, intval($_GET['limit']))) : 20;
    $offset = ($page - 1) * $limit;
    
    // Build query with filters
    $whereConditions = ["u.role = 'alumni'"];
    $params = [];
    
    // Search filter
    if (!empty($_GET['search'])) {
        $search = '%' . $_GET['search'] . '%';
        $whereConditions[] = "(u.name LIKE ? OR u.email LIKE ? OR u.alumni_id LIKE ?)";
        $params[] = $search;
        $params[] = $search;
        $params[] = $search;
    }
    
    // Status filter
    if (!empty($_GET['status']) && in_array($_GET['status'], ['active', 'inactive', 'blocked'])) {
        $whereConditions[] = "u.status = ?";
        $params[] = $_GET['status'];
    }
    
    // College filter
    if (!empty($_GET['college_id'])) {
        $whereConditions[] = "ap.college_id = ?";
        $params[] = intval($_GET['college_id']);
    }
    
    // Program filter
    if (!empty($_GET['program_id'])) {
        $whereConditions[] = "ap.program_id = ?";
        $params[] = intval($_GET['program_id']);
    }
    
    // Section filter
    if (!empty($_GET['section_id'])) {
        $whereConditions[] = "ap.section_id = ?";
        $params[] = intval($_GET['section_id']);
    }
    
    // Batch year filter
    if (!empty($_GET['batch_year'])) {
        $whereConditions[] = "ap.batch_year = ?";
        $params[] = intval($_GET['batch_year']);
    }
    
    $whereClause = implode(' AND ', $whereConditions);
    
    // Sorting
    $sortColumn = 'u.created_at';
    $sortDir = 'DESC';
    
    $allowedSorts = [
        'name' => 'u.name',
        'email' => 'u.email',
        'alumni_id' => 'u.alumni_id',
        'created_at' => 'u.created_at',
        'points' => 'ap.total_points',
        'batch_year' => 'ap.batch_year'
    ];
    
    if (!empty($_GET['sort']) && isset($allowedSorts[$_GET['sort']])) {
        $sortColumn = $allowedSorts[$_GET['sort']];
    }
    
    if (!empty($_GET['order']) && strtoupper($_GET['order']) === 'ASC') {
        $sortDir = 'ASC';
    }
    
    // Count total
    $countSql = "SELECT COUNT(*) as total FROM users u LEFT JOIN alumni_profiles ap ON u.id = ap.user_id WHERE $whereClause";
    $countStmt = $db->prepare($countSql);
    $countStmt->execute($params);
    $total = $countStmt->fetch()['total'];
    
    // Fetch alumni
    $sql = "
        SELECT 
            u.id, u.alumni_id, u.name, u.email, u.profile_image, u.status,
            u.email_verified, u.last_login, u.created_at,
            ap.batch_year, ap.graduation_year, ap.total_points, ap.badge_level,
            c.name as college_name, c.code as college_code,
            p.name as program_name, p.code as program_code,
            s.name as section_name
        FROM users u
        LEFT JOIN alumni_profiles ap ON u.id = ap.user_id
        LEFT JOIN colleges c ON ap.college_id = c.id
        LEFT JOIN programs p ON ap.program_id = p.id
        LEFT JOIN sections s ON ap.section_id = s.id
        WHERE $whereClause
        ORDER BY $sortColumn $sortDir
        LIMIT ? OFFSET ?
    ";
    
    $params[] = $limit;
    $params[] = $offset;
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $alumni = $stmt->fetchAll();
    
    $totalPages = ceil($total / $limit);
    
    echo json_encode([
        'success' => true,
        'data' => [
            'alumni' => $alumni,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => intval($total),
                'total_pages' => $totalPages,
                'has_next' => $page < $totalPages,
                'has_prev' => $page > 1
            ]
        ]
    ]);
    
} catch (PDOException $e) {
    error_log("Admin Alumni List Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
