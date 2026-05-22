<?php
/**
 * Alumni API - Get Profile
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../utils/helpers.php';
require_once __DIR__ . '/../../middleware/auth.php';

// Require authentication
requireAuth();

try {
    $db = Database::getInstance()->getConnection();
    $user = getCurrentUser();
    
    $profile = null;

    try {
        // Get user profile with alumni details
        $stmt = $db->prepare("
            SELECT 
                u.id, u.alumni_id, u.email, u.name, u.role, u.profile_image,
                u.email_verified, u.auth_provider, u.created_at, u.last_login,
                ap.campus_id, ap.college_id, ap.program_id, ap.section_id,
                ap.batch_year, ap.graduation_year, ap.student_id,
                ap.first_name, ap.middle_name, ap.last_name, ap.suffix, ap.nickname,
                ap.gender, ap.birthdate, ap.civil_status, ap.nationality, ap.religion,
                ap.phone, ap.mobile,
                ap.address_street, ap.address_barangay, ap.address_city, ap.address_province,
                ap.address_region, ap.address_zip, ap.address_country,
                ap.employment_status, ap.current_employer, ap.job_title, ap.company_address,
                ap.industry, ap.monthly_salary_range,
                ap.linkedin_url, ap.facebook_url, ap.twitter_url, ap.instagram_url,
                ap.total_points, ap.badge_level, ap.profile_completed, ap.profile_completed_at,
                cam.name as campus_name, cam.code as campus_code,
                c.name as college_name, c.code as college_code,
                p.name as program_name, p.code as program_code,
                s.name as section_name
            FROM users u
            LEFT JOIN alumni_profiles ap ON u.id = ap.user_id
            LEFT JOIN campuses cam ON ap.campus_id = cam.id
            LEFT JOIN colleges c ON ap.college_id = c.id
            LEFT JOIN programs p ON ap.program_id = p.id
            LEFT JOIN sections s ON ap.section_id = s.id
            WHERE u.id = :user_id
        ");
        $stmt->execute(['user_id' => $user['id']]);
        $profile = $stmt->fetch();
    } catch (Throwable $queryError) {
        error_log('Alumni profile query failed: ' . $queryError->getMessage());

        $stmt = $db->prepare("
            SELECT 
                u.id, u.alumni_id, u.email, u.name, u.role, u.profile_image,
                u.email_verified, u.auth_provider, u.created_at, u.last_login,
                ap.campus_id, ap.college_id, ap.program_id, ap.section_id,
                ap.batch_year, ap.graduation_year, ap.student_id,
                ap.first_name, ap.middle_name, ap.last_name, ap.suffix, ap.nickname,
                ap.gender, ap.birthdate, ap.civil_status, ap.nationality, ap.religion,
                ap.phone, ap.mobile,
                ap.address_street, ap.address_barangay, ap.address_city, ap.address_province,
                ap.address_region, ap.address_zip, ap.address_country,
                ap.employment_status, ap.current_employer, ap.job_title, ap.company_address,
                ap.industry, ap.monthly_salary_range,
                ap.linkedin_url, ap.facebook_url, ap.twitter_url, ap.instagram_url,
                ap.total_points, ap.badge_level, ap.profile_completed, ap.profile_completed_at
            FROM users u
            LEFT JOIN alumni_profiles ap ON u.id = ap.user_id
            WHERE u.id = :user_id
        ");
        $stmt->execute(['user_id' => $user['id']]);
        $profile = $stmt->fetch();
    }
    
    if (!$profile) {
        respondError('Profile not found', 404);
    }

    $profile = processUserData($profile);
    
    // Keep compatibility for clients that still read this key.
    $profile['custom_fields'] = [];
    
    // Remove sensitive data
    unset($profile['password']);
    
    respondSuccess($profile);
    
} catch (Exception $e) {
    respondError('Failed to load profile: ' . $e->getMessage(), 500);
}
