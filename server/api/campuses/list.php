<?php
/**
 * Get Campus List (public endpoint for selectors)
 * GET /api/campuses
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../utils/helpers.php';

try {
    $db = Database::getInstance()->getConnection();
    
    $status = $_GET['status'] ?? 'active';
    
    $sql = 'SELECT id, name, code, location FROM campuses';
    $params = [];
    
    if ($status) {
        $sql .= ' WHERE status = ?';
        $params[] = $status;
    }
    
    $sql .= ' ORDER BY name ASC';
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    
    success($stmt->fetchAll());
} catch (Exception $e) {
    error('Failed to retrieve campuses: ' . $e->getMessage(), 500);
}
