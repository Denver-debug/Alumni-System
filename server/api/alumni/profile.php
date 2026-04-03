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
    
    // Get user profile with alumni details
    $stmt = $db->prepare("
        SELECT 
            u.id, u.alumni_id, u.email, u.name, u.role, u.profile_image,
            u.email_verified, u.auth_provider, u.created_at, u.last_login,
            ap.college_id, ap.program_id, ap.section_id,
            ap.batch_year, ap.graduation_year, ap.total_points, ap.badge_level,
            ap.custom_fields,
            c.name as college_name, c.code as college_code,
            p.name as program_name, p.code as program_code,
            s.name as section_name
        FROM users u
        LEFT JOIN alumni_profiles ap ON u.id = ap.user_id
        LEFT JOIN colleges c ON ap.college_id = c.id
        LEFT JOIN programs p ON ap.program_id = p.id
        LEFT JOIN sections s ON ap.section_id = s.id
        WHERE u.id = :user_id
    ");
    $stmt->execute(['user_id' => $user['id']]);
    $profile = $stmt->fetch();
    
    if (!$profile) {
        respondError('Profile not found', 404);
    }
    
    // Parse custom fields JSON
    if ($profile['custom_fields']) {
        $profile['custom_fields'] = json_decode($profile['custom_fields'], true);
    } else {
        $profile['custom_fields'] = [];
    }
    
    // Remove sensitive data
    unset($profile['password']);
    
    respondSuccess($profile);
    
} catch (Exception $e) {
    respondError('Failed to load profile: ' . $e->getMessage(), 500);
}
