<?php
/**
 * Admin Programs Management API
 * GET /api/admin/organization/programs - List programs
 * POST /api/admin/organization/programs - Create program
 * PUT /api/admin/organization/programs/{id} - Update program
 * DELETE /api/admin/organization/programs/{id} - Delete program
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../middleware/auth.php';

header('Content-Type: application/json');

requireAdmin();

$method = $_SERVER['REQUEST_METHOD'];
$programId = $GLOBALS['url_params']['id'] ?? null;

try {
    $db = Database::getInstance()->getConnection();
    
    switch ($method) {
        case 'GET':
            if ($programId) {
                $stmt = $db->prepare("
                    SELECT p.*, c.name as college_name, c.code as college_code
                    FROM programs p
                    JOIN colleges c ON p.college_id = c.id
                    WHERE p.id = ?
                ");
                $stmt->execute([$programId]);
                $program = $stmt->fetch();
                
                if (!$program) {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'message' => 'Program not found']);
                    exit;
                }
                
                // Get sections
                $stmt = $db->prepare("SELECT * FROM sections WHERE program_id = ? ORDER BY batch_year DESC, name");
                $stmt->execute([$programId]);
                $program['sections'] = $stmt->fetchAll();
                
                echo json_encode(['success' => true, 'data' => $program]);
            } else {
                $collegeId = $_GET['college_id'] ?? null;
                $where = $collegeId ? "WHERE p.college_id = ?" : "";
                $params = $collegeId ? [$collegeId] : [];
                
                $sql = "
                    SELECT p.*, c.name as college_name, c.code as college_code,
                        (SELECT COUNT(*) FROM sections WHERE program_id = p.id) as section_count,
                        (SELECT COUNT(*) FROM alumni_profiles WHERE program_id = p.id) as alumni_count
                    FROM programs p
                    JOIN colleges c ON p.college_id = c.id
                    $where
                    ORDER BY c.name, p.name
                ";
                
                $stmt = $db->prepare($sql);
                $stmt->execute($params);
                $programs = $stmt->fetchAll();
                
                echo json_encode(['success' => true, 'data' => $programs]);
            }
            break;
            
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (empty($data['name']) || empty($data['code']) || empty($data['college_id'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Name, code, and college_id are required']);
                exit;
            }
            
            // Check uniqueness
            $stmt = $db->prepare("SELECT id FROM programs WHERE code = ?");
            $stmt->execute([$data['code']]);
            if ($stmt->fetch()) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Program code already exists']);
                exit;
            }
            
            $stmt = $db->prepare("INSERT INTO programs (college_id, name, code, description, degree_type, duration_years, status, created_at) VALUES (?, ?, ?, ?, ?, ?, 'active', NOW())");
            $stmt->execute([
                $data['college_id'],
                trim($data['name']),
                strtoupper(trim($data['code'])),
                $data['description'] ?? null,
                $data['degree_type'] ?? 'bachelor',
                $data['duration_years'] ?? 4
            ]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Program created',
                'data' => ['id' => $db->lastInsertId()]
            ]);
            break;
            
        case 'PUT':
            if (!$programId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Program ID required']);
                exit;
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            $updates = [];
            $params = [];
            
            $fields = ['college_id', 'name', 'code', 'description', 'degree_type', 'duration_years', 'status'];
            foreach ($fields as $field) {
                if (isset($data[$field])) {
                    $updates[] = "$field = ?";
                    $params[] = $data[$field];
                }
            }
            
            if (!empty($updates)) {
                $updates[] = "updated_at = NOW()";
                $params[] = $programId;
                $stmt = $db->prepare("UPDATE programs SET " . implode(', ', $updates) . " WHERE id = ?");
                $stmt->execute($params);
            }
            
            echo json_encode(['success' => true, 'message' => 'Program updated']);
            break;
            
        case 'DELETE':
            if (!$programId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Program ID required']);
                exit;
            }
            
            $stmt = $db->prepare("SELECT COUNT(*) as cnt FROM sections WHERE program_id = ?");
            $stmt->execute([$programId]);
            if ($stmt->fetch()['cnt'] > 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Cannot delete program with sections']);
                exit;
            }
            
            $stmt = $db->prepare("DELETE FROM programs WHERE id = ?");
            $stmt->execute([$programId]);
            
            echo json_encode(['success' => true, 'message' => 'Program deleted']);
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    }
    
} catch (PDOException $e) {
    error_log("Admin Programs Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
