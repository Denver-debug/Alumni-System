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
    
    $whereConditions = ["u.role = 'alumni'"];
    $params = [];
    
    if (!empty($_GET['college_id'])) {
        $whereConditions[] = "ap.college_id = ?";
        $params[] = intval($_GET['college_id']);
    }
    
    if (!empty($_GET['program_id'])) {
        $whereConditions[] = "ap.program_id = ?";
        $params[] = intval($_GET['program_id']);
    }
    
    if (!empty($_GET['batch_year'])) {
        $whereConditions[] = "ap.batch_year = ?";
        $params[] = intval($_GET['batch_year']);
    }
    
    $whereClause = implode(' AND ', $whereConditions);
    
    $sql = "
        SELECT u.alumni_id, u.name, u.email, u.status, u.created_at,
            ap.batch_year, ap.graduation_year, ap.phone, ap.city, ap.country,
            ap.company, ap.job_title, ap.total_points, ap.badge_level,
            c.name as college, p.name as program, s.name as section
        FROM users u
        LEFT JOIN alumni_profiles ap ON u.id = ap.user_id
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
    
    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    fputcsv($output, ['Alumni ID', 'Name', 'Email', 'Status', 'Registered', 'Batch Year', 
                      'Graduation Year', 'Phone', 'City', 'Country', 'Company', 'Job Title',
                      'Points', 'Badge', 'College', 'Program', 'Section']);
    
    foreach ($alumni as $row) {
        fputcsv($output, array_values($row));
    }
    
    fclose($output);
    
} catch (PDOException $e) {
    error_log("Admin Export Error: " . $e->getMessage());
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
