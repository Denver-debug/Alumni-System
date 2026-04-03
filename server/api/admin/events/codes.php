<?php
/**
 * Admin Event Attendance Codes API
 * POST /api/admin/events/{id}/codes - Generate attendance code
 * GET /api/admin/events/{id}/codes - List codes
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../middleware/auth.php';
require_once __DIR__ . '/../../../config/auth.php';

header('Content-Type: application/json');

requireAdmin();

$eventId = $GLOBALS['url_params']['id'] ?? null;

if (!$eventId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Event ID required']);
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    
    // Verify event exists
    $stmt = $db->prepare("SELECT id FROM events WHERE id = ?");
    $stmt->execute([$eventId]);
    if (!$stmt->fetch()) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Event not found']);
        exit;
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $stmt = $db->prepare("
            SELECT eac.*, 
                (SELECT COUNT(*) FROM event_attendances WHERE attendance_code_id = eac.id) as uses
            FROM event_attendance_codes eac
            WHERE eac.event_id = ?
            ORDER BY eac.created_at DESC
        ");
        $stmt->execute([$eventId]);
        $codes = $stmt->fetchAll();
        
        echo json_encode(['success' => true, 'data' => $codes]);
        
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Generate code
        $code = strtoupper($data['code'] ?? substr(md5(uniqid()), 0, 8));
        
        $stmt = $db->prepare("
            INSERT INTO event_attendance_codes (event_id, code, code_type, points_bonus, max_uses, valid_from, valid_until, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([
            $eventId,
            $code,
            $data['code_type'] ?? 'general',
            $data['points_bonus'] ?? 0,
            $data['max_uses'] ?? null,
            $data['valid_from'] ?? date('Y-m-d H:i:s'),
            $data['valid_until'] ?? null
        ]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Attendance code created',
            'data' => ['code' => $code, 'id' => $db->lastInsertId()]
        ]);
        
    } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        $codeId = $_GET['code_id'] ?? null;
        if (!$codeId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Code ID required']);
            exit;
        }
        
        $stmt = $db->prepare("DELETE FROM event_attendance_codes WHERE id = ? AND event_id = ?");
        $stmt->execute([$codeId, $eventId]);
        
        echo json_encode(['success' => true, 'message' => 'Code deleted']);
        
    } else {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    }
    
} catch (PDOException $e) {
    error_log("Admin Event Codes Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
