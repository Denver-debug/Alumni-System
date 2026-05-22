<?php
/**
 * Admin Mark Attendance API
 * POST /api/admin/events/{id}/attendance
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../config/auth.php';
require_once __DIR__ . '/../../../utils/helpers.php';
require_once __DIR__ . '/../../../middleware/auth.php';

$admin = requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respondError('Method not allowed', 405);
}

$eventId = $GLOBALS['url_params']['id'] ?? null;
if (!$eventId) {
    respondError('Event ID required', 400);
}

$data = getRequestBody();
$userId = (int)($data['user_id'] ?? 0);
$status = $data['status'] ?? 'attended';

if ($userId <= 0) {
    respondError('User ID required', 400);
}

if (!in_array($status, ['attended', 'absent'], true)) {
    respondError('Invalid attendance status', 400);
}

try {
    $db = Database::getInstance()->getConnection();

    $stmt = $db->prepare("SELECT id, title, points_reward FROM events WHERE id = ?");
    $stmt->execute([$eventId]);
    $event = $stmt->fetch();

    if (!$event) {
        respondError('Event not found', 404);
    }

    $points = max(0, (int)($event['points_reward'] ?? 0));

    $db->beginTransaction();

    // Keep RSVP state aligned with attendance action.
    $rsvpStatus = $status === 'attended' ? 'going' : 'not_going';
    $stmt = $db->prepare("\n        INSERT INTO event_rsvps (event_id, user_id, status, created_at, updated_at)\n        VALUES (:event_id, :user_id, :status, NOW(), NOW())\n        ON DUPLICATE KEY UPDATE status = VALUES(status), updated_at = NOW()\n    ");
    $stmt->execute([
        'event_id' => $eventId,
        'user_id' => $userId,
        'status' => $rsvpStatus,
    ]);

    $stmt = $db->prepare("SELECT id, points_awarded FROM event_attendances WHERE event_id = :event_id AND user_id = :user_id LIMIT 1");
    $stmt->execute([
        'event_id' => $eventId,
        'user_id' => $userId,
    ]);
    $attendance = $stmt->fetch();

    $stmt = $db->prepare("\n        INSERT INTO alumni_profiles (user_id, total_points, badge_level, created_at, updated_at)\n        VALUES (:user_id, 0, 'bronze', NOW(), NOW())\n        ON DUPLICATE KEY UPDATE updated_at = NOW()\n    ");
    $stmt->execute(['user_id' => $userId]);

    if ($status === 'attended' && !$attendance) {
        $stmt = $db->prepare("\n            INSERT INTO event_attendances (event_id, user_id, check_in_method, check_in_time, points_awarded, verified_by, created_at)\n            VALUES (:event_id, :user_id, 'manual', NOW(), :points, :verified_by, NOW())\n        ");
        $stmt->execute([
            'event_id' => $eventId,
            'user_id' => $userId,
            'points' => $points,
            'verified_by' => $admin['id'],
        ]);

        if ($points > 0) {
            $stmt = $db->prepare("UPDATE alumni_profiles SET total_points = total_points + :points, updated_at = NOW() WHERE user_id = :user_id");
            $stmt->execute([
                'points' => $points,
                'user_id' => $userId,
            ]);
        }
    }

    if ($status === 'absent' && $attendance) {
        $awarded = max(0, (int)($attendance['points_awarded'] ?? 0));

        $stmt = $db->prepare("DELETE FROM event_attendances WHERE id = :id");
        $stmt->execute(['id' => $attendance['id']]);

        if ($awarded > 0) {
            $stmt = $db->prepare("UPDATE alumni_profiles SET total_points = GREATEST(total_points - :points, 0), updated_at = NOW() WHERE user_id = :user_id");
            $stmt->execute([
                'points' => $awarded,
                'user_id' => $userId,
            ]);
        }
    }

    $stmt = $db->prepare("SELECT COALESCE(total_points, 0) FROM alumni_profiles WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $userId]);
    $balanceAfter = (int)$stmt->fetchColumn();

    if ($status === 'attended' && $points > 0 && !$attendance) {
        $stmt = $db->prepare("\n            INSERT INTO point_transactions (\n                user_id, points, type, source, reference_id, reference_type, description, balance_after, created_at\n            ) VALUES (\n                :user_id, :points, 'earned', 'event_attendance', :reference_id, 'event', :description, :balance_after, NOW()\n            )\n        ");
        $stmt->execute([
            'user_id' => $userId,
            'points' => $points,
            'reference_id' => $eventId,
            'description' => 'Event attendance verified by admin',
            'balance_after' => $balanceAfter,
        ]);
    }

    if ($status === 'absent' && $attendance && (int)($attendance['points_awarded'] ?? 0) > 0) {
        $deducted = (int)$attendance['points_awarded'];
        $stmt = $db->prepare("\n            INSERT INTO point_transactions (\n                user_id, points, type, source, reference_id, reference_type, description, balance_after, created_at\n            ) VALUES (\n                :user_id, :points, 'penalty', 'event_attendance', :reference_id, 'event', :description, :balance_after, NOW()\n            )\n        ");
        $stmt->execute([
            'user_id' => $userId,
            'points' => $deducted,
            'reference_id' => $eventId,
            'description' => 'Event attendance reversed by admin',
            'balance_after' => $balanceAfter,
        ]);
    }

    logAdminActivity((int)$admin['id'], 'attendance_update', 'Attendance updated for event #' . $eventId, 'event', (int)$eventId);

    $db->commit();

    respondSuccess(['message' => 'Attendance updated']);
} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    respondError('Database error: ' . $e->getMessage(), 500);
}
