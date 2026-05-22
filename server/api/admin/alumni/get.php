<?php
/**
 * Admin Get Alumni Details API
 * GET /api/admin/alumni/{id} - Get detailed alumni info
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

$userId = $GLOBALS['url_params']['id'] ?? null;
$currentUser = getCurrentUser();
$userRole = $currentUser['role'] ?? 'alumni';
$userCampusId = $currentUser['campus_id'] ?? null;

if (!$userId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Alumni ID required']);
    exit;
}

if (in_array($userRole, ['campus_admin', 'staff'], true) && !$userCampusId) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Campus assignment required']);
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    
    // Get user with full profile
    $sql = "
        SELECT 
            u.id, u.alumni_id, u.name, u.email, u.profile_image, u.status,
            u.email_verified, u.auth_provider, u.login_attempts, u.last_login, u.campus_id,
            u.created_at, u.updated_at,
            ap.batch_year, ap.graduation_year, ap.phone, ap.birthdate, ap.employment_status,
            ap.address_street, ap.address_barangay, ap.address_city, ap.address_province,
            ap.address_region, ap.address_zip, ap.address_country,
            ap.current_employer, ap.company_address, ap.job_title,
            ap.industry, ap.linkedin_url, ap.facebook_url, ap.twitter_url,
            ap.instagram_url, ap.total_points, ap.badge_level,
            cam.name as campus_name, cam.code as campus_code,
            c.id as college_id, c.name as college_name, c.code as college_code,
            p.id as program_id, p.name as program_name, p.code as program_code,
            s.id as section_id, s.name as section_name
        FROM users u
        LEFT JOIN alumni_profiles ap ON u.id = ap.user_id
        LEFT JOIN campuses cam ON u.campus_id = cam.id
        LEFT JOIN colleges c ON ap.college_id = c.id
        LEFT JOIN programs p ON ap.program_id = p.id
        LEFT JOIN sections s ON ap.section_id = s.id
        WHERE u.id = ? AND u.role = 'alumni'
    ";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([$userId]);
    $alumni = $stmt->fetch();
    
    if (!$alumni) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Alumni not found']);
        exit;
    }

    $alumniCampusId = (int)($alumni['campus_id'] ?? 0);
    if (
        in_array($userRole, ['campus_admin', 'staff'], true) &&
        $userCampusId &&
        $alumniCampusId !== (int)$userCampusId
    ) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Alumni not found']);
        exit;
    }
    
    // Keep legacy response keys for admin UI compatibility.
    $alumni['address'] = $alumni['address_street'] ?? null;
    $alumni['city'] = $alumni['address_city'] ?? null;
    $alumni['state'] = $alumni['address_province'] ?? null;
    $alumni['country'] = $alumni['address_country'] ?? null;
    $alumni['postal_code'] = $alumni['address_zip'] ?? null;
    $alumni['company'] = $alumni['current_employer'] ?? null;
    $alumni['company_name'] = $alumni['current_employer'] ?? null;
    $alumni['birthday'] = $alumni['birthdate'] ?? null;
    $alumni['website_url'] = null;
    $alumni['bio'] = null;
    $alumni['custom_fields'] = [];
    
    // Get point transactions
    $stmt = $db->prepare("
        SELECT pt.*, e.title as event_title
        FROM point_transactions pt
           LEFT JOIN events e ON pt.reference_type = 'event' AND pt.reference_id = e.id
        WHERE pt.user_id = ?
        ORDER BY pt.created_at DESC LIMIT 20
    ");
    $stmt->execute([$userId]);
    $transactions = $stmt->fetchAll();
    
    // Get event attendances
    $stmt = $db->prepare("
        SELECT ea.*, e.title as event_title, e.event_date, e.event_type
        FROM event_attendances ea
        JOIN events e ON ea.event_id = e.id
        WHERE ea.user_id = ?
        ORDER BY ea.created_at DESC LIMIT 20
    ");
    $stmt->execute([$userId]);
    $attendances = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'data' => [
            'alumni' => $alumni,
            'point_transactions' => $transactions,
            'event_attendances' => $attendances
        ]
    ]);
    
} catch (PDOException $e) {
    error_log("Admin Get Alumni Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
