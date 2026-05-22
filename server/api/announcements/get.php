<?php
/**
 * Announcements API - Get Announcement Detail
 * GET /api/announcements/{id}
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../utils/helpers.php';

$user = null;
$userProfile = null;
try {
    $user = getCurrentUser();
    if ($user) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare('SELECT college_id, program_id, section_id, batch_year FROM alumni_profiles WHERE user_id = :user_id');
        $stmt->execute(['user_id' => $user['id']]);
        $userProfile = $stmt->fetch();
    }
} catch (Exception $e) {
}

try {
    $db = Database::getInstance()->getConnection();

    $announcementId = $GLOBALS['url_params']['id'] ?? ($_GET['id'] ?? null);
    if (!$announcementId) {
        respondError('Announcement ID required', 400);
    }

    $sql = "
        SELECT
            a.*,
            u.name as created_by_name
        FROM announcements a
        LEFT JOIN users u ON a.created_by = u.id
        WHERE a.id = :id
          AND a.status = 'published'
          AND (a.publish_date IS NULL OR a.publish_date <= NOW())
          AND (a.expire_date IS NULL OR a.expire_date >= NOW())
    ";

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

    $sql .= ' LIMIT 1';

    $stmt = $db->prepare($sql);
    $stmt->bindValue('id', $announcementId, PDO::PARAM_INT);

    if ($userProfile) {
        $stmt->bindValue('college_id', $userProfile['college_id']);
        $stmt->bindValue('program_id', $userProfile['program_id']);
        $stmt->bindValue('section_id', $userProfile['section_id']);
        $stmt->bindValue('batch_year', $userProfile['batch_year']);
    }

    $stmt->execute();
    $announcement = $stmt->fetch();

    if (!$announcement) {
        respondError('Announcement not found', 404);
    }

    if ($user) {
        $stmt = $db->prepare('INSERT IGNORE INTO announcement_reads (announcement_id, user_id, read_at) VALUES (?, ?, NOW())');
        $stmt->execute([(int)$announcement['id'], (int)$user['id']]);
    }

    respondSuccess($announcement);
} catch (Exception $e) {
    respondError('Failed to load announcement: ' . $e->getMessage(), 500);
}
