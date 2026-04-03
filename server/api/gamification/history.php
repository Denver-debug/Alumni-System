<?php
/**
 * Gamification API - Get Points History
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../utils/helpers.php';
require_once __DIR__ . '/../../middleware/auth.php';

requireAuth();

try {
    $db = Database::getInstance()->getConnection();
    $user = getCurrentUser();
    
    $limit = min((int)($_GET['limit'] ?? 50), 100);
    $offset = (int)($_GET['offset'] ?? 0);
    
    $stmt = $db->prepare("
        SELECT 
            pt.*,
            e.title as event_title
        FROM point_transactions pt
        LEFT JOIN events e ON pt.event_id = e.id
        WHERE pt.user_id = :user_id
        ORDER BY pt.created_at DESC
        LIMIT :limit OFFSET :offset
    ");
    $stmt->bindValue('user_id', $user['id']);
    $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue('offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    
    $history = $stmt->fetchAll();
    
    respondSuccess($history);
    
} catch (Exception $e) {
    respondError('Failed to load history: ' . $e->getMessage(), 500);
}
