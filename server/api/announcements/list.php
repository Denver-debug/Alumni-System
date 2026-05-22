<?php
/**
 * Announcements API - List Announcements
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../utils/helpers.php';

// Get user if authenticated (for targeted announcements)
$user = null;
$userProfile = null;
try {
    $user = getCurrentUser();
    if ($user) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT college_id, program_id, section_id, batch_year FROM alumni_profiles WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $user['id']]);
        $userProfile = $stmt->fetch();
    }
} catch (Exception $e) {}

try {
    $db = Database::getInstance()->getConnection();
    
    $limit = min((int)($_GET['limit'] ?? 20), 100);
    $offset = (int)($_GET['offset'] ?? 0);
    
    // Build query based on user access
    $sql = "
        SELECT 
            a.*,
            u.name as created_by_name
        FROM announcements a
        LEFT JOIN users u ON a.created_by = u.id
        WHERE a.status = 'published'
        AND (a.publish_date IS NULL OR a.publish_date <= NOW())
        AND (a.expire_date IS NULL OR a.expire_date >= NOW())
    ";
    
    // Filter by target audience
    if ($userProfile) {
        $sql .= " AND (
            a.target_type = 'all'
            OR (a.target_type = 'college' AND a.target_id = :college_id)
            OR (a.target_type = 'program' AND a.target_id = :program_id)
            OR (a.target_type = 'section' AND a.target_id = :section_id)
            OR (a.target_type = 'batch_year' AND a.target_batch_year = :batch_year)
        )";
    } else {
        $sql .= " AND a.target_type = 'all'";
    }
    
    $sql .= " ORDER BY a.priority DESC, a.created_at DESC LIMIT :limit OFFSET :offset";
    
    $stmt = $db->prepare($sql);
    
    if ($userProfile) {
        $stmt->bindValue('college_id', $userProfile['college_id']);
        $stmt->bindValue('program_id', $userProfile['program_id']);
        $stmt->bindValue('section_id', $userProfile['section_id']);
        $stmt->bindValue('batch_year', $userProfile['batch_year']);
    }
    
    $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue('offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    
    $announcements = $stmt->fetchAll();
    
    // Mark as read if user is authenticated
    if ($user && !empty($announcements)) {
        $announcementIds = array_column($announcements, 'id');
        $placeholders = implode(',', array_fill(0, count($announcementIds), '?'));
        
        $stmt = $db->prepare("
            INSERT IGNORE INTO announcement_reads (announcement_id, user_id, read_at)
            SELECT id, ?, NOW() FROM announcements WHERE id IN ($placeholders)
        ");
        $stmt->execute(array_merge([$user['id']], $announcementIds));
    }
    
    respondSuccess([
        'announcements' => $announcements
    ]);
    
} catch (Exception $e) {
    respondError('Failed to load announcements: ' . $e->getMessage(), 500);
}
