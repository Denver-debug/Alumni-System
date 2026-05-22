<?php
/**
 * Alumni API - Search Alumni
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../utils/helpers.php';
require_once __DIR__ . '/../../middleware/auth.php';

// Require authentication
requireAuth();

try {
    $db = Database::getInstance()->getConnection();
    $user = getCurrentUser();
    
    $query = $_GET['query'] ?? '';
    $collegeId = $_GET['college_id'] ?? '';
    $programId = $_GET['program_id'] ?? '';
    $batchYear = $_GET['batch_year'] ?? '';
    $limit = min((int)($_GET['limit'] ?? 20), 100);
    $offset = (int)($_GET['offset'] ?? 0);
    
    // Build query
    $sql = "
        SELECT 
            u.id, u.alumni_id, u.email, u.name, u.profile_image,
            ap.batch_year, ap.total_points, ap.badge_level,
            c.name as college_name, p.name as program_name
        FROM users u
        LEFT JOIN alumni_profiles ap ON u.id = ap.user_id
        LEFT JOIN colleges c ON ap.college_id = c.id
        LEFT JOIN programs p ON ap.program_id = p.id
        WHERE u.role = 'alumni' 
        AND u.status = 'active'
        AND u.id != :current_user_id
    ";
    
    $params = ['current_user_id' => $user['id']];
    
    if ($query) {
        $sql .= " AND (u.name LIKE :query_name OR u.email LIKE :query_email OR u.alumni_id LIKE :query_alumni_id)";
        $searchTerm = '%' . $query . '%';
        $params['query_name'] = $searchTerm;
        $params['query_email'] = $searchTerm;
        $params['query_alumni_id'] = $searchTerm;
    }
    
    if ($collegeId) {
        $sql .= " AND ap.college_id = :college_id";
        $params['college_id'] = $collegeId;
    }
    
    if ($programId) {
        $sql .= " AND ap.program_id = :program_id";
        $params['program_id'] = $programId;
    }
    
    if ($batchYear) {
        $sql .= " AND ap.batch_year = :batch_year";
        $params['batch_year'] = $batchYear;
    }
    
    $sql .= " ORDER BY u.name ASC LIMIT :limit OFFSET :offset";
    
    $stmt = $db->prepare($sql);
    
    foreach ($params as $key => $value) {
        if ($key === 'limit' || $key === 'offset') {
            $stmt->bindValue($key, (int)$value, PDO::PARAM_INT);
        } else {
            $stmt->bindValue($key, $value);
        }
    }
    $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue('offset', $offset, PDO::PARAM_INT);
    
    $stmt->execute();
    $alumni = $stmt->fetchAll();
    
    respondSuccess($alumni);
    
} catch (Exception $e) {
    respondError('Search failed: ' . $e->getMessage(), 500);
}
