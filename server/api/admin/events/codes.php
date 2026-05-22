<?php
/**
 * Admin Event Attendance Code API
 * GET /api/admin/events/{id}/codes
 * POST /api/admin/events/{id}/codes
 * DELETE /api/admin/events/{id}/codes
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../middleware/auth.php';

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

    $stmt = $db->prepare('SELECT id, attendance_code FROM events WHERE id = ? LIMIT 1');
    $stmt->execute([$eventId]);
    $event = $stmt->fetch();

    if (!$event) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Event not found']);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $codes = [];
        if (!empty($event['attendance_code'])) {
            $codes[] = [
                'id' => (int)$event['id'],
                'code' => $event['attendance_code'],
                'code_type' => 'general',
                'uses' => null,
            ];
        }

        echo json_encode(['success' => true, 'data' => $codes]);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true) ?? [];

        $code = strtoupper(trim((string)($data['code'] ?? '')));
        if ($code === '') {
            $code = strtoupper(substr(md5(uniqid('', true)), 0, 8));
        }

        $stmt = $db->prepare('UPDATE events SET attendance_code = ?, updated_at = NOW() WHERE id = ?');
        $stmt->execute([$code, $eventId]);

        echo json_encode([
            'success' => true,
            'message' => 'Attendance code generated',
            'data' => [
                'id' => (int)$eventId,
                'code' => $code,
            ],
        ]);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        $stmt = $db->prepare('UPDATE events SET attendance_code = NULL, updated_at = NOW() WHERE id = ?');
        $stmt->execute([$eventId]);

        echo json_encode(['success' => true, 'message' => 'Attendance code cleared']);
        exit;
    }

    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
} catch (PDOException $e) {
    error_log('Admin Event Codes Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}

