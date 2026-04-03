<?php
/**
 * Admin Create Event API
 * POST /api/admin/events - Create new event
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

// Validation
$required = ['title', 'event_date'];
foreach ($required as $field) {
    if (empty($data[$field])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => ucfirst($field) . ' is required']);
        exit;
    }
}

try {
    $db = Database::getInstance()->getConnection();
    $admin = getCurrentUser();
    
    // Generate attendance code
    $attendanceCode = strtoupper(substr(md5(uniqid()), 0, 8));
    
    $stmt = $db->prepare("
        INSERT INTO events (
            title, description, event_type, event_date, end_date, location, 
            venue_details, is_virtual, virtual_link, max_attendees, 
            points_reward, registration_deadline, status, image_url, 
            target_colleges, target_programs, target_batch_years,
            created_by, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    
    $stmt->execute([
        trim($data['title']),
        $data['description'] ?? null,
        $data['event_type'] ?? 'general',
        $data['event_date'],
        $data['end_date'] ?? null,
        $data['location'] ?? null,
        $data['venue_details'] ?? null,
        $data['is_virtual'] ?? 0,
        $data['virtual_link'] ?? null,
        $data['max_attendees'] ?? null,
        $data['points_reward'] ?? 10,
        $data['registration_deadline'] ?? null,
        $data['status'] ?? 'draft',
        $data['image_url'] ?? null,
        isset($data['target_colleges']) ? json_encode($data['target_colleges']) : null,
        isset($data['target_programs']) ? json_encode($data['target_programs']) : null,
        isset($data['target_batch_years']) ? json_encode($data['target_batch_years']) : null,
        $admin['id']
    ]);
    
    $eventId = $db->lastInsertId();
    
    // Create default attendance code
    $stmt = $db->prepare("
        INSERT INTO event_attendance_codes (event_id, code, code_type, valid_from, valid_until, created_at)
        VALUES (?, ?, 'general', ?, ?, NOW())
    ");
    $stmt->execute([
        $eventId,
        $attendanceCode,
        $data['event_date'],
        $data['end_date'] ?? $data['event_date']
    ]);
    
    // Log activity
    $stmt = $db->prepare("
        INSERT INTO admin_activities (admin_id, action, target_type, target_id, details, ip_address, created_at)
        VALUES (?, 'create', 'event', ?, ?, ?, NOW())
    ");
    $stmt->execute([
        $admin['id'],
        $eventId,
        json_encode(['title' => $data['title']]),
        $_SERVER['REMOTE_ADDR'] ?? null
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Event created successfully',
        'data' => [
            'event_id' => $eventId,
            'attendance_code' => $attendanceCode
        ]
    ]);
    
} catch (PDOException $e) {
    error_log("Admin Create Event Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
