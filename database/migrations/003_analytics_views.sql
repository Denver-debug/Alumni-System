-- =====================================================
-- Migration 003: Analytics Views
-- Creates views used by /admin/analytics/* endpoints
-- =====================================================

CREATE OR REPLACE VIEW v_alumni_stats AS
    SELECT
        COUNT(*) AS total_alumni,
        SUM(CASE WHEN u.verification_status = 'verified' THEN 1 ELSE 0 END) AS verified_count,
        SUM(CASE WHEN u.verification_status = 'pending'  THEN 1 ELSE 0 END) AS pending_count,
        SUM(CASE WHEN u.verification_status = 'rejected' THEN 1 ELSE 0 END) AS rejected_count,
        SUM(CASE WHEN u.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) AS new_this_month,
        SUM(CASE WHEN u.last_login  >= DATE_SUB(NOW(), INTERVAL 7  DAY) THEN 1 ELSE 0 END) AS active_last_week,
        SUM(CASE WHEN u.last_login  >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) AS active_this_month
    FROM users u
    WHERE u.role = 'alumni';

CREATE OR REPLACE VIEW v_alumni_by_college AS
    SELECT
        c.id,
        c.name,
        c.code,
        COUNT(ap.id)                                                             AS alumni_count,
        SUM(CASE WHEN u.verification_status = 'verified' THEN 1 ELSE 0 END) AS verified_count,
        SUM(CASE WHEN u.verification_status = 'pending'  THEN 1 ELSE 0 END) AS pending_count
    FROM colleges c
    LEFT JOIN alumni_profiles ap ON c.id = ap.college_id
    LEFT JOIN users u ON ap.user_id = u.id
    GROUP BY c.id, c.name, c.code
    ORDER BY alumni_count DESC;

CREATE OR REPLACE VIEW v_alumni_by_batch AS
    SELECT
        ap.batch_year,
        COUNT(*)                                                                     AS count,
        SUM(CASE WHEN u.verification_status = 'verified' THEN 1 ELSE 0 END) AS verified_count
    FROM alumni_profiles ap
    JOIN users u ON ap.user_id = u.id
    WHERE ap.batch_year IS NOT NULL
    GROUP BY ap.batch_year
    ORDER BY ap.batch_year DESC;

CREATE OR REPLACE VIEW v_top_programs AS
    SELECT
        p.id,
        p.name,
        p.code,
        c.name AS college_name,
        COUNT(ap.id) AS alumni_count,
        SUM(CASE WHEN ap.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) AS new_this_month,
        SUM(CASE WHEN u.verification_status = 'verified' THEN 1 ELSE 0 END) AS verified_count
    FROM programs p
    LEFT JOIN colleges c ON p.college_id = c.id
    LEFT JOIN alumni_profiles ap ON p.id = ap.program_id
    LEFT JOIN users u ON ap.user_id = u.id
    GROUP BY p.id, p.name, p.code, c.name
    ORDER BY alumni_count DESC
    LIMIT 10;

CREATE OR REPLACE VIEW v_event_stats AS
    SELECT
        COUNT(*)                                                                                  AS total_events,
        SUM(CASE WHEN event_date >= CURDATE() AND status != 'cancelled' THEN 1 ELSE 0 END)  AS upcoming_events,
        SUM(CASE WHEN event_date <  CURDATE() THEN 1 ELSE 0 END)                             AS past_events,
        SUM(CASE WHEN event_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) AS events_this_month,
        (SELECT COUNT(*)          FROM event_attendances) AS total_attendance,
        (SELECT COUNT(DISTINCT user_id) FROM event_attendances) AS unique_attendees
    FROM events
    WHERE status NOT IN ('draft', 'cancelled');

CREATE OR REPLACE VIEW v_registration_trend AS
    SELECT
        DATE_FORMAT(created_at, '%Y-%m') AS month,
        COUNT(*) AS registrations,
        SUM(CASE WHEN verification_status = 'verified' THEN 1 ELSE 0 END) AS verified
    FROM users
    WHERE role = 'alumni'
      AND created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
    ORDER BY month ASC;

SELECT 'Migration 003: Analytics Views - COMPLETE' AS status;
