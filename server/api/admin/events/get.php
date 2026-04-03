<?php
/**
 * Admin Get Event API
 * GET /api/admin/events/{id} - Get event details with attendees
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

$eventId = $GLOBALS['url_params']['id'] ?? null;

if (!$eventId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Event ID required']);
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    
    // Get event
    $stmt = $db->prepare("
        SELECT e.*, u.name as created_by_name
        FROM events e
        LEFT JOIN users u ON e.created_by = u.id
        WHERE e.id = ?
    ");
    $stmt->execute([$eventId]);
    $event = $stmt->fetch();
    
    if (!$event) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Event not found']);
        exit;
    }
    
    // Parse JSON fields
    foreach (['target_colleges', 'target_programs', 'target_batch_years'] as $field) {
        if ($event[$field]) {
            $event[$field] = json_decode($event[$field], true);
        }
    }
    
    // Get attendance codes
    $stmt = $db->prepare("SELECT * FROM event_attendance_codes WHERE event_id = ? ORDER BY created_at DESC");
    $stmt->execute([$eventId]);
    $codes = $stmt->fetchAll();
    
    // Get attendees
    $stmt = $db->prepare("
        SELECT ea.*, u.name, u.email, u.alumni_id, u.profile_image,
            ap.batch_year, c.name as college_name, p.name as program_name
        FROM event_attendances ea
        JOIN users u ON ea.user_id = u.id
        LEFT JOIN alumni_profiles ap ON u.id = ap.user_id
        LEFT JOIN colleges c ON ap.college_id = c.id
        LEFT JOIN programs p ON ap.program_id = p.id
        WHERE ea.event_id = ?
        ORDER BY ea.created_at DESC
    ");
    $stmt->execute([$eventId]);
    $attendees = $stmt->fetchAll();
    
    // Get stats
    $stats = [
        'total_registered' => 0,
        'total_attended' => 0,
        'total_cancelled' => 0
    ];
    
    foreach ($attendees as $a) {
        if ($a['status'] === 'registered') $stats['total_registered']++;
        elseif ($a['status'] === 'attended') $stats['total_attended']++;
        elseif ($a['status'] === 'cancelled') $stats['total_cancelled']++;
    }
    
    echo json_encode([
        'success' => true,
        'data' => [
            'event' => $event,
            'attendance_codes' => $codes,
            'attendees' => $attendees,
            'stats' => $stats
        ]
    ]);
    
} catch (PDOException $e) {
    error_log("Admin Get Event Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
