<?php
/**
 * Organization API - Get Sections
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../utils/helpers.php';

try {
    $db = Database::getInstance()->getConnection();
    
    $programId = $_GET['program_id'] ?? null;
    $campusId = $_GET['campus_id'] ?? null;
    $batchYear = $_GET['batch_year'] ?? null;
    $status = $_GET['status'] ?? 'active';
    
    $sql = "
        SELECT s.*, p.name as program_name, c.name as college_name,
               camp.name as campus_name, camp.code as campus_code
        FROM sections s
        LEFT JOIN programs p ON s.program_id = p.id
        LEFT JOIN colleges c ON p.college_id = c.id
        LEFT JOIN campuses camp ON s.campus_id = camp.id
        WHERE 1=1
    ";
    $params = [];
    
    if ($programId) {
        $sql .= " AND s.program_id = :program_id";
        $params['program_id'] = $programId;
    }

    if ($campusId) {
        $sql .= " AND s.campus_id = :campus_id";
        $params['campus_id'] = $campusId;
    }
    
    if ($batchYear) {
        $sql .= " AND s.batch_year = :batch_year";
        $params['batch_year'] = $batchYear;
    }
    
    if ($status && $status !== 'all') {
        $sql .= " AND s.status = :status";
        $params['status'] = $status;
    }
    
    $sql .= " ORDER BY s.batch_year DESC, s.name ASC";
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    
    $sections = $stmt->fetchAll();
    
    respondSuccess($sections);
    
} catch (Exception $e) {
    respondError('Failed to load sections: ' . $e->getMessage(), 500);
}
