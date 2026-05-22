<?php
/**
 * Analytics API
 * Provides dashboard statistics and analytics data
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../utils/helpers.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uri = $_SERVER['REQUEST_URI'] ?? '';

try {
    requireAdmin();

    $db = Database::getInstance();
    $conn = $db->getConnection();

    // GET /api/v1/admin/analytics/dashboard
    if ($method === 'GET' && preg_match('/\/admin\/analytics\/dashboard$/', $uri)) {
        $statsStmt = $conn->query("SELECT * FROM v_alumni_stats");
        $stats = $statsStmt->fetch() ?: [];

        $byCollegeStmt = $conn->query("SELECT * FROM v_alumni_by_college");
        $byCollege = $byCollegeStmt->fetchAll();

        $byBatchStmt = $conn->query("SELECT * FROM v_alumni_by_batch LIMIT 10");
        $byBatch = $byBatchStmt->fetchAll();

        $topProgramsStmt = $conn->query("SELECT * FROM v_top_programs");
        $topPrograms = $topProgramsStmt->fetchAll();

        $recentStmt = $conn->query("
            SELECT
                u.id,
                u.name,
                u.email,
                c.name AS college_name,
                u.verification_status,
                u.created_at
            FROM users u
            LEFT JOIN alumni_profiles ap ON u.id = ap.user_id
            LEFT JOIN colleges c ON ap.college_id = c.id
            WHERE u.role = 'alumni'
            ORDER BY u.created_at DESC
            LIMIT 10
        ");
        $recentRegistrations = $recentStmt->fetchAll();

        $eventStatsStmt = $conn->query("SELECT * FROM v_event_stats");
        $eventStats = $eventStatsStmt->fetch() ?: [];

        $trendStmt = $conn->query("
            SELECT * FROM v_registration_trend
            ORDER BY month DESC
            LIMIT 6
        ");
        $registrationTrend = $trendStmt->fetchAll();

        $growthPercentage = 0;
        if (count($registrationTrend) >= 2) {
            $currentMonth = (int) ($registrationTrend[0]['registrations'] ?? 0);
            $previousMonth = (int) ($registrationTrend[1]['registrations'] ?? 0);
            if ($previousMonth > 0) {
                $growthPercentage = round((($currentMonth - $previousMonth) / $previousMonth) * 100, 1);
            }
        }

        $engagementStmt = $conn->query("
            SELECT
                (SELECT COUNT(*) FROM messages WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)) AS messages_last_week,
                (SELECT COUNT(DISTINCT user_id) FROM event_attendances WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)) AS event_participants_month,
                (SELECT AVG(ap.total_points)
                   FROM alumni_profiles ap
                   INNER JOIN users u ON ap.user_id = u.id
                   WHERE u.role = 'alumni' AND u.verification_status = 'verified') AS avg_points
        ");
        $engagement = $engagementStmt->fetch() ?: [];

        respondSuccess([
            'stats' => $stats,
            'by_college' => $byCollege,
            'by_batch' => $byBatch,
            'top_programs' => $topPrograms,
            'recent_registrations' => $recentRegistrations,
            'event_stats' => $eventStats,
            'registration_trend' => array_reverse($registrationTrend),
            'growth_percentage' => $growthPercentage,
            'engagement' => $engagement,
        ]);
    }

    // GET /api/v1/admin/analytics/alumni-distribution
    if ($method === 'GET' && preg_match('/\/admin\/analytics\/alumni-distribution$/', $uri)) {
        $locationStmt = $conn->query("
            SELECT
                ap.address_province AS province,
                ap.address_city AS city,
                COUNT(*) AS count
            FROM alumni_profiles ap
            INNER JOIN users u ON ap.user_id = u.id
            WHERE u.role = 'alumni'
              AND u.verification_status = 'verified'
              AND ap.address_province IS NOT NULL
            GROUP BY ap.address_province, ap.address_city
            ORDER BY count DESC
            LIMIT 20
        ");
        $byLocation = $locationStmt->fetchAll();

        $employmentStmt = $conn->query("
            SELECT
                ap.employment_status,
                COUNT(*) AS count
            FROM alumni_profiles ap
            INNER JOIN users u ON ap.user_id = u.id
            WHERE u.role = 'alumni'
              AND u.verification_status = 'verified'
              AND ap.employment_status IS NOT NULL
            GROUP BY ap.employment_status
            ORDER BY count DESC
        ");
        $byEmployment = $employmentStmt->fetchAll();

        $industryStmt = $conn->query("
            SELECT
                ap.industry,
                COUNT(*) AS count
            FROM alumni_profiles ap
            INNER JOIN users u ON ap.user_id = u.id
            WHERE u.role = 'alumni'
              AND u.verification_status = 'verified'
              AND ap.industry IS NOT NULL
            GROUP BY ap.industry
            ORDER BY count DESC
            LIMIT 10
        ");
        $byIndustry = $industryStmt->fetchAll();

        respondSuccess([
            'by_location' => $byLocation,
            'by_employment' => $byEmployment,
            'by_industry' => $byIndustry,
        ]);
    }

    // GET /api/v1/admin/analytics/engagement
    if ($method === 'GET' && preg_match('/\/admin\/analytics\/engagement$/', $uri)) {
        $dauStmt = $conn->query("
            SELECT
                DATE(last_login) AS date,
                COUNT(DISTINCT id) AS active_users
            FROM users
            WHERE role = 'alumni'
              AND verification_status = 'verified'
              AND last_login >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            GROUP BY DATE(last_login)
            ORDER BY date DESC
        ");
        $dailyActiveUsers = $dauStmt->fetchAll();

        $attendanceStmt = $conn->query("
            SELECT
                e.id,
                e.title,
                e.event_date,
                COUNT(ea.id) AS attendees,
                e.max_attendees AS max_participants,
                ROUND((COUNT(ea.id) / NULLIF(e.max_attendees, 0)) * 100, 1) AS attendance_rate
            FROM events e
            LEFT JOIN event_attendances ea ON e.id = ea.event_id
            WHERE e.event_date >= DATE_SUB(CURDATE(), INTERVAL 90 DAY)
              AND e.status IN ('upcoming', 'ongoing', 'completed')
            GROUP BY e.id, e.title, e.event_date, e.max_attendees
            ORDER BY e.event_date DESC
            LIMIT 10
        ");
        $eventAttendance = $attendanceStmt->fetchAll();

        $pointsStmt = $conn->query("
            SELECT
                CASE
                    WHEN ap.total_points = 0 THEN '0'
                    WHEN ap.total_points BETWEEN 1 AND 50 THEN '1-50'
                    WHEN ap.total_points BETWEEN 51 AND 100 THEN '51-100'
                    WHEN ap.total_points BETWEEN 101 AND 200 THEN '101-200'
                    WHEN ap.total_points BETWEEN 201 AND 500 THEN '201-500'
                    ELSE '500+'
                END AS range,
                COUNT(*) AS count
            FROM alumni_profiles ap
            INNER JOIN users u ON ap.user_id = u.id
            WHERE u.role = 'alumni'
              AND u.verification_status = 'verified'
            GROUP BY range
            ORDER BY MIN(ap.total_points)
        ");
        $pointsDistribution = $pointsStmt->fetchAll();

        respondSuccess([
            'daily_active_users' => array_reverse($dailyActiveUsers),
            'event_attendance' => $eventAttendance,
            'points_distribution' => $pointsDistribution,
        ]);
    }

    // GET /api/v1/admin/analytics/export
    if ($method === 'GET' && preg_match('/\/admin\/analytics\/export$/', $uri)) {
        $type = $_GET['type'] ?? 'alumni';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $type . '_export_' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');

        if ($type === 'alumni') {
            fputcsv($output, ['ID', 'Name', 'Email', 'College', 'Program', 'Batch Year', 'Status', 'Registered']);

            $stmt = $conn->query("
                SELECT
                    u.id,
                    u.name,
                    u.email,
                    c.name AS college,
                    p.name AS program,
                    ap.batch_year,
                    u.verification_status,
                    u.created_at
                FROM users u
                LEFT JOIN alumni_profiles ap ON u.id = ap.user_id
                LEFT JOIN colleges c ON ap.college_id = c.id
                LEFT JOIN programs p ON ap.program_id = p.id
                WHERE u.role = 'alumni'
                ORDER BY u.created_at DESC
            ");

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                fputcsv($output, $row);
            }
        }

        fclose($output);
        exit;
    }

    respondError('Endpoint not found', 404);
} catch (Throwable $e) {
    $status = (int) $e->getCode();
    if ($status < 400 || $status >= 600) {
        $status = 500;
    }
    respondError($e->getMessage(), $status);
}
