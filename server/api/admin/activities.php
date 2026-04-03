<?php
/**
 * Admin Activity Logs API
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../utils/helpers.php';
require_once __DIR__ . '/../../middleware/auth.php';

requireAdmin();

try {
    $db = Database::getInstance()->getConnection();
    
    $limit = min((int)($_GET['limit'] ?? 50), 200);
    $offset = (int)($_GET['offset'] ?? 0);
    $action = $_GET['action'] ?? '';
    $adminId = $_GET['admin_id'] ?? '';
    
    $sql = "
        SELECT 
            aa.*,
            u.name as admin_name,
            u.email as admin_email
        FROM admin_activities aa
        LEFT JOIN users u ON aa.admin_id = u.id
        WHERE 1=1
    ";
    $params = [];
    
    if ($action) {
        $sql .= " AND aa.action = :action";
        $params['action'] = $action;
    }
    
    if ($adminId) {
        $sql .= " AND aa.admin_id = :admin_id";
        $params['admin_id'] = $adminId;
    }
    
    $sql .= " ORDER BY aa.created_at DESC LIMIT :limit OFFSET :offset";
    
    $stmt = $db->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue('offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    
    $logs = $stmt->fetchAll();
    
    respondSuccess($logs);
    
} catch (Exception $e) {
    respondError('Failed to load logs: ' . $e->getMessage(), 500);
}
