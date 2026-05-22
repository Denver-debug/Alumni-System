<?php
/**
 * Events API - List Events
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../utils/helpers.php';
require_once __DIR__ . '/../../middleware/auth.php';

// Authentication optional for listing
$user = null;
try {
    $user = getCurrentUser();
} catch (Exception $e) {}

try {
    $db = Database::getInstance()->getConnection();
    syncEventStatuses($db);
    
    // Query parameters
    $search = $_GET['search'] ?? '';
    $status = $_GET['status'] ?? '';
    $eventType = $_GET['event_type'] ?? '';
    $month = $_GET['month'] ?? '';
    $page = max(1, (int)($_GET['page'] ?? 1));
    $limit = min((int)($_GET['limit'] ?? 10), 50);
    $offset = ($page - 1) * $limit;
    
    // Build query
    $where = ["e.status != 'cancelled'"];
    $params = [];
    
    if ($search) {
        $where[] = "(e.title LIKE :search OR e.description LIKE :search OR e.location LIKE :search)";
        $params['search'] = '%' . $search . '%';
    }
    
    if ($status) {
        $where[] = "e.status = :status";
        $params['status'] = $status;
    }
    
    if ($eventType) {
        $where[] = "e.event_type = :event_type";
        $params['event_type'] = $eventType;
    }
    
    if ($month) {
        $where[] = "DATE_FORMAT(e.event_date, '%Y-%m') = :month";
        $params['month'] = $month;
    }
    
    $whereClause = implode(' AND ', $where);
    
    // Count total
    $countSql = "SELECT COUNT(*) FROM events e WHERE $whereClause";
    $stmt = $db->prepare($countSql);
    $stmt->execute($params);
    $total = $stmt->fetchColumn();
    
    // Get events
    $sql = "
        SELECT 
            e.*,
            (SELECT COUNT(*) FROM event_rsvps WHERE event_id = e.id AND status IN ('going', 'maybe')) as registered_count
        FROM events e
        WHERE $whereClause
        ORDER BY e.event_date ASC, e.event_time ASC
        LIMIT :limit OFFSET :offset
    ";
    
    $stmt = $db->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue('offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    
    $events = $stmt->fetchAll();
    
    // Get user's registrations and attendance if authenticated
    $myRegistrations = [];
    $myAttendance = [];
    if ($user) {
        $stmt = $db->prepare("SELECT event_id FROM event_rsvps WHERE user_id = :user_id AND status IN ('going', 'maybe')");
        $stmt->execute(['user_id' => $user['id']]);
        $myRegistrations = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Get attendance records
        $stmt = $db->prepare("SELECT event_id, check_in_time, points_awarded FROM event_attendances WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $user['id']]);
        $attendanceRecords = $stmt->fetchAll();
        foreach ($attendanceRecords as $record) {
            $myAttendance[$record['event_id']] = [
                'attended' => true,
                'check_in_time' => $record['check_in_time'],
                'points_awarded' => $record['points_awarded']
            ];
        }
    }
    
    respondSuccess([
        'events' => $events,
        'total' => $total,
        'page' => $page,
        'per_page' => $limit,
        'my_registrations' => $myRegistrations,
        'my_attendance' => $myAttendance
    ]);
    
} catch (Exception $e) {
    respondError('Failed to load events: ' . $e->getMessage(), 500);
}
