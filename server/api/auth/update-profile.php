<?php
/**
 * Update Profile API
 * PUT /api/v1/auth/profile
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../utils/helpers.php';
require_once __DIR__ . '/../../utils/validators.php';
require_once __DIR__ . '/../../utils/uploads.php';
require_once __DIR__ . '/../../middleware/auth.php';

// Require authentication
$currentUser = requireAuth();

$db = Database::getInstance();

// Handle file upload for profile image
$profileImage = null;
if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
    $uploader = new FileUploader('profiles', ALLOWED_IMAGE_TYPES);
    $filename = $uploader->upload($_FILES['profile_image']);
    
    if (!$filename) {
        error('Failed to upload profile image: ' . implode(', ', $uploader->getErrors()));
    }
    
    $profileImage = $uploader->getUrl($filename);
    
    // Create thumbnail
    $sourcePath = UPLOAD_DIR . '/profiles/' . $filename;
    $thumbPath = UPLOAD_DIR . '/profiles/thumb_' . $filename;
    ImageProcessor::thumbnail($sourcePath, $thumbPath, 150);
}

// Get request data (from POST data if file upload, otherwise JSON body)
$data = !empty($_POST) ? $_POST : getRequestBody();

// Validate user data
$userErrors = validate($data, [
    'name' => 'required|min:2|max:255'
]);

if (!empty($userErrors)) {
    validationError($userErrors);
}

try {
    $db->beginTransaction();
    
    // Update user table
    $userData = ['name' => $data['name']];
    if ($profileImage) {
        $userData['profile_image'] = $profileImage;
    }
    
    $db->update('users', $userData, 'id = ?', [$currentUser['id']]);
    
    // Check if alumni profile exists
    $profile = $db->fetchOne("SELECT id FROM alumni_profiles WHERE user_id = ?", [$currentUser['id']]);
    
    // Prepare profile data
    $profileFields = [
        'first_name', 'middle_name', 'last_name', 'suffix', 'nickname',
        'gender', 'birthdate', 'civil_status', 'nationality', 'religion',
        'phone', 'mobile',
        'address_street', 'address_barangay', 'address_city', 'address_province',
        'address_region', 'address_zip', 'address_country',
        'employment_status', 'current_employer', 'job_title', 'company_address',
        'industry', 'monthly_salary_range',
        'linkedin_url', 'facebook_url', 'twitter_url', 'instagram_url',
        'college_id', 'program_id', 'section_id', 'batch_year', 'graduation_year', 'student_id'
    ];
    
    $profileData = [];
    foreach ($profileFields as $field) {
        if (array_key_exists($field, $data)) {
            $value = $data[$field];
            // Convert empty strings to null for optional fields
            $profileData[$field] = ($value === '' || $value === null) ? null : $value;
        }
    }
    
    if (!empty($profileData)) {
        if ($profile) {
            $db->update('alumni_profiles', $profileData, 'user_id = ?', [$currentUser['id']]);
        } else {
            $profileData['user_id'] = $currentUser['id'];
            $db->insert('alumni_profiles', $profileData);
        }
    }
    
    // Update alumni ID if college changed
    if (isset($data['college_id']) && $data['college_id']) {
        $college = $db->fetchOne("SELECT code FROM colleges WHERE id = ?", [$data['college_id']]);
        
        if ($college) {
            $currentUserData = $db->fetchOne("SELECT alumni_id FROM users WHERE id = ?", [$currentUser['id']]);
            
            // Check if alumni ID has default code
            if (strpos($currentUserData['alumni_id'], 'ALM-' . date('Y') . '-GEN') === 0) {
                $newAlumniId = generateAlumniId($college['code']);
                $db->update('users', ['alumni_id' => $newAlumniId], 'id = ?', [$currentUser['id']]);
            }
        }
    }
    
    // Check profile completion
    $updatedProfile = $db->fetchOne("SELECT * FROM alumni_profiles WHERE user_id = ?", [$currentUser['id']]);
    
    $requiredFields = ['first_name', 'last_name', 'gender', 'birthdate', 'phone', 'college_id', 'program_id', 'batch_year'];
    $isComplete = true;
    
    foreach ($requiredFields as $field) {
        if (empty($updatedProfile[$field])) {
            $isComplete = false;
            break;
        }
    }
    
    // Award profile completion points if newly completed
    if ($isComplete && !$updatedProfile['profile_completed']) {
        $db->update('alumni_profiles', [
            'profile_completed' => true,
            'profile_completed_at' => date('Y-m-d H:i:s'),
            'total_points' => $updatedProfile['total_points'] + POINTS_PROFILE_COMPLETION
        ], 'user_id = ?', [$currentUser['id']]);
        
        $db->insert('point_transactions', [
            'user_id' => $currentUser['id'],
            'points' => POINTS_PROFILE_COMPLETION,
            'type' => 'earned',
            'source' => 'profile_completion',
            'description' => 'Profile completion bonus',
            'balance_after' => $updatedProfile['total_points'] + POINTS_PROFILE_COMPLETION
        ]);
        
        // Update badge level
        $newTotal = $updatedProfile['total_points'] + POINTS_PROFILE_COMPLETION;
        $newBadge = BadgeLevel::getForPoints($newTotal);
        
        if ($newBadge !== $updatedProfile['badge_level']) {
            $db->update('alumni_profiles', ['badge_level' => $newBadge], 'user_id = ?', [$currentUser['id']]);
        }
    }
    
    $db->commit();
    
    logAdminActivity($currentUser['id'], 'profile_updated', 'User updated their profile');
    
    success([], 'Profile updated successfully');
    
} catch (Exception $e) {
    $db->rollback();
    error_log("Profile update error: " . $e->getMessage());
    serverError('Failed to update profile');
}
