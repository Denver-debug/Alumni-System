<?php
/**
 * Gamification API - Get Leaderboard
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../utils/helpers.php';

try {
    $db = Database::getInstance()->getConnection();
    
    $limit = min((int)($_GET['limit'] ?? 20), 100);
    $period = $_GET['period'] ?? 'all'; // all, month, week
    
    // Build query based on period
    $sql = "
        SELECT 
            u.id, u.alumni_id, u.name, u.profile_image,
            ap.total_points, ap.badge_level,
            c.name as college_name,
            (SELECT COUNT(*) FROM event_attendances WHERE user_id = u.id AND status = 'attended') as events_attended
        FROM users u
        INNER JOIN alumni_profiles ap ON u.id = ap.user_id
        LEFT JOIN colleges c ON ap.college_id = c.id
        WHERE u.role = 'alumni' AND u.status = 'active'
    ";
    
    if ($period === 'month') {
        $sql = "
            SELECT 
                u.id, u.alumni_id, u.name, u.profile_image,
                COALESCE(SUM(pt.points), 0) as total_points,
                ap.badge_level,
                c.name as college_name,
                (SELECT COUNT(*) FROM event_attendances ea WHERE ea.user_id = u.id AND ea.status = 'attended' AND MONTH(ea.check_in_time) = MONTH(CURDATE())) as events_attended
            FROM users u
            INNER JOIN alumni_profiles ap ON u.id = ap.user_id
            LEFT JOIN colleges c ON ap.college_id = c.id
            LEFT JOIN point_transactions pt ON u.id = pt.user_id AND pt.type = 'earned' AND MONTH(pt.created_at) = MONTH(CURDATE()) AND YEAR(pt.created_at) = YEAR(CURDATE())
            WHERE u.role = 'alumni' AND u.status = 'active'
            GROUP BY u.id
        ";
    } elseif ($period === 'week') {
        $sql = "
            SELECT 
                u.id, u.alumni_id, u.name, u.profile_image,
                COALESCE(SUM(pt.points), 0) as total_points,
                ap.badge_level,
                c.name as college_name,
                (SELECT COUNT(*) FROM event_attendances ea WHERE ea.user_id = u.id AND ea.status = 'attended' AND WEEK(ea.check_in_time) = WEEK(CURDATE())) as events_attended
            FROM users u
            INNER JOIN alumni_profiles ap ON u.id = ap.user_id
            LEFT JOIN colleges c ON ap.college_id = c.id
            LEFT JOIN point_transactions pt ON u.id = pt.user_id AND pt.type = 'earned' AND WEEK(pt.created_at) = WEEK(CURDATE()) AND YEAR(pt.created_at) = YEAR(CURDATE())
            WHERE u.role = 'alumni' AND u.status = 'active'
            GROUP BY u.id
        ";
    }
    
    $sql .= " ORDER BY total_points DESC LIMIT :limit";
    
    $stmt = $db->prepare($sql);
    $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    
    $leaderboard = $stmt->fetchAll();
    
    respondSuccess([
        'leaderboard' => $leaderboard,
        'period' => $period
    ]);
    
} catch (Exception $e) {
    respondError('Failed to load leaderboard: ' . $e->getMessage(), 500);
}
