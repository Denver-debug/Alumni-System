<?php
/**
 * Admin Create Announcement API
 * POST /api/admin/announcements - Create new announcement
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../middleware/auth.php';
require_once __DIR__ . '/../../../config/auth.php';

header('Content-Type: application/json');

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (empty($data['title'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Title is required']);
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    $admin = getCurrentUser();
    
    $stmt = $db->prepare("
        INSERT INTO announcements (
            title, content, priority, status, is_pinned, image_url,
            publish_at, expires_at, target_colleges, target_programs, 
            target_batch_years, created_by, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    
    $stmt->execute([
        trim($data['title']),
        $data['content'] ?? null,
        $data['priority'] ?? 'normal',
        $data['status'] ?? 'draft',
        $data['is_pinned'] ?? 0,
        $data['image_url'] ?? null,
        $data['publish_at'] ?? null,
        $data['expires_at'] ?? null,
        isset($data['target_colleges']) ? json_encode($data['target_colleges']) : null,
        isset($data['target_programs']) ? json_encode($data['target_programs']) : null,
        isset($data['target_batch_years']) ? json_encode($data['target_batch_years']) : null,
        $admin['id']
    ]);
    
    $announcementId = $db->lastInsertId();
    
    $stmt = $db->prepare("INSERT INTO admin_activities (admin_id, action, target_type, target_id, details, ip_address, created_at) VALUES (?, 'create', 'announcement', ?, ?, ?, NOW())");
    $stmt->execute([$admin['id'], $announcementId, json_encode(['title' => $data['title']]), $_SERVER['REMOTE_ADDR'] ?? null]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Announcement created',
        'data' => ['id' => $announcementId]
    ]);
    
} catch (PDOException $e) {
    error_log("Admin Create Announcement Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
