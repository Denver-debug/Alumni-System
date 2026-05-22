-- ============================================
-- Analytics Views for Admin Dashboard
-- ============================================

-- Alumni statistics view
CREATE OR REPLACE VIEW v_alumni_stats AS
SELECT 
  COUNT(*) as total_alumni,
  SUM(CASE WHEN verification_status = 'verified' THEN 1 ELSE 0 END) as verified_count,
  SUM(CASE WHEN verification_status = 'pending' THEN 1 ELSE 0 END) as pending_count,
  SUM(CASE WHEN verification_status = 'rejected' THEN 1 ELSE 0 END) as rejected_count,
  SUM(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) as new_this_month,
  SUM(CASE WHEN last_login >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 ELSE 0 END) as active_last_week,
  SUM(CASE WHEN last_login >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) as active_this_month
FROM users
WHERE role = 'alumni';

-- Alumni by college view
CREATE OR REPLACE VIEW v_alumni_by_college AS
SELECT 
  c.id,
  c.name,
  c.code,
  COUNT(u.id) as alumni_count,
  SUM(CASE WHEN u.verification_status = 'verified' THEN 1 ELSE 0 END) as verified_count,
  SUM(CASE WHEN u.verification_status = 'pending' THEN 1 ELSE 0 END) as pending_count
FROM colleges c
LEFT JOIN alumni_profiles ap ON c.id = ap.college_id
LEFT JOIN users u ON ap.user_id = u.id AND u.role = 'alumni'
GROUP BY c.id, c.name, c.code
ORDER BY alumni_count DESC;

-- Alumni by batch year view
CREATE OR REPLACE VIEW v_alumni_by_batch AS
SELECT 
  ap.batch_year,
  COUNT(u.id) as count,
  SUM(CASE WHEN u.verification_status = 'verified' THEN 1 ELSE 0 END) as verified_count
FROM alumni_profiles ap
LEFT JOIN users u ON ap.user_id = u.id AND u.role = 'alumni'
WHERE ap.batch_year IS NOT NULL
GROUP BY batch_year
ORDER BY batch_year DESC;

-- Top programs view
CREATE OR REPLACE VIEW v_top_programs AS
SELECT 
  p.id,
  p.name,
  p.code,
  c.name as college_name,
  COUNT(u.id) as alumni_count,
  SUM(CASE WHEN u.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) as new_this_month,
  SUM(CASE WHEN u.verification_status = 'verified' THEN 1 ELSE 0 END) as verified_count
FROM programs p
LEFT JOIN colleges c ON p.college_id = c.id
LEFT JOIN alumni_profiles ap ON p.id = ap.program_id
LEFT JOIN users u ON ap.user_id = u.id AND u.role = 'alumni'
GROUP BY p.id, p.name, p.code, c.name
ORDER BY alumni_count DESC
LIMIT 10;

-- Event statistics view
CREATE OR REPLACE VIEW v_event_stats AS
SELECT 
  COUNT(*) as total_events,
  SUM(CASE WHEN event_date >= CURDATE() THEN 1 ELSE 0 END) as upcoming_events,
  SUM(CASE WHEN event_date < CURDATE() THEN 1 ELSE 0 END) as past_events,
  SUM(CASE WHEN event_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) as events_this_month,
  (SELECT COUNT(*) FROM event_attendances) as total_attendance,
  (SELECT COUNT(DISTINCT user_id) FROM event_attendances) as unique_attendees
FROM events
WHERE status = 'published';

-- Monthly registration trend view
CREATE OR REPLACE VIEW v_registration_trend AS
SELECT 
  DATE_FORMAT(created_at, '%Y-%m') as month,
  COUNT(*) as registrations,
  SUM(CASE WHEN verification_status = 'verified' THEN 1 ELSE 0 END) as verified
FROM users
WHERE role = 'alumni'
  AND created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
GROUP BY DATE_FORMAT(created_at, '%Y-%m')
ORDER BY month DESC;
