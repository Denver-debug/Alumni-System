<?php
/**
 * Security Settings API
 * Manages login security configuration and account lockouts
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../utils/helpers.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uri = $_SERVER['REQUEST_URI'] ?? '';

try {
    $admin = requireAdmin();

    $db = Database::getInstance();
    $conn = $db->getConnection();

    // GET /api/v1/admin/security/settings
    if ($method === 'GET' && preg_match('/\/admin\/security\/settings$/', $uri)) {
        $stmt = $conn->prepare("
            SELECT setting_key, setting_value, setting_type, description
            FROM system_settings
            WHERE setting_key IN (
                'max_login_attempts',
                'lockout_duration_minutes',
                'enable_login_lockout',
                'session_timeout_minutes',
                'require_email_verification'
            )
        ");
        $stmt->execute();

        $settings = [];
        foreach ($stmt->fetchAll() as $row) {
            $value = $row['setting_value'];
            if ($row['setting_type'] === 'number') {
                $value = (int) $value;
            } elseif ($row['setting_type'] === 'boolean') {
                $value = $value === 'true' || $value === '1';
            }

            $settings[$row['setting_key']] = [
                'value' => $value,
                'type' => $row['setting_type'],
                'description' => $row['description'],
            ];
        }

        respondSuccess($settings);
    }

    // PUT /api/v1/admin/security/settings
    if ($method === 'PUT' && preg_match('/\/admin\/security\/settings$/', $uri)) {
        $data = getRequestBody();
        if (!$data) {
            respondError('Invalid request data', 400);
        }

        $allowedKeys = [
            'max_login_attempts',
            'lockout_duration_minutes',
            'enable_login_lockout',
            'session_timeout_minutes',
            'require_email_verification'
        ];

        $db->beginTransaction();
        try {
            foreach ($data as $key => $value) {
                if (!in_array($key, $allowedKeys, true)) {
                    continue;
                }

                if ($key === 'max_login_attempts') {
                    $value = max(3, min(10, (int) $value));
                } elseif ($key === 'lockout_duration_minutes') {
                    $value = max(5, min(1440, (int) $value));
                } elseif ($key === 'session_timeout_minutes') {
                    $value = max(15, min(1440, (int) $value));
                } elseif (in_array($key, ['enable_login_lockout', 'require_email_verification'], true)) {
                    $value = $value ? 'true' : 'false';
                }

                $db->update(
                    'system_settings',
                    ['setting_value' => $value, 'updated_by' => $admin['id']],
                    'setting_key = ?',
                    [$key]
                );
            }

            $db->commit();
            respondSuccess([], 200, 'Security settings updated successfully');
        } catch (Throwable $e) {
            $db->rollback();
            throw $e;
        }
    }

    // GET /api/v1/admin/security/locked-accounts
    if ($method === 'GET' && preg_match('/\/admin\/security\/locked-accounts$/', $uri)) {
        $stmt = $conn->prepare("
            SELECT id AS user_id, name, email, locked_until, login_attempts, status
            FROM users
            WHERE locked_until IS NOT NULL AND locked_until > NOW()
            ORDER BY locked_until DESC
        ");
        $stmt->execute();

        respondSuccess($stmt->fetchAll());
    }

    // PUT /api/v1/admin/security/unlock/:user_id
    if ($method === 'PUT' && preg_match('/\/admin\/security\/unlock\/(\d+)$/', $uri, $matches)) {
        $userId = (int) $matches[1];

        $stmt = $conn->prepare("
            UPDATE users
            SET locked_until = NULL, login_attempts = 0
            WHERE id = ?
        ");
        $stmt->execute([$userId]);

        if ($stmt->rowCount() === 0) {
            respondError('No active lockout found for this account', 404);
        }

        respondSuccess([], 200, 'Account unlocked successfully');
    }

    // GET /api/v1/admin/security/login-attempts
    if ($method === 'GET' && preg_match('/\/admin\/security\/login-attempts$/', $uri)) {
        $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 100;
        $email = isset($_GET['email']) ? $_GET['email'] : null;

        $sql = "
            SELECT email, ip_address, attempt_time, success, failure_reason
            FROM login_attempts
        ";
        $params = [];

        if ($email) {
            $sql .= " WHERE email = :email";
            $params['email'] = $email;
        }

        $sql .= " ORDER BY attempt_time DESC LIMIT :limit";

        $stmt = $conn->prepare($sql);
        if ($email) {
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute($params);

        respondSuccess($stmt->fetchAll());
    }

    // GET /api/v1/admin/security/stats
    if ($method === 'GET' && preg_match('/\/admin\/security\/stats$/', $uri)) {
        $stats = [];

        $stmt = $conn->query("
            SELECT
                COUNT(*) AS total_attempts,
                SUM(CASE WHEN success = TRUE THEN 1 ELSE 0 END) AS successful,
                SUM(CASE WHEN success = FALSE THEN 1 ELSE 0 END) AS failed
            FROM login_attempts
            WHERE DATE(attempt_time) = CURDATE()
        ");
        $stats['today'] = $stmt->fetch();

        $stmt = $conn->query("
            SELECT COUNT(*) AS count
            FROM users
            WHERE locked_until IS NOT NULL AND locked_until > NOW()
        ");
        $stats['active_lockouts'] = (int) $stmt->fetchColumn();

        $stmt = $conn->query("
            SELECT ip_address, COUNT(*) AS attempts
            FROM login_attempts
            WHERE success = FALSE
            AND attempt_time >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
            GROUP BY ip_address
            ORDER BY attempts DESC
            LIMIT 10
        ");
        $stats['top_failed_ips'] = $stmt->fetchAll();

        $stmt = $conn->query("
            SELECT
                DATE(attempt_time) AS date,
                COUNT(*) AS total,
                SUM(CASE WHEN success = TRUE THEN 1 ELSE 0 END) AS successful,
                ROUND((SUM(CASE WHEN success = TRUE THEN 1 ELSE 0 END) / COUNT(*)) * 100, 1) AS success_rate
            FROM login_attempts
            WHERE attempt_time >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            GROUP BY DATE(attempt_time)
            ORDER BY date DESC
        ");
        $stats['success_rate_trend'] = $stmt->fetchAll();

        respondSuccess($stats);
    }

    respondError('Endpoint not found', 404);
} catch (Throwable $e) {
    $status = (int) $e->getCode();
    if ($status < 400 || $status >= 600) {
        $status = 500;
    }
    respondError($e->getMessage(), $status);
}
