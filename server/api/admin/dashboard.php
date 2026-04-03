<?php
/**
 * Admin Dashboard Stats API
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../utils/helpers.php';
require_once __DIR__ . '/../../middleware/auth.php';

requireAdmin();

try {
    $db = Database::getInstance()->getConnection();
    
    // Get basic stats
    $stats = [];
    
    // Total alumni
    $stmt = $db->query("SELECT COUNT(*) FROM users WHERE role = 'alumni'");
    $stats['total_alumni'] = (int)$stmt->fetchColumn();
    
    // Active alumni (logged in last 30 days)
    $stmt = $db->query("SELECT COUNT(*) FROM users WHERE role = 'alumni' AND last_login >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
    $stats['active_alumni'] = (int)$stmt->fetchColumn();
    
    // Pending registrations
    $stmt = $db->query("SELECT COUNT(*) FROM pending_registrations WHERE expires_at > NOW()");
    $stats['pending_registrations'] = (int)$stmt->fetchColumn();
    
    // Active events
    $stmt = $db->query("SELECT COUNT(*) FROM events WHERE status IN ('upcoming', 'ongoing')");
    $stats['active_events'] = (int)$stmt->fetchColumn();
    
    // Total points distributed
    $stmt = $db->query("SELECT COALESCE(SUM(points), 0) FROM point_transactions WHERE type = 'earned'");
    $stats['total_points'] = (int)$stmt->fetchColumn();
    
    // Alumni by college
    $stmt = $db->query("
        SELECT c.name, COUNT(ap.user_id) as count
        FROM colleges c
        LEFT JOIN alumni_profiles ap ON c.id = ap.college_id
        GROUP BY c.id, c.name
        ORDER BY count DESC
    ");
    $stats['alumni_by_college'] = $stmt->fetchAll();
    
    // Registrations trend (last 6 months)
    $stmt = $db->query("
        SELECT 
            DATE_FORMAT(created_at, '%Y-%m') as month,
            COUNT(*) as count
        FROM users 
        WHERE role = 'alumni' 
        AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
        GROUP BY DATE_FORMAT(created_at, '%Y-%m')
        ORDER BY month ASC
    ");
    $stats['registrations_trend'] = $stmt->fetchAll();
    
    // Events this month
    $stmt = $db->query("
        SELECT COUNT(*) FROM events 
        WHERE MONTH(event_date) = MONTH(CURDATE()) 
        AND YEAR(event_date) = YEAR(CURDATE())
    ");
    $stats['events_this_month'] = (int)$stmt->fetchColumn();
    
    // Announcements count
    $stmt = $db->query("SELECT COUNT(*) FROM announcements WHERE status = 'published'");
    $stats['active_announcements'] = (int)$stmt->fetchColumn();
    
    respondSuccess($stats);
    
} catch (Exception $e) {
    respondError('Failed to load dashboard: ' . $e->getMessage(), 500);
}
