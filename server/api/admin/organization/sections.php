<?php
/**
 * Admin Sections Management API
 * GET /api/admin/organization/sections - List sections
 * POST /api/admin/organization/sections - Create section
 * PUT /api/admin/organization/sections/{id} - Update section
 * DELETE /api/admin/organization/sections/{id} - Delete section
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../middleware/auth.php';

header('Content-Type: application/json');

requireAdmin();

$method = $_SERVER['REQUEST_METHOD'];
$sectionId = $GLOBALS['url_params']['id'] ?? null;

try {
    $db = Database::getInstance()->getConnection();
    
    switch ($method) {
        case 'GET':
            if ($sectionId) {
                $stmt = $db->prepare("
                    SELECT s.*, p.name as program_name, p.code as program_code,
                        c.name as college_name, c.code as college_code
                    FROM sections s
                    JOIN programs p ON s.program_id = p.id
                    JOIN colleges c ON p.college_id = c.id
                    WHERE s.id = ?
                ");
                $stmt->execute([$sectionId]);
                $section = $stmt->fetch();
                
                if (!$section) {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'message' => 'Section not found']);
                    exit;
                }
                
                // Get alumni in section
                $stmt = $db->prepare("
                    SELECT u.id, u.name, u.email, u.alumni_id, u.profile_image, ap.total_points
                    FROM users u
                    JOIN alumni_profiles ap ON u.id = ap.user_id
                    WHERE ap.section_id = ? AND u.role = 'alumni'
                    ORDER BY u.name
                ");
                $stmt->execute([$sectionId]);
                $section['alumni'] = $stmt->fetchAll();
                
                echo json_encode(['success' => true, 'data' => $section]);
            } else {
                $programId = $_GET['program_id'] ?? null;
                $batchYear = $_GET['batch_year'] ?? null;
                
                $where = ['1=1'];
                $params = [];
                
                if ($programId) {
                    $where[] = "s.program_id = ?";
                    $params[] = $programId;
                }
                if ($batchYear) {
                    $where[] = "s.batch_year = ?";
                    $params[] = $batchYear;
                }
                
                $sql = "
                    SELECT s.*, p.name as program_name, p.code as program_code,
                        c.name as college_name,
                        (SELECT COUNT(*) FROM alumni_profiles WHERE section_id = s.id) as alumni_count
                    FROM sections s
                    JOIN programs p ON s.program_id = p.id
                    JOIN colleges c ON p.college_id = c.id
                    WHERE " . implode(' AND ', $where) . "
                    ORDER BY s.batch_year DESC, c.name, p.name, s.name
                ";
                
                $stmt = $db->prepare($sql);
                $stmt->execute($params);
                $sections = $stmt->fetchAll();
                
                echo json_encode(['success' => true, 'data' => $sections]);
            }
            break;
            
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (empty($data['name']) || empty($data['program_id']) || empty($data['batch_year'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Name, program_id, and batch_year are required']);
                exit;
            }
            
            $stmt = $db->prepare("INSERT INTO sections (program_id, name, batch_year, status, created_at) VALUES (?, ?, ?, 'active', NOW())");
            $stmt->execute([
                $data['program_id'],
                trim($data['name']),
                $data['batch_year']
            ]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Section created',
                'data' => ['id' => $db->lastInsertId()]
            ]);
            break;
            
        case 'PUT':
            if (!$sectionId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Section ID required']);
                exit;
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            $updates = [];
            $params = [];
            
            foreach (['program_id', 'name', 'batch_year', 'status'] as $field) {
                if (isset($data[$field])) {
                    $updates[] = "$field = ?";
                    $params[] = $data[$field];
                }
            }
            
            if (!empty($updates)) {
                $updates[] = "updated_at = NOW()";
                $params[] = $sectionId;
                $stmt = $db->prepare("UPDATE sections SET " . implode(', ', $updates) . " WHERE id = ?");
                $stmt->execute($params);
            }
            
            echo json_encode(['success' => true, 'message' => 'Section updated']);
            break;
            
        case 'DELETE':
            if (!$sectionId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Section ID required']);
                exit;
            }
            
            $stmt = $db->prepare("SELECT COUNT(*) as cnt FROM alumni_profiles WHERE section_id = ?");
            $stmt->execute([$sectionId]);
            if ($stmt->fetch()['cnt'] > 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Cannot delete section with alumni']);
                exit;
            }
            
            $stmt = $db->prepare("DELETE FROM sections WHERE id = ?");
            $stmt->execute([$sectionId]);
            
            echo json_encode(['success' => true, 'message' => 'Section deleted']);
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    }
    
} catch (PDOException $e) {
    error_log("Admin Sections Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
