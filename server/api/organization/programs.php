<?php
/**
 * Organization API - Get Programs
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../utils/helpers.php';

try {
    $db = Database::getInstance()->getConnection();
    
    $collegeId = $_GET['college_id'] ?? null;
    $status = $_GET['status'] ?? 'active';
    
    $sql = "
        SELECT p.*, c.name as college_name 
        FROM programs p
        LEFT JOIN colleges c ON p.college_id = c.id
        WHERE 1=1
    ";
    $params = [];
    
    if ($collegeId) {
        $sql .= " AND p.college_id = :college_id";
        $params['college_id'] = $collegeId;
    }
    
    if ($status && $status !== 'all') {
        $sql .= " AND p.status = :status";
        $params['status'] = $status;
    }
    
    $sql .= " ORDER BY p.name ASC";
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    
    $programs = $stmt->fetchAll();
    
    respondSuccess($programs);
    
} catch (Exception $e) {
    respondError('Failed to load programs: ' . $e->getMessage(), 500);
}
