<?php
/**
 * Admin Export Alumni API
 * GET /api/admin/alumni/export - Export alumni data to CSV
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../middleware/auth.php';

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    $currentUser = getCurrentUser();
    $userRole = $currentUser['role'] ?? 'alumni';
    $userCampusId = $currentUser['campus_id'] ?? null;

    if (in_array($userRole, ['campus_admin', 'staff'], true) && !$userCampusId) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Campus assignment required']);
        exit;
    }
    
    $whereConditions = ["u.role = 'alumni'"];
    $params = [];
    
    // Campus filter
    if (in_array($userRole, ['campus_admin', 'staff'], true) && $userCampusId) {
        $whereConditions[] = "COALESCE(ap.campus_id, u.campus_id) = ?";
        $params[] = $userCampusId;
    }

    if (!empty($_GET['campus_id'])) {
        $whereConditions[] = "COALESCE(ap.campus_id, u.campus_id) = ?";
        $params[] = intval($_GET['campus_id']);
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
    
    // Batch year filter
    if (!empty($_GET['batch_year'])) {
        $whereConditions[] = "ap.batch_year = ?";
        $params[] = intval($_GET['batch_year']);
    }
    
    // Status filter
    if (!empty($_GET['status'])) {
        $whereConditions[] = "u.status = ?";
        $params[] = $_GET['status'];
    }
    
    // Search filter
    if (!empty($_GET['search'])) {
        $searchTerm = '%' . $_GET['search'] . '%';
        $whereConditions[] = "(u.name LIKE ? OR u.email LIKE ? OR u.alumni_id LIKE ?)";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }
    
    $whereClause = implode(' AND ', $whereConditions);
    
    $sql = "
        SELECT u.alumni_id, u.name, u.email, u.status, u.created_at,
            ap.batch_year, ap.graduation_year, ap.phone,
            ap.address_city AS city, ap.address_country AS country,
            ap.current_employer AS company, ap.job_title, ap.total_points, ap.badge_level,
            campus.name as campus, campus.code as campus_code,
            c.name as college, c.code as college_code,
            p.name as program, p.code as program_code,
            s.name as section
        FROM users u
        LEFT JOIN alumni_profiles ap ON u.id = ap.user_id
        LEFT JOIN campuses campus ON campus.id = COALESCE(ap.campus_id, u.campus_id)
        LEFT JOIN colleges c ON ap.college_id = c.id
        LEFT JOIN programs p ON ap.program_id = p.id
        LEFT JOIN sections s ON ap.section_id = s.id
        WHERE $whereClause ORDER BY u.created_at DESC
    ";
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $alumni = $stmt->fetchAll();
    
    $filename = 'alumni_export_' . date('Y-m-d_His') . '.csv';
    
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    fputcsv($output, ['Alumni ID', 'Name', 'Email', 'Status', 'Registered', 'Batch Year', 
                      'Graduation Year', 'Phone', 'City', 'Country', 'Company', 'Job Title',
                      'Points', 'Badge', 'Campus', 'Campus Code', 'College', 'College Code', 
                      'Program', 'Program Code', 'Section']);
    
    foreach ($alumni as $row) {
        fputcsv($output, array_values($row));
    }
    
    fclose($output);
    exit;
    
} catch (PDOException $e) {
    error_log("Admin Export Error: " . $e->getMessage());
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
