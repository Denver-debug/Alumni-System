<?php
/**
 * Events API - Scan QR Code for Check-in
 * Validates alumni QR code and marks attendance
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../utils/helpers.php';
require_once __DIR__ . '/../../middleware/auth.php';

if (!function_exists('eventScanFirstValue')) {
    function eventScanFirstValue(array $source, array $keys) {
        foreach ($keys as $key) {
            if (isset($source[$key]) && $source[$key] !== '') {
                return $source[$key];
            }
        }

        return null;
    }
}

if (!function_exists('eventScanNormalizeAlumniPayload')) {
    function eventScanNormalizeAlumniPayload($qrData): array {
        $rawString = is_array($qrData) ? '' : trim((string)$qrData);
        $payload = is_array($qrData) ? $qrData : json_decode($rawString, true);

        if (!is_array($payload)) {
            $payload = [];
            $parts = parse_url($rawString);
            if (is_array($parts) && isset($parts['query'])) {
                parse_str($parts['query'], $query);
                if (is_array($query)) {
                    $payload = $query;
                }
            } elseif (str_starts_with($rawString, '?')) {
                parse_str(ltrim($rawString, '?'), $query);
                if (is_array($query)) {
                    $payload = $query;
                }
            }
        }

        if (empty($payload) && $rawString !== '') {
            $payload = ['alumni_id' => $rawString];
        }

        if (isset($payload['data']) && is_array($payload['data'])) {
            $payload = array_merge($payload['data'], $payload);
        }

        return [
            'type' => strtolower(trim((string)($payload['type'] ?? ''))),
            'user_id' => eventScanFirstValue($payload, ['user_id', 'userId', 'id']),
            'alumni_id' => eventScanFirstValue($payload, ['alumni_id', 'alumniId', 'alumni_number']),
            'student_id' => eventScanFirstValue($payload, ['student_id', 'studentId']),
            'email' => eventScanFirstValue($payload, ['email']),
        ];
    }
}

// Require authentication (admin, campus admin, staff, or system admin)
$admin = requireAdmin();

header('Content-Type: application/json');

try {
    $db = Database::getInstance()->getConnection();
    syncEventStatuses($db);
    $body = getRequestBody();
    
    // Validate required fields
    $errors = validateRequired($body, ['event_id', 'qr_data']);
    if (!empty($errors)) {
        validationError($errors);
    }
    
    $eventId = (int) $body['event_id'];
    if ($eventId <= 0) {
        respondError('Please select a valid event before scanning', 400);
    }

    $qrInfo = eventScanNormalizeAlumniPayload($body['qr_data']);

    if ($qrInfo['type'] !== '' && $qrInfo['type'] !== 'alumni_id') {
        respondError('This is not an alumni ID QR code', 400);
    }

    if (!$qrInfo['user_id'] && !$qrInfo['alumni_id'] && !$qrInfo['student_id'] && !$qrInfo['email']) {
        respondError('QR code does not contain valid alumni information', 400);
    }
    
    // Get user ID from QR data
    $userId = $qrInfo['user_id'] ? (int) $qrInfo['user_id'] : null;
    
    // If we only have a visible identifier, look up user_id.
    if (!$userId) {
        $identifier = trim((string)($qrInfo['alumni_id'] ?? $qrInfo['student_id'] ?? $qrInfo['email']));
        $stmt = $db->prepare(
            "SELECT u.id
             FROM users u
             LEFT JOIN alumni_profiles ap ON u.id = ap.user_id
             WHERE u.role = 'alumni'
               AND (
                    UPPER(u.alumni_id) = UPPER(:identifier)
                    OR UPPER(ap.student_id) = UPPER(:identifier)
                    OR LOWER(u.email) = LOWER(:identifier)
               )
             LIMIT 1"
        );
        $stmt->execute(['identifier' => $identifier]);
        $user = $stmt->fetch();
        
        if (!$user) {
            respondError('Alumni not found', 404);
        }
        
        $userId = (int) $user['id'];
    }
    
    // Verify event exists
    $stmt = $db->prepare("SELECT id, title, event_date, status, points_reward FROM events WHERE id = :event_id");
    $stmt->execute(['event_id' => $eventId]);
    $event = $stmt->fetch();
    
    if (!$event) {
        respondError('Event not found', 404);
    }
    
    if ($event['status'] === 'cancelled') {
        respondError('This event has been cancelled', 400);
    }

    if (!in_array($event['status'], ['upcoming', 'ongoing'], true)) {
        respondError('Check-in is not available for this event', 400);
    }
    
    // Verify alumni exists and is active
    $stmt = $db->prepare("SELECT id, name, alumni_id, status FROM users WHERE id = :user_id AND role = 'alumni'");
    $stmt->execute(['user_id' => $userId]);
    $alumniUser = $stmt->fetch();
    
    if (!$alumniUser) {
        respondError('Alumni not found', 404);
    }
    
    if ($alumniUser['status'] !== 'active') {
        respondError('Alumni account is not active', 400);
    }
    
    // Check if already checked in
    $stmt = $db->prepare("
        SELECT id, check_in_time 
        FROM event_attendances 
        WHERE event_id = :event_id AND user_id = :user_id
    ");
    $stmt->execute([
        'event_id' => $eventId,
        'user_id' => $userId
    ]);
    $existingAttendance = $stmt->fetch();
    
    if ($existingAttendance) {
        respondError('Alumni already checked in at ' . date('g:i A', strtotime($existingAttendance['check_in_time'])), 400);
    }
    
    // Get event points reward
    $pointsAwarded = max(0, (int)($event['points_reward'] ?? 0));
    
    // Begin transaction
    $db->beginTransaction();
    
    try {
        // Mark attendance
        $stmt = $db->prepare("
            INSERT INTO event_rsvps (event_id, user_id, status, created_at, updated_at)
            VALUES (:event_id, :user_id, 'going', NOW(), NOW())
            ON DUPLICATE KEY UPDATE status = 'going', updated_at = NOW()
        ");
        $stmt->execute([
            'event_id' => $eventId,
            'user_id' => $userId
        ]);

        $stmt = $db->prepare("
            INSERT INTO event_attendances 
            (event_id, user_id, check_in_method, check_in_time, points_awarded, verified_by, created_at)
            VALUES (:event_id, :user_id, 'qr_code', NOW(), :points_awarded, :verified_by, NOW())
        ");
        $stmt->execute([
            'event_id' => $eventId,
            'user_id' => $userId,
            'points_awarded' => $pointsAwarded,
            'verified_by' => $admin['id']
        ]);

        $stmt = $db->prepare("
            INSERT INTO alumni_profiles (user_id, total_points, badge_level, created_at, updated_at)
            VALUES (:user_id, 0, 'bronze', NOW(), NOW())
            ON DUPLICATE KEY UPDATE updated_at = NOW()
        ");
        $stmt->execute(['user_id' => $userId]);
        
        // Award points if applicable
        if ($pointsAwarded > 0) {
            // Update total points
            $stmt = $db->prepare("
                UPDATE alumni_profiles 
                SET total_points = total_points + :points,
                    updated_at = NOW()
                WHERE user_id = :user_id
            ");
            $stmt->execute([
                'points' => $pointsAwarded,
                'user_id' => $userId
            ]);
            
            // Get new balance
            $stmt = $db->prepare("SELECT total_points FROM alumni_profiles WHERE user_id = :user_id");
            $stmt->execute(['user_id' => $userId]);
            $profile = $stmt->fetch();
            $newBalance = $profile ? (int) $profile['total_points'] : $pointsAwarded;

            $stmt = $db->prepare("UPDATE alumni_profiles SET badge_level = :badge_level, updated_at = NOW() WHERE user_id = :user_id");
            $stmt->execute([
                'badge_level' => BadgeLevel::getForPoints($newBalance),
                'user_id' => $userId
            ]);
            
            // Record transaction
            $stmt = $db->prepare("
                INSERT INTO point_transactions 
                (user_id, points, type, source, reference_id, reference_type, description, balance_after)
                VALUES (:user_id, :points, 'earned', 'event_attendance', :event_id, 'event', :description, :balance)
            ");
            $stmt->execute([
                'user_id' => $userId,
                'points' => $pointsAwarded,
                'event_id' => $eventId,
                'description' => 'Attended: ' . $event['title'],
                'balance' => $newBalance
            ]);
        }
        
        $db->commit();
        
        // Get alumni info for response
        $stmt = $db->prepare("
            SELECT u.name, u.alumni_id, u.profile_image,
                   ap.total_points, ap.badge_level
            FROM users u
            LEFT JOIN alumni_profiles ap ON u.id = ap.user_id
            WHERE u.id = :user_id
        ");
        $stmt->execute(['user_id' => $userId]);
        $alumni = $stmt->fetch();
        
        // Process profile image URL
        $alumniData = processUserData([
            'name' => $alumni['name'],
            'alumni_id' => $alumni['alumni_id'],
            'profile_image' => $alumni['profile_image'],
            'total_points' => (int) $alumni['total_points'],
            'badge_level' => $alumni['badge_level']
        ]);
        
        respondSuccess([
            'message' => 'Check-in successful!',
            'alumni' => $alumniData,
            'event' => [
                'id' => $event['id'],
                'title' => $event['title'],
                'event_date' => $event['event_date']
            ],
            'points_awarded' => $pointsAwarded,
            'check_in_time' => date('Y-m-d H:i:s')
        ]);
        
    } catch (Exception $e) {
        $db->rollback();
        throw $e;
    }
    
} catch (Exception $e) {
    error_log("QR Scan Error: " . $e->getMessage());
    respondError('Failed to process check-in: ' . $e->getMessage(), 500);
}
