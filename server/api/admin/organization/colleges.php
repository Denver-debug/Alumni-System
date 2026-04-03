<?php
/**
 * Admin Colleges Management API
 * GET /api/admin/organization/colleges - List colleges
 * POST /api/admin/organization/colleges - Create college
 * PUT /api/admin/organization/colleges/{id} - Update college
 * DELETE /api/admin/organization/colleges/{id} - Delete college
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../middleware/auth.php';
require_once __DIR__ . '/../../../config/auth.php';

header('Content-Type: application/json');

requireAdmin();

$method = $_SERVER['REQUEST_METHOD'];
$collegeId = $GLOBALS['url_params']['id'] ?? null;

try {
    $db = Database::getInstance()->getConnection();
    
    switch ($method) {
        case 'GET':
            if ($collegeId) {
                // Get single college with programs
                $stmt = $db->prepare("SELECT * FROM colleges WHERE id = ?");
                $stmt->execute([$collegeId]);
                $college = $stmt->fetch();
                
                if (!$college) {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'message' => 'College not found']);
                    exit;
                }
                
                $stmt = $db->prepare("SELECT * FROM programs WHERE college_id = ? AND status = 'active' ORDER BY name");
                $stmt->execute([$collegeId]);
                $programs = $stmt->fetchAll();
                
                $college['programs'] = $programs;
                
                echo json_encode(['success' => true, 'data' => $college]);
            } else {
                // List all colleges with stats
                $stmt = $db->query("
                    SELECT c.*,
                        (SELECT COUNT(*) FROM programs WHERE college_id = c.id) as program_count,
                        (SELECT COUNT(*) FROM alumni_profiles ap JOIN users u ON ap.user_id = u.id 
                         WHERE ap.college_id = c.id AND u.role = 'alumni') as alumni_count
                    FROM colleges c
                    ORDER BY c.name
                ");
                $colleges = $stmt->fetchAll();
                
                echo json_encode(['success' => true, 'data' => $colleges]);
            }
            break;
            
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (empty($data['name']) || empty($data['code'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Name and code are required']);
                exit;
            }
            
            // Check uniqueness
            $stmt = $db->prepare("SELECT id FROM colleges WHERE code = ?");
            $stmt->execute([$data['code']]);
            if ($stmt->fetch()) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'College code already exists']);
                exit;
            }
            
            $stmt = $db->prepare("INSERT INTO colleges (name, code, description, status, created_at) VALUES (?, ?, ?, 'active', NOW())");
            $stmt->execute([trim($data['name']), strtoupper(trim($data['code'])), $data['description'] ?? null]);
            
            echo json_encode([
                'success' => true,
                'message' => 'College created',
                'data' => ['id' => $db->lastInsertId()]
            ]);
            break;
            
        case 'PUT':
            if (!$collegeId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'College ID required']);
                exit;
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            $stmt = $db->prepare("SELECT id FROM colleges WHERE id = ?");
            $stmt->execute([$collegeId]);
            if (!$stmt->fetch()) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'College not found']);
                exit;
            }
            
            $updates = [];
            $params = [];
            
            if (isset($data['name'])) {
                $updates[] = "name = ?";
                $params[] = trim($data['name']);
            }
            if (isset($data['code'])) {
                $updates[] = "code = ?";
                $params[] = strtoupper(trim($data['code']));
            }
            if (isset($data['description'])) {
                $updates[] = "description = ?";
                $params[] = $data['description'];
            }
            if (isset($data['status'])) {
                $updates[] = "status = ?";
                $params[] = $data['status'];
            }
            
            if (!empty($updates)) {
                $updates[] = "updated_at = NOW()";
                $params[] = $collegeId;
                $stmt = $db->prepare("UPDATE colleges SET " . implode(', ', $updates) . " WHERE id = ?");
                $stmt->execute($params);
            }
            
            echo json_encode(['success' => true, 'message' => 'College updated']);
            break;
            
        case 'DELETE':
            if (!$collegeId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'College ID required']);
                exit;
            }
            
            // Check for dependencies
            $stmt = $db->prepare("SELECT COUNT(*) as cnt FROM programs WHERE college_id = ?");
            $stmt->execute([$collegeId]);
            if ($stmt->fetch()['cnt'] > 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Cannot delete college with programs']);
                exit;
            }
            
            $stmt = $db->prepare("DELETE FROM colleges WHERE id = ?");
            $stmt->execute([$collegeId]);
            
            echo json_encode(['success' => true, 'message' => 'College deleted']);
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    }
    
} catch (PDOException $e) {
    error_log("Admin Colleges Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
