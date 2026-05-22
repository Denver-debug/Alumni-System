<?php
/**
 * Admin Update Alumni API
 * PUT /api/admin/alumni/{id} - Update alumni info
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../middleware/auth.php';
require_once __DIR__ . '/../../../config/auth.php';
require_once __DIR__ . '/../../../utils/helpers.php';

header('Content-Type: application/json');

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$userId = $GLOBALS['url_params']['id'] ?? null;
$currentUser = getCurrentUser();
$userRole = $currentUser['role'] ?? 'alumni';
$userCampusId = $currentUser['campus_id'] ?? null;

if (!$userId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Alumni ID required']);
    exit;
}

if (in_array($userRole, ['campus_admin', 'staff'], true) && !$userCampusId) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Campus assignment required']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true) ?? [];
if (!is_array($data)) {
    $data = [];
}

try {
    $db = Database::getInstance()->getConnection();
    
    $stmt = $db->prepare("SELECT id FROM users WHERE id = ? AND role = 'alumni'");
    $stmt->execute([$userId]);
    if (!$stmt->fetch()) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Alumni not found']);
        exit;
    }

    if (in_array($userRole, ['campus_admin', 'staff'], true) && $userCampusId) {
        $campusCheck = $db->prepare(
            "SELECT COALESCE(ap.campus_id, u.campus_id) AS campus_id
             FROM users u
             LEFT JOIN alumni_profiles ap ON u.id = ap.user_id
             WHERE u.id = ?
             LIMIT 1"
        );
        $campusCheck->execute([$userId]);
        $allowedCampus = $campusCheck->fetch();

        if (!$allowedCampus || (int)($allowedCampus['campus_id'] ?? 0) !== (int)$userCampusId) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Alumni not found']);
            exit;
        }
    }
    
    $db->beginTransaction();

    $nullIfEmpty = static function ($value) {
        if ($value === null) {
            return null;
        }

        if (is_string($value)) {
            $trimmed = trim($value);
            return $trimmed === '' ? null : $trimmed;
        }

        return $value;
    };

    $toNullableInt = static function ($value) use ($nullIfEmpty): ?int {
        $value = $nullIfEmpty($value);
        if ($value === null || $value === '') {
            return null;
        }

        return is_numeric($value) ? (int)$value : null;
    };
    
    // Update user table
    $userUpdates = [];
    $userParams = [];
    
    if (isset($data['name'])) {
        $userUpdates[] = "name = ?";
        $userParams[] = trim($data['name']);
    }
    
    if (isset($data['status']) && in_array($data['status'], ['active', 'inactive', 'blocked'])) {
        $userUpdates[] = "status = ?";
        $userParams[] = $data['status'];
    }
    
    if (isset($data['email_verified'])) {
        $userUpdates[] = "email_verified = ?";
        $userParams[] = $data['email_verified'] ? 1 : 0;
    }
    
    if (!empty($userUpdates)) {
        $userUpdates[] = "updated_at = NOW()";
        $userParams[] = $userId;
        $stmt = $db->prepare("UPDATE users SET " . implode(', ', $userUpdates) . " WHERE id = ?");
        $stmt->execute($userParams);
    }

    // Map legacy request keys to canonical alumni_profiles columns.
    $profileFieldMap = [
        'college_id' => 'college_id',
        'campus_id' => 'campus_id',
        'program_id' => 'program_id',
        'section_id' => 'section_id',
        'batch_year' => 'batch_year',
        'graduation_year' => 'graduation_year',
        'phone' => 'phone',
        'address' => 'address_street',
        'city' => 'address_city',
        'state' => 'address_province',
        'country' => 'address_country',
        'postal_code' => 'address_zip',
        'company' => 'current_employer',
        'job_title' => 'job_title',
        'industry' => 'industry',
        'employment_status' => 'employment_status',

        // Canonical keys (newer clients can write directly).
        'address_street' => 'address_street',
        'address_barangay' => 'address_barangay',
        'address_city' => 'address_city',
        'address_province' => 'address_province',
        'address_region' => 'address_region',
        'address_zip' => 'address_zip',
        'address_country' => 'address_country',
        'current_employer' => 'current_employer',
        'company_address' => 'company_address',
    ];

    $intColumns = ['campus_id', 'college_id', 'program_id', 'section_id', 'batch_year', 'graduation_year'];
    $profileData = [];

    foreach ($profileFieldMap as $requestKey => $columnName) {
        if (!array_key_exists($requestKey, $data)) {
            continue;
        }

        if (in_array($columnName, $intColumns, true)) {
            $profileData[$columnName] = $toNullableInt($data[$requestKey]);
            continue;
        }

        $profileData[$columnName] = $nullIfEmpty($data[$requestKey]);
    }

    $requestedAdjustPoints = isset($data['adjust_points']) && is_numeric($data['adjust_points'])
        ? (int)$data['adjust_points']
        : 0;

    $needsProfileRow = !empty($profileData) || $requestedAdjustPoints !== 0;

    if ($needsProfileRow) {
        $stmt = $db->prepare(
            "INSERT INTO alumni_profiles (user_id, total_points, badge_level, created_at, updated_at)
             VALUES (?, 0, 'bronze', NOW(), NOW())
             ON DUPLICATE KEY UPDATE updated_at = NOW()"
        );
        $stmt->execute([$userId]);
    }

    if (!empty($profileData)) {
        $profileUpdates = [];
        $profileParams = [];

        foreach ($profileData as $columnName => $value) {
            $profileUpdates[] = "$columnName = ?";
            $profileParams[] = $value;
        }

        $profileUpdates[] = "updated_at = NOW()";
        $profileParams[] = $userId;

        $stmt = $db->prepare("UPDATE alumni_profiles SET " . implode(', ', $profileUpdates) . " WHERE user_id = ?");
        $stmt->execute($profileParams);
    }

    $stmt = $db->prepare(
        "SELECT u.alumni_id, COALESCE(ap.campus_id, u.campus_id) as campus_id, ap.college_id, ap.graduation_year
         FROM users u
         LEFT JOIN alumni_profiles ap ON u.id = ap.user_id
         WHERE u.id = ?
         LIMIT 1"
    );
    $stmt->execute([$userId]);
    $idProfile = $stmt->fetch();

    if (
        $idProfile &&
        !empty($idProfile['campus_id']) &&
        !empty($idProfile['college_id']) &&
        !empty($idProfile['graduation_year'])
    ) {
        $alumniIdPrefix = getAlumniIdProfilePrefix(
            $db,
            (int)$idProfile['campus_id'],
            (int)$idProfile['college_id'],
            (int)$idProfile['graduation_year']
        );

        if ($alumniIdPrefix) {
            $currentAlumniId = strtoupper((string)($idProfile['alumni_id'] ?? ''));
            $sequenceLength = 5;
            $expectedPattern = '/^' . preg_quote(strtoupper($alumniIdPrefix), '/') . '-\d{' . $sequenceLength . '}$/';

            if (!preg_match($expectedPattern, $currentAlumniId)) {
                $newAlumniId = generateAlumniId(
                    $db,
                    (int)$idProfile['campus_id'],
                    (int)$idProfile['college_id'],
                    (int)$idProfile['graduation_year']
                );

                if ($newAlumniId) {
                    $stmt = $db->prepare("UPDATE users SET alumni_id = ?, updated_at = NOW() WHERE id = ?");
                    $stmt->execute([$newAlumniId, $userId]);
                }
            }
        }
    }

    $pointsDelta = 0;
    $pointsAfter = null;

    if ($requestedAdjustPoints !== 0) {
        $stmt = $db->prepare('SELECT COALESCE(total_points, 0) as total_points FROM alumni_profiles WHERE user_id = ? FOR UPDATE');
        $stmt->execute([$userId]);
        $beforePoints = (int)($stmt->fetch()['total_points'] ?? 0);

        $stmt = $db->prepare('UPDATE alumni_profiles SET total_points = GREATEST(total_points + ?, 0), updated_at = NOW() WHERE user_id = ?');
        $stmt->execute([$requestedAdjustPoints, $userId]);

        $stmt = $db->prepare('SELECT COALESCE(total_points, 0) as total_points FROM alumni_profiles WHERE user_id = ?');
        $stmt->execute([$userId]);
        $pointsAfter = (int)($stmt->fetch()['total_points'] ?? 0);
        $pointsDelta = $pointsAfter - $beforePoints;

        if ($pointsDelta !== 0) {
            $admin = getCurrentUser();
            $txnType = $pointsDelta > 0 ? 'bonus' : 'penalty';
            $txnPoints = abs($pointsDelta);
            $reason = trim((string)($data['points_reason'] ?? 'Admin adjustment'));

            $stmt = $db->prepare(
                "INSERT INTO point_transactions (user_id, points, type, source, description, balance_after, created_at)
                 VALUES (?, ?, ?, 'admin_bonus', ?, ?, NOW())"
            );
            $stmt->execute([
                $userId,
                $txnPoints,
                $txnType,
                $reason . ' (by ' . ($admin['name'] ?? 'Admin') . ')',
                $pointsAfter,
            ]);
        }
    }
    
    $db->commit();
    
    $admin = getCurrentUser();
    logAdminActivity((int)$admin['id'], 'update', 'Updated alumni #' . $userId, 'alumni', (int)$userId);
    
    echo json_encode([
        'success' => true,
        'message' => 'Alumni updated successfully',
        'data' => [
            'points_delta' => $pointsDelta,
            'total_points' => $pointsAfter,
        ],
    ]);
    
} catch (PDOException $e) {
    if (isset($db) && $db->inTransaction()) $db->rollBack();
    error_log("Admin Update Alumni Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
