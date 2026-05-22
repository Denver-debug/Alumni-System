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

    $cacheFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'alumni_admin_dashboard_cache.json';
    $cacheTtl = 45;

    if (is_file($cacheFile)) {
        $cacheAge = time() - (int) filemtime($cacheFile);
        if ($cacheAge >= 0 && $cacheAge < $cacheTtl) {
            $cached = json_decode((string) file_get_contents($cacheFile), true);
            if (is_array($cached)) {
                respondSuccess($cached);
            }
        }
    }

    $stats = [];

    $stmt = $db->query("SELECT COUNT(*) FROM users WHERE role = 'alumni'");
    $stats['total_alumni'] = (int) $stmt->fetchColumn();

    $stmt = $db->query("SELECT COUNT(*) FROM users WHERE role = 'alumni' AND last_login >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
    $stats['active_alumni'] = (int) $stmt->fetchColumn();

    $stmt = $db->query("SELECT COUNT(*) FROM users WHERE role = 'alumni' AND verification_status = 'pending'");
    $stats['pending_registrations'] = (int) $stmt->fetchColumn();

    $stmt = $db->query("SELECT COUNT(*) FROM events WHERE status IN ('upcoming', 'ongoing')");
    $stats['active_events'] = (int) $stmt->fetchColumn();

    $stmt = $db->query("SELECT COALESCE(SUM(points), 0) FROM point_transactions WHERE type = 'earned'");
    $stats['total_points'] = (int) $stmt->fetchColumn();

    $stmt = $db->query(<<<SQL
        SELECT c.name, COUNT(ap.user_id) AS count
        FROM colleges c
        LEFT JOIN alumni_profiles ap ON c.id = ap.college_id
        GROUP BY c.id, c.name
        ORDER BY count DESC
SQL
    );
    $stats['alumni_by_college'] = $stmt->fetchAll();

    $stmt = $db->query(<<<SQL
        SELECT DATE_FORMAT(created_at, '%Y-%m') AS month,
               COUNT(*) AS count
        FROM users
        WHERE role = 'alumni'
          AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
        GROUP BY DATE_FORMAT(created_at, '%Y-%m')
        ORDER BY month ASC
SQL
    );
    $stats['registrations_trend'] = $stmt->fetchAll();

    $stmt = $db->query(<<<SQL
        SELECT COALESCE(NULLIF(TRIM(LOWER(ap.employment_status)), ''), 'not_specified') AS status_key,
               COUNT(*) AS count
        FROM alumni_profiles ap
        INNER JOIN users u ON ap.user_id = u.id
        WHERE u.role = 'alumni'
          AND u.verification_status = 'verified'
        GROUP BY status_key
        ORDER BY count DESC
SQL
    );
    $employmentRows = $stmt->fetchAll();
    $employmentMap = [
        'employed' => 0,
        'self_employed' => 0,
        'unemployed' => 0,
        'student' => 0,
        'not_specified' => 0,
    ];
    foreach ($employmentRows as $row) {
        $statusKey = (string) (isset($row['status_key']) ? $row['status_key'] : 'not_specified');
        if (!array_key_exists($statusKey, $employmentMap)) {
            $statusKey = 'not_specified';
        }
        $employmentMap[$statusKey] = (int) (isset($row['count']) ? $row['count'] : 0);
    }
    $stats['employment_trends'] = $employmentMap;
    $stats['employed_alumni'] = (int) $employmentMap['employed'] + (int) $employmentMap['self_employed'];
    $stats['unemployed_alumni'] = (int) $employmentMap['unemployed'];

    $stmt = $db->query(<<<SQL
        SELECT COALESCE(ap.graduation_year, ap.batch_year) AS year,
               COUNT(*) AS count
        FROM alumni_profiles ap
        INNER JOIN users u ON ap.user_id = u.id
        WHERE u.role = 'alumni'
          AND COALESCE(ap.graduation_year, ap.batch_year) IS NOT NULL
        GROUP BY COALESCE(ap.graduation_year, ap.batch_year)
        ORDER BY year ASC
SQL
    );
    $stats['alumni_by_graduation_year'] = $stmt->fetchAll();

    $stmt = $db->query(<<<SQL
        SELECT ap.batch_year AS batch,
               COUNT(*) AS count
        FROM alumni_profiles ap
        INNER JOIN users u ON ap.user_id = u.id
        WHERE u.role = 'alumni'
          AND ap.batch_year IS NOT NULL
        GROUP BY ap.batch_year
        ORDER BY ap.batch_year DESC
SQL
    );
    $batchRows = $stmt->fetchAll();
    $stats['alumni_by_batch'] = $batchRows;
    $batchTotal = 0;
    foreach ($batchRows as $row) {
        $batchTotal += (int) (isset($row['count']) ? $row['count'] : 0);
    }
    $stats['average_alumni_per_batch'] = count($batchRows) > 0
        ? round($batchTotal / count($batchRows), 1)
        : 0;

    $stmt = $db->query(<<<SQL
        SELECT ap.batch_year AS batch,
               COUNT(*) AS count
        FROM alumni_profiles ap
        INNER JOIN users u ON ap.user_id = u.id
        WHERE u.role = 'alumni'
          AND ap.batch_year IS NOT NULL
          AND LOWER(COALESCE(ap.employment_status, '')) IN ('employed', 'self_employed')
        GROUP BY ap.batch_year
        ORDER BY count DESC, ap.batch_year DESC
        LIMIT 8
SQL
    );
    $stats['top_employed_batches'] = $stmt->fetchAll();

    $stmt = $db->query(<<<SQL
        SELECT DATE_FORMAT(COALESCE(ea.check_in_time, ea.created_at), '%Y-%m') AS month,
               DATE_FORMAT(COALESCE(ea.check_in_time, ea.created_at), '%b %Y') AS month_label,
               COUNT(*) AS count
        FROM event_attendances ea
        WHERE COALESCE(ea.check_in_time, ea.created_at) >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
        GROUP BY DATE_FORMAT(COALESCE(ea.check_in_time, ea.created_at), '%Y-%m'),
                 DATE_FORMAT(COALESCE(ea.check_in_time, ea.created_at), '%b %Y')
        ORDER BY month ASC
SQL
    );
    $stats['event_attendance_trend'] = $stmt->fetchAll();

    $stmt = $db->query(<<<SQL
        SELECT ap.batch_year AS batch,
               COUNT(ea.id) AS count
        FROM event_attendances ea
        INNER JOIN alumni_profiles ap ON ea.user_id = ap.user_id
        INNER JOIN users u ON ap.user_id = u.id
        WHERE u.role = 'alumni'
          AND ap.batch_year IS NOT NULL
        GROUP BY ap.batch_year
        ORDER BY count DESC, ap.batch_year DESC
        LIMIT 8
SQL
    );
    $stats['top_active_batches'] = $stmt->fetchAll();

    $stmt = $db->query(<<<SQL
        SELECT u.id,
               u.name,
               u.alumni_id,
               ap.batch_year,
               COALESCE(ap.total_points, 0) AS total_points,
               COUNT(ea.id) AS events_attended
        FROM users u
        LEFT JOIN alumni_profiles ap ON u.id = ap.user_id
        LEFT JOIN event_attendances ea ON u.id = ea.user_id
        WHERE u.role = 'alumni'
        GROUP BY u.id, u.name, u.alumni_id, ap.batch_year, ap.total_points
        ORDER BY events_attended DESC, total_points DESC, u.name ASC
        LIMIT 8
SQL
    );
    $stats['top_active_alumni'] = $stmt->fetchAll();

    $stmt = $db->query(<<<SQL
        SELECT DATE_FORMAT(created_at, '%Y-%m-01') AS month_key,
               DATE_FORMAT(created_at, '%b %Y') AS month_label,
               COUNT(*) AS count
        FROM users
        WHERE role = 'alumni'
          AND created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
        GROUP BY DATE_FORMAT(created_at, '%Y-%m-01'), DATE_FORMAT(created_at, '%b %Y')
        ORDER BY month_key ASC
SQL
    );
    $stats['user_growth'] = $stmt->fetchAll();

    $stmt = $db->query("SELECT COUNT(*) FROM users WHERE role = 'alumni' AND last_login >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
    $activeUsers30d = (int) $stmt->fetchColumn();

    $stmt = $db->query("SELECT COUNT(*) FROM users WHERE role = 'alumni'");
    $totalAlumni = (int) $stmt->fetchColumn();

    $stmt = $db->query("SELECT COALESCE(AVG(total_points), 0) FROM alumni_profiles ap INNER JOIN users u ON ap.user_id = u.id WHERE u.role = 'alumni' AND u.verification_status = 'verified'");
    $avgPoints = (float) $stmt->fetchColumn();

    $stmt = $db->query("SELECT COUNT(*) FROM announcements WHERE status = 'published'");
    $publishedAnnouncements = (int) $stmt->fetchColumn();

    $momentumStmt = $db->query(<<<SQL
        SELECT DATE_FORMAT(created_at, '%Y-%m') AS month_key,
               COUNT(*) AS count
        FROM users
        WHERE role = 'alumni'
          AND created_at >= DATE_SUB(CURDATE(), INTERVAL 2 MONTH)
        GROUP BY DATE_FORMAT(created_at, '%Y-%m')
        ORDER BY month_key ASC
SQL
    );
    $momentumRows = $momentumStmt->fetchAll();
    $registrationMomentum = 0;
    if (count($momentumRows) >= 2) {
        $previousMonth = (int) (isset($momentumRows[count($momentumRows) - 2]['count']) ? $momentumRows[count($momentumRows) - 2]['count'] : 0);
        $currentMonth = (int) (isset($momentumRows[count($momentumRows) - 1]['count']) ? $momentumRows[count($momentumRows) - 1]['count'] : 0);
        if ($previousMonth > 0) {
            $registrationMomentum = round((($currentMonth - $previousMonth) / $previousMonth) * 100, 1);
        }
    }

    $stats['engagement_metrics'] = [
        'active_users_30d' => $activeUsers30d,
        'active_users_rate' => $totalAlumni > 0 ? round(($activeUsers30d / $totalAlumni) * 100, 1) : 0,
        'avg_points_per_alumni' => round($avgPoints, 1),
        'registration_momentum' => $registrationMomentum,
        'published_announcements' => $publishedAnnouncements,
    ];

    $stmt = $db->query("SELECT COUNT(*) FROM events WHERE MONTH(event_date) = MONTH(CURDATE()) AND YEAR(event_date) = YEAR(CURDATE())");
    $stats['events_this_month'] = (int) $stmt->fetchColumn();

    $stmt = $db->query("SELECT COUNT(*) FROM announcements WHERE status = 'published'");
    $stats['active_announcements'] = (int) $stmt->fetchColumn();

    @file_put_contents($cacheFile, json_encode($stats, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

    respondSuccess($stats);
} catch (Exception $e) {
    respondError('Failed to load dashboard: ' . $e->getMessage(), 500);
}
