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

if (!$announcementId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Announcement ID required']);
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    
    $stmt = $db->prepare("
        SELECT a.*, u.name as created_by_name,
            (SELECT COUNT(*) FROM announcement_reads WHERE announcement_id = a.id) as read_count
        FROM announcements a
        LEFT JOIN users u ON a.created_by = u.id
        WHERE a.id = ?
    ");
    $stmt->execute([$announcementId]);
    $announcement = $stmt->fetch();
    
    if (!$announcement) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Announcement not found']);
        exit;
    }
    
    // Parse JSON fields
    foreach (['target_colleges', 'target_programs', 'target_batch_years'] as $field) {
        if ($announcement[$field]) {
            $announcement[$field] = json_decode($announcement[$field], true);
        }
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
