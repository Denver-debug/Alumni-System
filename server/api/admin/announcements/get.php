<?php
/**
 * Admin Get Announcement API
 * GET /api/admin/announcements/{id} - Get announcement details
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

$announcementId = $GLOBALS['url_params']['id'] ?? null;
$currentUser = getCurrentUser();
$userRole = $currentUser['role'] ?? 'alumni';
$userCampusId = $currentUser['campus_id'] ?? null;

if (!$announcementId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Announcement ID required']);
    exit;
}

if (in_array($userRole, ['campus_admin', 'staff'], true) && !$userCampusId) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Campus assignment required']);
    exit;
}

try {
    $db = Database::getInstance()->getConnection();

    $campusMatch = null;
    if (in_array($userRole, ['campus_admin', 'staff'], true) && $userCampusId) {
        $campusMatch = $db->prepare("SELECT 1 FROM announcement_campuses WHERE announcement_id = ? AND campus_id = ? LIMIT 1");
        $campusMatch->execute([$announcementId, $userCampusId]);
    }
    
    $stmt = $db->prepare("
        SELECT a.*, u.name as created_by_name,
            c.name as campus_name,
            (SELECT COUNT(*) FROM announcement_reads WHERE announcement_id = a.id) as read_count
        FROM announcements a
        LEFT JOIN users u ON a.created_by = u.id
        LEFT JOIN campuses c ON a.campus_id = c.id
        WHERE a.id = ?
    ");
    $stmt->execute([$announcementId]);
    $announcement = $stmt->fetch();
    
    if (!$announcement) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Announcement not found']);
        exit;
    }

    $campusStmt = $db->prepare(
        "SELECT ac.campus_id, c.name, c.code
         FROM announcement_campuses ac
         JOIN campuses c ON c.id = ac.campus_id
         WHERE ac.announcement_id = ?
         ORDER BY c.name ASC"
    );
    $campusStmt->execute([$announcementId]);
    $campuses = $campusStmt->fetchAll();

    if (empty($campuses) && !empty($announcement['campus_id'])) {
        $campuses = [[
            'campus_id' => (int)$announcement['campus_id'],
            'name' => $announcement['campus_name'] ?? null,
            'code' => null,
        ]];
    }

    $announcement['campus_ids'] = array_map('intval', array_column($campuses, 'campus_id'));
    $announcement['campuses'] = $campuses;

    if (
        in_array($userRole, ['campus_admin', 'staff'], true) &&
        $userCampusId &&
        !empty($announcement['campus_id']) &&
        (int)$announcement['campus_id'] !== (int)$userCampusId &&
        (!$campusMatch || !$campusMatch->fetchColumn())
    ) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Announcement not found']);
        exit;
    }
    
    // Get read stats
    $stmt = $db->prepare("
        SELECT ar.*, u.name, u.email, u.alumni_id
        FROM announcement_reads ar
        JOIN users u ON ar.user_id = u.id
        WHERE ar.announcement_id = ?
        ORDER BY ar.read_at DESC
        LIMIT 50
    ");
    $stmt->execute([$announcementId]);
    $readers = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'data' => [
            'announcement' => $announcement,
            'readers' => $readers
        ]
    ]);
    
} catch (PDOException $e) {
    error_log("Admin Get Announcement Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
