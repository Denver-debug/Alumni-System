<?php
/**
 * Alumni Verification API
 * Admin approval + alumni status endpoints
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../utils/helpers.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uri = $_SERVER['REQUEST_URI'] ?? '';

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();

    // ============================================
    // ADMIN ENDPOINTS
    // ============================================

    // GET /api/v1/admin/alumni/pending
    if ($method === 'GET' && preg_match('/\/admin\/alumni\/pending$/', $uri)) {
        requireTopAdmin();

        $stmt = $conn->prepare("
            SELECT
                u.id,
                u.alumni_id,
                u.name,
                u.email,
                u.profile_image,
                u.created_at,
                u.verification_status,
                u.verified_at,
                u.rejection_reason,
                ap.batch_year,
                ap.graduation_year,
                ap.student_id,
                c.name AS college_name,
                p.name AS program_name,
                s.name AS section_name
            FROM users u
            LEFT JOIN alumni_profiles ap ON u.id = ap.user_id
            LEFT JOIN colleges c ON ap.college_id = c.id
            LEFT JOIN programs p ON ap.program_id = p.id
            LEFT JOIN sections s ON ap.section_id = s.id
            WHERE u.role = 'alumni'
              AND u.verification_status = 'pending'
            ORDER BY u.created_at ASC
        ");
        $stmt->execute();

        respondSuccess($stmt->fetchAll());
    }

    // GET /api/v1/admin/alumni/verification-stats
    if ($method === 'GET' && preg_match('/\/admin\/alumni\/verification-stats$/', $uri)) {
        requireTopAdmin();

        $stmt = $conn->query("
            SELECT
                COUNT(*) AS total,
                SUM(CASE WHEN verification_status = 'pending' THEN 1 ELSE 0 END) AS pending,
                SUM(CASE WHEN verification_status = 'verified' THEN 1 ELSE 0 END) AS verified,
                SUM(CASE WHEN verification_status = 'rejected' THEN 1 ELSE 0 END) AS rejected,
                SUM(CASE WHEN verification_status = 'verified' AND DATE(verified_at) = CURDATE() THEN 1 ELSE 0 END) AS verified_today
            FROM users
            WHERE role = 'alumni'
        ");
        $stats = $stmt->fetch() ?: [];

        respondSuccess($stats);
    }

    // PUT /api/v1/admin/alumni/:id/verify
    if ($method === 'PUT' && preg_match('/\/admin\/alumni\/(\d+)\/verify$/', $uri, $matches)) {
        $admin = requireTopAdmin();
        $userId = (int) $matches[1];
        $payload = getRequestBody();
        $notes = isset($payload['notes']) ? trim((string) $payload['notes']) : null;

        $db->beginTransaction();

        try {
            $stmt = $conn->prepare("
                UPDATE users
                SET verification_status = 'verified',
                    verified_by = ?,
                    verified_at = NOW(),
                    rejection_reason = NULL,
                    verification_notes = ?,
                    status = 'active'
                WHERE id = ? AND role = 'alumni'
            ");
            $stmt->execute([$admin['id'], $notes, $userId]);

            if ($stmt->rowCount() === 0) {
                throw new Exception('Alumni not found or already verified');
            }

            $message = 'Congratulations! Your account has been verified. You now have full access to the alumni system.';
            $db->insert('verification_notifications', [
                'user_id' => $userId,
                'status' => 'verified',
                'message' => $message,
            ]);

            $db->commit();

            respondSuccess([
                'user_id' => $userId,
                'status' => 'verified',
            ], 200, 'Alumni verified successfully');
        } catch (Throwable $e) {
            $db->rollback();
            throw $e;
        }
    }

    // PUT /api/v1/admin/alumni/:id/reject
    if ($method === 'PUT' && preg_match('/\/admin\/alumni\/(\d+)\/reject$/', $uri, $matches)) {
        $admin = requireTopAdmin();
        $userId = (int) $matches[1];
        $payload = getRequestBody();
        $reason = trim((string) ($payload['reason'] ?? 'Your registration did not meet our verification criteria.'));

        $db->beginTransaction();

        try {
            $stmt = $conn->prepare("
                UPDATE users
                SET verification_status = 'rejected',
                    verified_by = ?,
                    verified_at = NOW(),
                    rejection_reason = ?,
                    verification_notes = NULL,
                    status = 'inactive'
                WHERE id = ? AND role = 'alumni'
            ");
            $stmt->execute([$admin['id'], $reason, $userId]);

            if ($stmt->rowCount() === 0) {
                throw new Exception('Alumni not found');
            }

            $db->insert('verification_notifications', [
                'user_id' => $userId,
                'status' => 'rejected',
                'message' => $reason,
            ]);

            $db->commit();

            respondSuccess([
                'user_id' => $userId,
                'status' => 'rejected',
            ], 200, 'Alumni registration rejected');
        } catch (Throwable $e) {
            $db->rollback();
            throw $e;
        }
    }

    // ============================================
    // ALUMNI ENDPOINTS
    // ============================================

    // GET /api/v1/alumni/verification-status
    if ($method === 'GET' && preg_match('/\/alumni\/verification-status$/', $uri)) {
        $user = requireAuth();

        $stmt = $conn->prepare("
            SELECT verification_status, rejection_reason, verified_at, created_at
            FROM users
            WHERE id = ? AND role = 'alumni'
        ");
        $stmt->execute([$user['id']]);
        $data = $stmt->fetch();

        if (!$data) {
            notFound('Alumni not found');
        }

        respondSuccess($data);
    }

    // GET /api/v1/alumni/notifications
    if ($method === 'GET' && preg_match('/\/alumni\/notifications$/', $uri)) {
        $user = requireAuth();

        $stmt = $conn->prepare("
            SELECT id, status, message, sent_at, read_at
            FROM verification_notifications
            WHERE user_id = ?
            ORDER BY sent_at DESC
            LIMIT 10
        ");
        $stmt->execute([$user['id']]);

        respondSuccess($stmt->fetchAll());
    }

    // PUT /api/v1/alumni/notifications/:id/read
    if ($method === 'PUT' && preg_match('/\/alumni\/notifications\/(\d+)\/read$/', $uri, $matches)) {
        $user = requireAuth();
        $notificationId = (int) $matches[1];

        $stmt = $conn->prepare("
            UPDATE verification_notifications
            SET read_at = NOW()
            WHERE id = ? AND user_id = ? AND read_at IS NULL
        ");
        $stmt->execute([$notificationId, $user['id']]);

        respondSuccess([
            'id' => $notificationId,
            'read' => true,
        ], 200, 'Notification marked as read');
    }

    respondError('Endpoint not found', 404);
} catch (Throwable $e) {
    $status = (int) $e->getCode();
    if ($status < 400 || $status >= 600) {
        $status = 500;
    }
    respondError($e->getMessage(), $status);
}
