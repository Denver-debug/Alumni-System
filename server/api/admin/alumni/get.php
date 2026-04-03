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

if (!$userId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Alumni ID required']);
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    
    // Get user with full profile
    $sql = "
        SELECT 
            u.id, u.alumni_id, u.name, u.email, u.profile_image, u.status,
            u.email_verified, u.auth_provider, u.login_attempts, u.last_login,
            u.created_at, u.updated_at,
            ap.batch_year, ap.graduation_year, ap.phone, ap.address, ap.city,
            ap.state, ap.country, ap.postal_code, ap.company, ap.job_title,
            ap.industry, ap.linkedin_url, ap.facebook_url, ap.twitter_url,
            ap.website_url, ap.bio, ap.total_points, ap.badge_level, ap.custom_fields,
            c.id as college_id, c.name as college_name, c.code as college_code,
            p.id as program_id, p.name as program_name, p.code as program_code,
            s.id as section_id, s.name as section_name
        FROM users u
        LEFT JOIN alumni_profiles ap ON u.id = ap.user_id
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
    
    if ($alumni['custom_fields']) {
        $alumni['custom_fields'] = json_decode($alumni['custom_fields'], true);
    }
    
    // Get point transactions
    $stmt = $db->prepare("
        SELECT pt.*, e.title as event_title
        FROM point_transactions pt
        LEFT JOIN events e ON pt.event_id = e.id
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
