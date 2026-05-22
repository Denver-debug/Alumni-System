<?php
/**
 * Alumni API - Get ID Card
 * Generates alumni ID card data with QR code
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
    
    // Get complete alumni profile with all details
    $stmt = $db->prepare("
        SELECT 
            u.id, u.alumni_id, u.email, u.name, u.profile_image, u.status,
            u.created_at,
            ap.student_id, ap.graduation_year, ap.batch_year,
            ap.first_name, ap.middle_name, ap.last_name, ap.suffix,
            ap.birthdate, ap.phone, ap.mobile,
            ap.address_street, ap.address_city, ap.address_province,
            ap.employment_status, ap.current_employer, ap.job_title,
            c.name as college_name, c.code as college_code,
            p.name as program_name, p.code as program_code,
            s.name as section_name
        FROM users u
        LEFT JOIN alumni_profiles ap ON u.id = ap.user_id
        LEFT JOIN colleges c ON ap.college_id = c.id
        LEFT JOIN programs p ON ap.program_id = p.id
        LEFT JOIN sections s ON ap.section_id = s.id
        WHERE u.id = :user_id AND u.role = 'alumni'
    ");
    $stmt->execute(['user_id' => $user['id']]);
    $profile = $stmt->fetch();
    
    if (!$profile) {
        respondError('Alumni profile not found', 404);
    }
    
    // Ensure profile image URL is absolute
    $profileImageUrl = $profile['profile_image'];
    if ($profileImageUrl && !preg_match('/^https?:\/\//i', $profileImageUrl)) {
        // Make sure the path starts with /
        if (!str_starts_with($profileImageUrl, '/')) {
            $profileImageUrl = '/' . $profileImageUrl;
        }
    }
    
    // Generate QR code data (JSON encoded for scanning)
    $qrData = json_encode([
        'type' => 'alumni_id',
        'alumni_id' => $profile['alumni_id'],
        'user_id' => $profile['id'],
        'name' => $profile['name'],
        'student_id' => $profile['student_id'],
        'timestamp' => time()
    ]);
    
    // Prepare ID card data
    $idCard = [
        'alumni_id' => $profile['alumni_id'],
        'student_id' => $profile['student_id'],
        'name' => $profile['name'],
        'first_name' => $profile['first_name'],
        'middle_name' => $profile['middle_name'],
        'last_name' => $profile['last_name'],
        'suffix' => $profile['suffix'],
        'email' => $profile['email'],
        'phone' => $profile['phone'] ?: $profile['mobile'],
        'address' => trim(implode(', ', array_filter([
            $profile['address_street'],
            $profile['address_city'],
            $profile['address_province']
        ]))),
        'employment_status' => $profile['employment_status'],
        'current_employer' => $profile['current_employer'],
        'job_title' => $profile['job_title'],
        'profile_image' => $profileImageUrl,
        'college_name' => $profile['college_name'],
        'college_code' => $profile['college_code'],
        'program_name' => $profile['program_name'],
        'program_code' => $profile['program_code'],
        'section_name' => $profile['section_name'],
        'batch_year' => $profile['batch_year'],
        'graduation_year' => $profile['graduation_year'],
        'birthdate' => $profile['birthdate'],
        'member_since' => date('Y', strtotime($profile['created_at'])),
        'status' => $profile['status'],
        'qr_code_data' => $qrData,
        'issued_date' => date('Y-m-d'),
        'valid_until' => null // Alumni IDs don't expire
    ];
    
    respondSuccess($idCard);
    
} catch (Exception $e) {
    error_log("ID Card Error: " . $e->getMessage());
    respondError('Failed to generate ID card: ' . $e->getMessage(), 500);
}
