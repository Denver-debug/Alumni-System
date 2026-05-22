<?php
/**
 * Admin Get Event API
 * GET /api/admin/events/{id} - Get event details with attendees
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../utils/helpers.php';
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
    syncEventStatuses($db);

    $stmt = $db->prepare(
           "SELECT e.*, u.name as created_by_name, c.name as campus_name
         FROM events e
         LEFT JOIN users u ON e.created_by = u.id
            LEFT JOIN campuses c ON e.campus_id = c.id
         WHERE e.id = ?"
    );
    $stmt->execute([$eventId]);
    $event = $stmt->fetch();

    if (!$event) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Event not found']);
        exit;
    }

    $campusStmt = $db->prepare(
        "SELECT ec.campus_id, c.name, c.code
         FROM event_campuses ec
         JOIN campuses c ON c.id = ec.campus_id
         WHERE ec.event_id = ?
         ORDER BY c.name ASC"
    );
    $campusStmt->execute([$eventId]);
    $campuses = $campusStmt->fetchAll();

    if (empty($campuses) && !empty($event['campus_id'])) {
        $campuses = [[
            'campus_id' => (int)$event['campus_id'],
            'name' => $event['campus_name'] ?? null,
            'code' => null,
        ]];
    }

    $event['campus_ids'] = array_map('intval', array_column($campuses, 'campus_id'));
    $event['campuses'] = $campuses;

    $codes = [];
    if (!empty($event['attendance_code'])) {
        $codes[] = [
            'id' => (int)$event['id'],
            'code' => $event['attendance_code'],
            'code_type' => 'general',
        ];
    }

    $stmt = $db->prepare(
        "SELECT
            u.id as user_id,
            u.name,
            u.email,
            u.alumni_id,
            u.profile_image,
            ap.batch_year,
            c.name as college_name,
            p.name as program_name,
            r.status as rsvp_status,
            COALESCE(r.created_at, a.created_at) as registered_at,
            a.check_in_time,
            a.points_awarded,
            CASE
                WHEN a.id IS NOT NULL THEN 'attended'
                WHEN r.status = 'not_going' THEN 'absent'
                ELSE 'registered'
            END as status
         FROM users u
         LEFT JOIN event_rsvps r ON r.user_id = u.id AND r.event_id = ?
         LEFT JOIN event_attendances a ON a.user_id = u.id AND a.event_id = ?
         LEFT JOIN alumni_profiles ap ON u.id = ap.user_id
         LEFT JOIN colleges c ON ap.college_id = c.id
         LEFT JOIN programs p ON ap.program_id = p.id
         WHERE r.id IS NOT NULL OR a.id IS NOT NULL
         ORDER BY COALESCE(a.created_at, r.created_at) DESC"
    );
    $stmt->execute([$eventId, $eventId]);
    $attendees = $stmt->fetchAll();

    $stats = [
        'total_registered' => 0,
        'total_attended' => 0,
        'total_absent' => 0,
    ];

    foreach ($attendees as $attendee) {
        if ($attendee['status'] === 'attended') {
            $stats['total_attended']++;
        } elseif ($attendee['status'] === 'absent') {
            $stats['total_absent']++;
        } else {
            $stats['total_registered']++;
        }
    }

    echo json_encode([
        'success' => true,
        'data' => [
            'event' => $event,
            'attendance_codes' => $codes,
            'attendees' => $attendees,
            'stats' => $stats,
        ]
    ]);
} catch (PDOException $e) {
    error_log('Admin Get Event Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
