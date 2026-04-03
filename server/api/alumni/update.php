<?php
/**
 * Alumni API - Update Profile
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../utils/helpers.php';
require_once __DIR__ . '/../../utils/validators.php';
require_once __DIR__ . '/../../utils/uploads.php';
require_once __DIR__ . '/../../middleware/auth.php';

// Require authentication
requireAuth();

try {
    $db = Database::getInstance()->getConnection();
    $user = getCurrentUser();
    $data = getRequestBody();
    
    // Begin transaction
    $db->beginTransaction();
    
    // Update user table fields
    $userUpdates = [];
    $userParams = ['user_id' => $user['id']];
    
    if (isset($data['name'])) {
        $userUpdates[] = 'name = :name';
        $userParams['name'] = sanitize($data['name']);
    }
    
    // Handle profile image upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $imagePath = uploadProfileImage($_FILES['profile_image'], $user['id']);
        $userUpdates[] = 'profile_image = :profile_image';
        $userParams['profile_image'] = $imagePath;
    }
    
    if (!empty($userUpdates)) {
        $sql = "UPDATE users SET " . implode(', ', $userUpdates) . ", updated_at = NOW() WHERE id = :user_id";
        $stmt = $db->prepare($sql);
        $stmt->execute($userParams);
    }
    
    // Check if alumni profile exists
    $stmt = $db->prepare("SELECT id FROM alumni_profiles WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $user['id']]);
    $profileExists = $stmt->fetch();
    
    // Prepare alumni profile data
    $profileData = [
        'college_id' => $data['college_id'] ?? null,
        'program_id' => $data['program_id'] ?? null,
        'section_id' => $data['section_id'] ?? null,
        'batch_year' => $data['batch_year'] ?? null,
        'graduation_year' => $data['graduation_year'] ?? null
    ];
    
    // Get existing custom fields and merge with new ones
    $customFields = [];
    if ($profileExists) {
        $stmt = $db->prepare("SELECT custom_fields FROM alumni_profiles WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $user['id']]);
        $existing = $stmt->fetchColumn();
        $customFields = $existing ? json_decode($existing, true) : [];
    }
    
    // Get dynamic form fields
    $stmt = $db->prepare("SELECT field_name FROM form_fields WHERE is_active = 1");
    $stmt->execute();
    $formFields = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Update custom fields from request
    foreach ($formFields as $fieldName) {
        if (isset($data[$fieldName])) {
            $customFields[$fieldName] = sanitize($data[$fieldName]);
        }
    }
    
    $profileData['custom_fields'] = json_encode($customFields);
    
    // Insert or update alumni profile
    if ($profileExists) {
        $stmt = $db->prepare("
            UPDATE alumni_profiles SET
                college_id = :college_id,
                program_id = :program_id,
                section_id = :section_id,
                batch_year = :batch_year,
                graduation_year = :graduation_year,
                custom_fields = :custom_fields,
                updated_at = NOW()
            WHERE user_id = :user_id
        ");
        $profileData['user_id'] = $user['id'];
    } else {
        $stmt = $db->prepare("
            INSERT INTO alumni_profiles (user_id, college_id, program_id, section_id, batch_year, graduation_year, custom_fields, created_at, updated_at)
            VALUES (:user_id, :college_id, :program_id, :section_id, :batch_year, :graduation_year, :custom_fields, NOW(), NOW())
        ");
        $profileData['user_id'] = $user['id'];
    }
    
    $stmt->execute($profileData);
    
    // Award points for profile update (first time or significant update)
    $pointsToAward = 0;
    $isFirstProfileComplete = !$profileExists && !empty($data['college_id']) && !empty($data['batch_year']);
    
    if ($isFirstProfileComplete) {
        $pointsToAward = POINTS_PROFILE_COMPLETE;
        $description = 'Completed profile';
    } else {
        // Check if this is the first update today
        $stmt = $db->prepare("
            SELECT COUNT(*) FROM point_transactions 
            WHERE user_id = :user_id AND type = 'earned' 
            AND description = 'Profile update' AND DATE(created_at) = CURDATE()
        ");
        $stmt->execute(['user_id' => $user['id']]);
        if ($stmt->fetchColumn() == 0) {
            $pointsToAward = POINTS_PROFILE_UPDATE;
            $description = 'Profile update';
        }
    }
    
    if ($pointsToAward > 0) {
        // Add points
        $stmt = $db->prepare("
            INSERT INTO point_transactions (user_id, points, type, description, created_at)
            VALUES (:user_id, :points, 'earned', :description, NOW())
        ");
        $stmt->execute([
            'user_id' => $user['id'],
            'points' => $pointsToAward,
            'description' => $description
        ]);
        
        // Update total points
        $stmt = $db->prepare("
            UPDATE alumni_profiles 
            SET total_points = total_points + :points,
                badge_level = CASE 
                    WHEN total_points + :points >= 5000 THEN 'Diamond'
                    WHEN total_points + :points >= 1000 THEN 'Platinum'
                    WHEN total_points + :points >= 500 THEN 'Gold'
                    WHEN total_points + :points >= 100 THEN 'Silver'
                    ELSE 'Bronze'
                END
            WHERE user_id = :user_id
        ");
        $stmt->execute([
            'points' => $pointsToAward,
            'user_id' => $user['id']
        ]);
    }
    
    $db->commit();
    
    // Return updated profile
    $stmt = $db->prepare("
        SELECT 
            u.id, u.alumni_id, u.email, u.name, u.profile_image,
            ap.college_id, ap.program_id, ap.section_id,
            ap.batch_year, ap.graduation_year, ap.total_points, ap.badge_level
        FROM users u
        LEFT JOIN alumni_profiles ap ON u.id = ap.user_id
        WHERE u.id = :user_id
    ");
    $stmt->execute(['user_id' => $user['id']]);
    $profile = $stmt->fetch();
    
    respondSuccess([
        'profile' => $profile,
        'points_awarded' => $pointsToAward,
        'message' => 'Profile updated successfully'
    ]);
    
} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    respondError('Failed to update profile: ' . $e->getMessage(), 500);
}
