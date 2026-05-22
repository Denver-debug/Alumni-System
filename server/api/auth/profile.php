<?php
/**
 * Get Profile API
 * GET /api/v1/auth/profile
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../utils/helpers.php';
require_once __DIR__ . '/../../middleware/auth.php';

// Require authentication
$currentUser = requireAuth();

$db = Database::getInstance();

try {
    $user = null;

    try {
        // Get full user data with profile
        $user = $db->fetchOne("
            SELECT 
                u.id,
                u.alumni_id,
                u.email,
                u.name,
                u.role,
                u.profile_image,
                u.auth_provider,
                u.email_verified,
                u.status,
                u.verification_status,
                u.verified_at,
                u.rejection_reason,
                u.last_login,
                u.created_at,
                p.id as profile_id,
                p.campus_id,
                p.college_id,
                p.program_id,
                p.section_id,
                p.batch_year,
                p.graduation_year,
                p.student_id,
                p.first_name,
                p.middle_name,
                p.last_name,
                p.suffix,
                p.nickname,
                p.gender,
                p.birthdate,
                p.civil_status,
                p.nationality,
                p.religion,
                p.phone,
                p.mobile,
                p.address_street,
                p.address_barangay,
                p.address_city,
                p.address_province,
                p.address_region,
                p.address_zip,
                p.address_country,
                p.employment_status,
                p.current_employer,
                p.job_title,
                p.company_address,
                p.industry,
                p.monthly_salary_range,
                p.linkedin_url,
                p.facebook_url,
                p.twitter_url,
                p.instagram_url,
                p.total_points,
                p.badge_level,
                p.profile_completed,
                p.profile_completed_at,
                c.name as college_name,
                c.code as college_code,
                cam.name as campus_name,
                cam.code as campus_code,
                pr.name as program_name,
                pr.code as program_code,
                s.name as section_name
            FROM users u
            LEFT JOIN alumni_profiles p ON u.id = p.user_id
            LEFT JOIN campuses cam ON p.campus_id = cam.id
            LEFT JOIN colleges c ON p.college_id = c.id
            LEFT JOIN programs pr ON p.program_id = pr.id
            LEFT JOIN sections s ON p.section_id = s.id
            WHERE u.id = ?
        ", [$currentUser['id']]);
    } catch (Throwable $queryError) {
        error_log('Auth profile query failed: ' . $queryError->getMessage());
        $user = $db->fetchOne("
            SELECT 
                u.id,
                u.alumni_id,
                u.email,
                u.name,
                u.role,
                u.profile_image,
                u.auth_provider,
                u.email_verified,
                u.status,
                u.verification_status,
                u.verified_at,
                u.rejection_reason,
                u.last_login,
                u.created_at
            FROM users u
            WHERE u.id = ?
        ", [$currentUser['id']]);
    }

    if (!$user) {
        notFound('User not found');
    }

    $unreadMessagesCount = 0;
    $unreadAnnouncementsCount = 0;
    $upcomingEventsCount = 0;

    try {
        $unreadMessages = $db->fetchOne("
            SELECT COUNT(*) as count
            FROM messages m
            JOIN conversation_participants cp ON m.conversation_id = cp.conversation_id
            WHERE cp.user_id = ? 
            AND m.sender_id != ?
            AND (cp.last_read_at IS NULL OR m.created_at > cp.last_read_at)
        ", [$currentUser['id'], $currentUser['id']]);
        $unreadMessagesCount = (int)($unreadMessages['count'] ?? 0);
    } catch (Throwable $notificationError) {
        error_log('Auth profile unread messages query failed: ' . $notificationError->getMessage());
    }

    try {
        $unreadAnnouncements = $db->fetchOne("
            SELECT COUNT(*) as count
            FROM announcements a
            LEFT JOIN announcement_reads ar ON a.id = ar.announcement_id AND ar.user_id = ?
            WHERE a.status = 'published'
            AND (a.publish_date IS NULL OR a.publish_date <= NOW())
            AND (a.expire_date IS NULL OR a.expire_date > NOW())
            AND ar.id IS NULL
        ", [$currentUser['id']]);
        $unreadAnnouncementsCount = (int)($unreadAnnouncements['count'] ?? 0);
    } catch (Throwable $notificationError) {
        error_log('Auth profile unread announcements query failed: ' . $notificationError->getMessage());
    }

    try {
        $upcomingEvents = $db->fetchOne("
            SELECT COUNT(*) as count
            FROM events e
            WHERE e.status IN ('upcoming', 'ongoing')
            AND e.event_date >= CURDATE()
        ", []);
        $upcomingEventsCount = (int)($upcomingEvents['count'] ?? 0);
    } catch (Throwable $notificationError) {
        error_log('Auth profile upcoming events query failed: ' . $notificationError->getMessage());
    }

    success([
        'user' => processUserData($user),
        'notifications' => [
            'unread_messages' => $unreadMessagesCount,
            'unread_announcements' => $unreadAnnouncementsCount,
            'upcoming_events' => $upcomingEventsCount,
        ]
    ]);
} catch (Throwable $error) {
    error_log('Auth profile error: ' . $error->getMessage());
    serverError('Failed to load profile');
}
