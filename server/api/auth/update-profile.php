<?php
/**
 * Update Profile API
 * POST|PUT /api/v1/auth/profile
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
$existingUser = $db->fetchOne('SELECT profile_image FROM users WHERE id = ?', [$currentUser['id']]) ?: [];
$previousProfileImage = $existingUser['profile_image'] ?? null;

// Handle file upload for profile image
$profileImage = null;
if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] !== UPLOAD_ERR_NO_FILE) {
    $uploader = new FileUploader(PROFILE_UPLOAD_SUBDIR, ALLOWED_IMAGE_TYPES);
    $filename = $uploader->upload($_FILES['profile_image']);

    if (!$filename) {
        error('Failed to upload profile image: ' . implode(', ', $uploader->getErrors()));
    }

    $profileImage = $uploader->getUrl($filename);

    // Create thumbnail
    $sourcePath = UPLOAD_DIR . '/' . PROFILE_UPLOAD_SUBDIR . '/' . $filename;
    $thumbPath = UPLOAD_DIR . '/' . PROFILE_UPLOAD_SUBDIR . '/thumb_' . $filename;
    if (is_file($sourcePath) && !ImageProcessor::thumbnail($sourcePath, $thumbPath, 150)) {
        error_log('Profile update: thumbnail generation skipped (GD extension may be unavailable).');
    }
}

// Get request data (from POST data if file upload, otherwise JSON body)
$data = !empty($_POST) ? $_POST : getRequestBody();

// Validate user data
$userErrors = validate($data, [
    'name' => 'min:2|max:255'
]);

if (!empty($userErrors)) {
    validationError($userErrors);
}

try {
    $db->beginTransaction();
    
    // Update user table
    $userData = [];
    if (isset($data['name']) && trim((string)$data['name']) !== '') {
        $userData['name'] = trim((string)$data['name']);
    }

    if ($profileImage) {
        $userData['profile_image'] = $profileImage;
    }

    if (array_key_exists('campus_id', $data)) {
        $userData['campus_id'] = ($data['campus_id'] === '' || $data['campus_id'] === null) ? null : (int)$data['campus_id'];
    }

    if (!empty($userData)) {
        $db->update('users', $userData, 'id = ?', [$currentUser['id']]);
    }
    
    // Check if alumni profile exists
    $profile = $db->fetchOne("SELECT id FROM alumni_profiles WHERE user_id = ?", [$currentUser['id']]);

    if (!$profile) {
        $profileId = $db->insert('alumni_profiles', [
            'user_id' => $currentUser['id']
        ]);

        $profile = [
            'id' => $profileId
        ];
    }
    
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
        'campus_id', 'college_id', 'program_id', 'section_id', 'batch_year', 'graduation_year', 'student_id'
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
        $db->update('alumni_profiles', $profileData, 'user_id = ?', [$currentUser['id']]);
    }
    
    // Check profile completion
    $updatedProfile = $db->fetchOne("SELECT * FROM alumni_profiles WHERE user_id = ?", [$currentUser['id']]);

    if (
        !empty($updatedProfile['campus_id']) &&
        !empty($updatedProfile['college_id']) &&
        !empty($updatedProfile['graduation_year'])
    ) {
        $alumniIdPrefix = getAlumniIdProfilePrefix(
            $db,
            (int)$updatedProfile['campus_id'],
            (int)$updatedProfile['college_id'],
            (int)$updatedProfile['graduation_year']
        );

        if ($alumniIdPrefix) {
            $currentUserData = $db->fetchOne("SELECT alumni_id FROM users WHERE id = ?", [$currentUser['id']]);
            $currentAlumniId = strtoupper((string)($currentUserData['alumni_id'] ?? ''));
            $sequenceLength = 5;
            $expectedPattern = '/^' . preg_quote(strtoupper($alumniIdPrefix), '/') . '-\d{' . $sequenceLength . '}$/';

            if (!preg_match($expectedPattern, $currentAlumniId)) {
                $newAlumniId = generateAlumniId(
                    $db,
                    (int)$updatedProfile['campus_id'],
                    (int)$updatedProfile['college_id'],
                    (int)$updatedProfile['graduation_year']
                );

                if ($newAlumniId) {
                    $db->update('users', ['alumni_id' => $newAlumniId], 'id = ?', [$currentUser['id']]);
                }
            }
        }
    }
    
    $completionRequiredFields = [
        'campus_id', 'college_id', 'program_id', 'section_id', 'batch_year', 'graduation_year', 'student_id',
        'first_name', 'middle_name', 'last_name', 'suffix',
        'gender', 'birthdate', 'civil_status', 'mobile',
        'address_street', 'address_city', 'address_province',
        'employment_status', 'current_employer', 'job_title', 'company_address',
        'industry', 'monthly_salary_range',
        'linkedin_url', 'facebook_url', 'instagram_url',
    ];

    if (!empty($data['complete_profile'])) {
        $missing = [];
        foreach ($completionRequiredFields as $field) {
            if (empty($updatedProfile[$field])) {
                $missing[$field] = ucwords(str_replace('_', ' ', $field)) . ' is required';
            }
        }

        if (!empty($missing)) {
            $db->rollback();
            validationError($missing);
        }
    }

    $requiredFields = ['campus_id', 'college_id', 'program_id', 'section_id', 'batch_year', 'graduation_year', 'first_name', 'last_name', 'gender', 'birthdate', 'mobile'];
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

    try {
        logAdminActivity($currentUser['id'], 'profile_updated', 'User updated their profile');
    } catch (Throwable $logError) {
        error_log('Profile update activity log failed: ' . $logError->getMessage());
    }
    
    $updatedUser = $db->fetchOne(
        "SELECT id, alumni_id, email, name, role, profile_image, status, verification_status, email_verified
         FROM users
         WHERE id = ?",
        [$currentUser['id']]
    );

    if ($profileImage && $previousProfileImage && $previousProfileImage !== $profileImage) {
        deleteProfileUploadFile($previousProfileImage);
    }

    success([
        'user' => $updatedUser ?: [],
        'profile_image' => $updatedUser['profile_image'] ?? $profileImage,
    ], 'Profile updated successfully');
    
} catch (Exception $e) {
    try {
        if ($db->getConnection()->inTransaction()) {
            $db->rollback();
        }
    } catch (Throwable $rollbackError) {
        error_log('Profile update rollback failed: ' . $rollbackError->getMessage());
    }
    error_log("Profile update error: " . $e->getMessage());
    serverError('Failed to update profile');
}
