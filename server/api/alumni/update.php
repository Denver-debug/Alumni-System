<?php
/**
 * Alumni API - Update Profile
 * PUT|POST /api/alumni/profile
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../utils/helpers.php';
require_once __DIR__ . '/../../utils/uploads.php';
require_once __DIR__ . '/../../middleware/auth.php';

$user = requireAuth();

if (!in_array($_SERVER['REQUEST_METHOD'], ['PUT', 'POST'], true)) {
    respondError('Method not allowed', 405);
}

try {
    $db = Database::getInstance()->getConnection();
    $data = getRequestBody();
    $existingUserStmt = $db->prepare('SELECT profile_image FROM users WHERE id = :user_id LIMIT 1');
    $existingUserStmt->execute(['user_id' => $user['id']]);
    $existingUser = $existingUserStmt->fetch() ?: [];
    $previousProfileImage = $existingUser['profile_image'] ?? null;

    if (empty($data) && !empty($_POST)) {
        $data = $_POST;
    }

    $db->beginTransaction();

    $profileColumnsStmt = $db->query('SHOW COLUMNS FROM alumni_profiles');
    $profileColumns = $profileColumnsStmt->fetchAll(PDO::FETCH_COLUMN);
    $hasProfileColumn = array_fill_keys($profileColumns, true);

    $userUpdates = [];
    $userParams = ['user_id' => $user['id']];

    if (isset($data['name']) && trim((string)$data['name']) !== '') {
        $userUpdates[] = 'name = :name';
        $userParams['name'] = sanitize((string)$data['name']);
    }

    if (array_key_exists('campus_id', $data)) {
        $userUpdates[] = 'campus_id = :campus_id';
        $userParams['campus_id'] = ($data['campus_id'] === '' || $data['campus_id'] === null) ? null : (int)$data['campus_id'];
    }

    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $uploader = new FileUploader(PROFILE_UPLOAD_SUBDIR, ALLOWED_IMAGE_TYPES);
        $filename = $uploader->upload($_FILES['profile_image']);

        if (!$filename) {
            respondError('Failed to upload profile image: ' . implode(', ', $uploader->getErrors()), 400);
        }

        // Store the full URL path to ensure it persists across sessions
        $imageUrl = $uploader->getUrl($filename);
        $userUpdates[] = 'profile_image = :profile_image';
        $userParams['profile_image'] = $imageUrl;

        // Log the image upload for debugging
        error_log("Profile image uploaded: {$imageUrl} for user {$user['id']}");
    }

    if (!empty($userUpdates)) {
        $sql = 'UPDATE users SET ' . implode(', ', $userUpdates) . ', updated_at = NOW() WHERE id = :user_id';
        $stmt = $db->prepare($sql);
        $stmt->execute($userParams);
    }

    $stmt = $db->prepare('SELECT id, profile_completed FROM alumni_profiles WHERE user_id = :user_id LIMIT 1');
    $stmt->execute(['user_id' => $user['id']]);
    $existingProfile = $stmt->fetch();
    $profileExists = (bool)$existingProfile;

    if (!$profileExists) {
        $stmt = $db->prepare(
            "INSERT INTO alumni_profiles (user_id, total_points, badge_level, created_at, updated_at)
             VALUES (:user_id, 0, 'bronze', NOW(), NOW())"
        );
        $stmt->execute(['user_id' => $user['id']]);
    }

    $profileFields = [
        'campus_id', 'college_id', 'program_id', 'section_id', 'batch_year', 'graduation_year', 'student_id',
        'first_name', 'middle_name', 'last_name', 'suffix', 'nickname',
        'gender', 'birthdate', 'civil_status', 'nationality', 'religion',
        'phone', 'mobile',
        'address_street', 'address_barangay', 'address_city', 'address_province',
        'address_region', 'address_zip', 'address_country',
        'employment_status', 'current_employer', 'job_title', 'company_address',
        'industry', 'monthly_salary_range',
        'linkedin_url', 'facebook_url', 'twitter_url', 'instagram_url',
    ];
    $profileFields = array_values(array_filter($profileFields, static function ($field) use ($hasProfileColumn) {
        return isset($hasProfileColumn[$field]);
    }));

    $profileUpdates = [];
    $profileParams = ['user_id' => $user['id']];

    foreach ($profileFields as $field) {
        if (!array_key_exists($field, $data)) {
            continue;
        }

        $value = $data[$field];
        $profileUpdates[] = "$field = :$field";
        $profileParams[$field] = ($value === '' || $value === null) ? null : $value;
    }

    if (!empty($profileUpdates)) {
        $sql = 'UPDATE alumni_profiles SET ' . implode(', ', $profileUpdates) . ', updated_at = NOW() WHERE user_id = :user_id';
        $stmt = $db->prepare($sql);
        $stmt->execute($profileParams);
    }

    $stmt = $db->prepare('SELECT * FROM alumni_profiles WHERE user_id = :user_id LIMIT 1');
    $stmt->execute(['user_id' => $user['id']]);
    $profile = $stmt->fetch();

    if (!$profile) {
        throw new RuntimeException('Unable to load updated profile');
    }

    if (
        !empty($profile['campus_id']) &&
        !empty($profile['college_id']) &&
        !empty($profile['graduation_year'])
    ) {
        $alumniIdPrefix = getAlumniIdProfilePrefix(
            $db,
            (int)$profile['campus_id'],
            (int)$profile['college_id'],
            (int)$profile['graduation_year']
        );

        if ($alumniIdPrefix) {
            $stmt = $db->prepare('SELECT alumni_id FROM users WHERE id = :user_id LIMIT 1');
            $stmt->execute(['user_id' => $user['id']]);
            $currentAlumniId = strtoupper((string)($stmt->fetch()['alumni_id'] ?? ''));
            $sequenceLength = 5;
            $expectedPattern = '/^' . preg_quote(strtoupper($alumniIdPrefix), '/') . '-\d{' . $sequenceLength . '}$/';

            if (!preg_match($expectedPattern, $currentAlumniId)) {
                $newAlumniId = generateAlumniId(
                    $db,
                    (int)$profile['campus_id'],
                    (int)$profile['college_id'],
                    (int)$profile['graduation_year']
                );

                if ($newAlumniId) {
                    $stmt = $db->prepare('UPDATE users SET alumni_id = :alumni_id, updated_at = NOW() WHERE id = :user_id');
                    $stmt->execute([
                        'alumni_id' => $newAlumniId,
                        'user_id' => $user['id'],
                    ]);
                }
            }
        }
    }

    $completionRequiredFields = [
        'campus_id', 'college_id', 'program_id', 'section_id', 'batch_year', 'graduation_year', 'student_id',
        'first_name', 'middle_name', 'last_name',
        'gender', 'birthdate', 'civil_status', 'mobile',
        'address_street', 'address_city', 'address_province',
        'employment_status', 'current_employer', 'job_title', 'company_address',
        'industry', 'monthly_salary_range',
    ];

    if (!empty($data['complete_profile'])) {
        $missing = [];
        foreach ($completionRequiredFields as $field) {
            if (isset($hasProfileColumn[$field]) && empty($profile[$field])) {
                $missing[$field] = ucwords(str_replace('_', ' ', $field)) . ' is required';
            }
        }

        if (!empty($missing)) {
            $db->rollBack();
            validationError($missing);
        }
    }

    $requiredFields = ['campus_id', 'college_id', 'program_id', 'section_id', 'batch_year', 'graduation_year', 'first_name', 'last_name', 'gender', 'birthdate', 'mobile'];
    $requiredFields = array_values(array_filter($requiredFields, static function ($field) use ($hasProfileColumn) {
        return isset($hasProfileColumn[$field]);
    }));

    $isComplete = !empty($requiredFields);
    foreach ($requiredFields as $field) {
        if (empty($profile[$field])) {
            $isComplete = false;
            break;
        }
    }

    $pointsToAward = 0;
    $pointsDescription = '';

    if ($isComplete && isset($hasProfileColumn['profile_completed']) && empty($profile['profile_completed'])) {
        $pointsToAward = (int)POINTS_PROFILE_COMPLETION;
        $pointsDescription = 'Profile completion bonus';

        $completionAssignments = ['profile_completed = 1'];
        if (isset($hasProfileColumn['profile_completed_at'])) {
            $completionAssignments[] = 'profile_completed_at = NOW()';
        }

        $stmt = $db->prepare('UPDATE alumni_profiles SET ' . implode(', ', $completionAssignments) . ' WHERE user_id = :user_id');
        $stmt->execute(['user_id' => $user['id']]);
    } elseif (!empty($profileUpdates)) {
        $stmt = $db->prepare(
            "SELECT COUNT(*)
             FROM point_transactions
             WHERE user_id = :user_id
               AND source = 'profile_completion'
               AND DATE(created_at) = CURDATE()"
        );
        $stmt->execute(['user_id' => $user['id']]);

        if ((int)$stmt->fetchColumn() === 0) {
            $pointsToAward = (int)POINTS_PROFILE_UPDATE;
            $pointsDescription = 'Profile update';
        }
    }

    if ($pointsToAward > 0 && isset($hasProfileColumn['total_points'])) {
        $pointsAssignments = ['total_points = total_points + :points'];
        if (isset($hasProfileColumn['updated_at'])) {
            $pointsAssignments[] = 'updated_at = NOW()';
        }

        $stmt = $db->prepare('UPDATE alumni_profiles SET ' . implode(', ', $pointsAssignments) . ' WHERE user_id = :user_id');
        $stmt->execute([
            'points' => $pointsToAward,
            'user_id' => $user['id'],
        ]);

        $stmt = $db->prepare('SELECT COALESCE(total_points, 0) as total_points FROM alumni_profiles WHERE user_id = :user_id');
        $stmt->execute(['user_id' => $user['id']]);
        $totalPoints = (int)($stmt->fetch()['total_points'] ?? 0);

        if (isset($hasProfileColumn['badge_level'])) {
            $badgeLevel = BadgeLevel::getForPoints($totalPoints);
            $badgeAssignments = ['badge_level = :badge_level'];
            if (isset($hasProfileColumn['updated_at'])) {
                $badgeAssignments[] = 'updated_at = NOW()';
            }

            $stmt = $db->prepare('UPDATE alumni_profiles SET ' . implode(', ', $badgeAssignments) . ' WHERE user_id = :user_id');
            $stmt->execute([
                'badge_level' => $badgeLevel,
                'user_id' => $user['id'],
            ]);
        }

        $stmt = $db->prepare(
            "INSERT INTO point_transactions (
                user_id, points, type, source, description, balance_after, created_at
            ) VALUES (
                :user_id, :points, 'earned', 'profile_completion', :description, :balance_after, NOW()
            )"
        );
        $stmt->execute([
            'user_id' => $user['id'],
            'points' => $pointsToAward,
            'description' => $pointsDescription,
            'balance_after' => $totalPoints,
        ]);
    }

    $db->commit();

    $stmt = $db->prepare(
        "SELECT
            u.id, u.alumni_id, u.email, u.name, u.profile_image,
            ap.*,
            c.name as college_name, c.code as college_code,
            p.name as program_name, p.code as program_code,
            s.name as section_name
         FROM users u
         LEFT JOIN alumni_profiles ap ON u.id = ap.user_id
         LEFT JOIN colleges c ON ap.college_id = c.id
         LEFT JOIN programs p ON ap.program_id = p.id
         LEFT JOIN sections s ON ap.section_id = s.id
         WHERE u.id = :user_id"
    );
    $stmt->execute(['user_id' => $user['id']]);
    $updatedProfile = $stmt->fetch();

    if (!empty($imageUrl) && $previousProfileImage && $previousProfileImage !== $imageUrl) {
        deleteProfileUploadFile($previousProfileImage);
    }

    respondSuccess([
        'profile' => $updatedProfile,
        'points_awarded' => $pointsToAward,
        'message' => 'Profile updated successfully',
    ]);
} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    respondError('Failed to update profile: ' . $e->getMessage(), 500);
}

