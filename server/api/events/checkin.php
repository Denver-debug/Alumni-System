<?php
/**
 * Events API - Check In
 * POST /api/events/{id}/checkin
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../utils/helpers.php';
require_once __DIR__ . '/../../middleware/auth.php';

if (!function_exists('eventCheckInFirstValue')) {
    function eventCheckInFirstValue(array $source, array $keys) {
        foreach ($keys as $key) {
            if (isset($source[$key]) && $source[$key] !== '') {
                return $source[$key];
            }
        }

        return null;
    }
}

if (!function_exists('eventCheckInNormalizePayload')) {
    function eventCheckInNormalizePayload(array $data): array {
        $eventId = eventCheckInFirstValue($data, ['event_id', 'eventId']);
        $code = eventCheckInFirstValue($data, ['code', 'attendance_code', 'attendanceCode', 'event_code', 'eventCode']);
        $raw = eventCheckInFirstValue($data, ['qr_data', 'qrData', 'qr', 'scan_data', 'scanData']);

        if ($raw === null) {
            $raw = $code;
        }

        if (is_array($raw)) {
            $payload = $raw;
        } else {
            $rawString = trim((string)($raw ?? ''));
            $payload = json_decode($rawString, true);
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

            if ($code === null && empty($payload)) {
                $code = $rawString;
            }
        }

        if (!empty($payload)) {
            $eventId = $eventId ?? eventCheckInFirstValue($payload, ['event_id', 'eventId']);
            $code = $code ?? eventCheckInFirstValue($payload, ['code', 'attendance_code', 'attendanceCode', 'event_code', 'eventCode']);

            if ($code === null && isset($payload['data']) && is_array($payload['data'])) {
                $eventId = $eventId ?? eventCheckInFirstValue($payload['data'], ['event_id', 'eventId']);
                $code = eventCheckInFirstValue($payload['data'], ['code', 'attendance_code', 'attendanceCode', 'event_code', 'eventCode']);
            }
        }

        return [
            'event_id' => $eventId !== null && $eventId !== '' ? (int)$eventId : null,
            'code' => strtoupper(trim((string)($code ?? ''))),
        ];
    }
}

$user = requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respondError('Method not allowed', 405);
}

try {
    $db = Database::getInstance()->getConnection();
    syncEventStatuses($db);
    $data = getRequestBody();

    $normalized = eventCheckInNormalizePayload($data);
    $eventId = $GLOBALS['url_params']['id'] ?? ($_GET['id'] ?? $normalized['event_id']);
    $code = $normalized['code'];

    if ($code === '' || strlen($code) < 4 || strlen($code) > 20) {
        respondError('Valid attendance code required', 400);
    }

    if ($eventId) {
        $stmt = $db->prepare('SELECT id, title, event_date, event_time, status, attendance_code, points_reward FROM events WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $eventId]);
    } else {
        $stmt = $db->prepare(
            "SELECT id, title, event_date, event_time, status, attendance_code, points_reward
             FROM events
             WHERE attendance_code IS NOT NULL
               AND UPPER(attendance_code) = :code
             ORDER BY FIELD(status, 'ongoing', 'upcoming', 'draft', 'completed', 'cancelled'), event_date ASC, id DESC
             LIMIT 1"
        );
        $stmt->execute(['code' => $code]);
    }
    $event = $stmt->fetch();

    if (!$event) {
        respondError('Event not found', 404);
    }

    $eventId = (int)$event['id'];

    if (!in_array($event['status'], ['upcoming', 'ongoing'], true)) {
        respondError('Check-in is not available for this event', 400);
    }

    $eventCode = strtoupper(trim((string)($event['attendance_code'] ?? '')));
    if ($eventCode === '' || $eventCode !== $code) {
        respondError('Invalid attendance code', 400);
    }

    $stmt = $db->prepare('SELECT id, status FROM event_rsvps WHERE event_id = :event_id AND user_id = :user_id LIMIT 1');
    $stmt->execute([
        'event_id' => $eventId,
        'user_id' => $user['id'],
    ]);
    $rsvp = $stmt->fetch();

    if (!$rsvp || $rsvp['status'] === 'not_going') {
        respondError('You are not registered for this event', 400);
    }

    $stmt = $db->prepare('SELECT id FROM event_attendances WHERE event_id = :event_id AND user_id = :user_id LIMIT 1');
    $stmt->execute([
        'event_id' => $eventId,
        'user_id' => $user['id'],
    ]);
    if ($stmt->fetch()) {
        respondError('You have already checked in', 400);
    }

    $points = max(0, (int)($event['points_reward'] ?? 0));

    $db->beginTransaction();

    $stmt = $db->prepare(
        'INSERT INTO event_attendances (event_id, user_id, check_in_method, check_in_time, points_awarded, created_at)
         VALUES (:event_id, :user_id, \'attendance_code\', NOW(), :points, NOW())'
    );
    $stmt->execute([
        'event_id' => $eventId,
        'user_id' => $user['id'],
        'points' => $points,
    ]);

    $stmt = $db->prepare(
        "INSERT INTO alumni_profiles (user_id, total_points, badge_level, created_at, updated_at)
         VALUES (:user_id, 0, 'bronze', NOW(), NOW())
         ON DUPLICATE KEY UPDATE updated_at = NOW()"
    );
    $stmt->execute(['user_id' => $user['id']]);

    $balanceAfter = null;
    if ($points > 0) {
        $stmt = $db->prepare('UPDATE alumni_profiles SET total_points = total_points + :points, updated_at = NOW() WHERE user_id = :user_id');
        $stmt->execute([
            'points' => $points,
            'user_id' => $user['id'],
        ]);

        $stmt = $db->prepare('SELECT COALESCE(total_points, 0) FROM alumni_profiles WHERE user_id = :user_id');
        $stmt->execute(['user_id' => $user['id']]);
        $balanceAfter = (int)$stmt->fetchColumn();

        $badgeLevel = BadgeLevel::getForPoints($balanceAfter);
        $stmt = $db->prepare('UPDATE alumni_profiles SET badge_level = :badge_level, updated_at = NOW() WHERE user_id = :user_id');
        $stmt->execute([
            'badge_level' => $badgeLevel,
            'user_id' => $user['id'],
        ]);

        $stmt = $db->prepare(
            "INSERT INTO point_transactions (
                user_id, points, type, source, reference_id, reference_type, description, balance_after, created_at
            ) VALUES (
                :user_id, :points, 'earned', 'event_attendance', :reference_id, 'event', :description, :balance_after, NOW()
            )"
        );
        $stmt->execute([
            'user_id' => $user['id'],
            'points' => $points,
            'reference_id' => $eventId,
            'description' => 'Attended: ' . $event['title'],
            'balance_after' => $balanceAfter,
        ]);
    }

    $db->commit();

    respondSuccess([
        'event_id' => $eventId,
        'event' => [
            'id' => $eventId,
            'title' => $event['title'],
            'event_date' => $event['event_date'] ?? null,
            'event_time' => $event['event_time'] ?? null,
        ],
        'event_title' => $event['title'],
        'points_awarded' => $points,
        'points_earned' => $points,
        'total_points' => $balanceAfter,
        'message' => 'Check-in successful',
    ]);
} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    respondError('Check-in failed: ' . $e->getMessage(), 500);
}
