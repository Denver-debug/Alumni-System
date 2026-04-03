<?php
/**
 * Organization API - Get Colleges
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../utils/helpers.php';

try {
    $db = Database::getInstance()->getConnection();
    
    $status = $_GET['status'] ?? 'active';
    
    $sql = "SELECT * FROM colleges";
    $params = [];
    
    if ($status && $status !== 'all') {
        $sql .= " WHERE status = :status";
        $params['status'] = $status;
    }
    
    $sql .= " ORDER BY name ASC";
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    
    $colleges = $stmt->fetchAll();
    
    respondSuccess($colleges);
    
} catch (Exception $e) {
    respondError('Failed to load colleges: ' . $e->getMessage(), 500);
}
