<?php
/**
 * Admin Email Settings API
 * GET /api/admin/settings/email
 * PUT /api/admin/settings/email
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../middleware/auth.php';

header('Content-Type: application/json');

$admin = requireAdmin();

try {
    $db = Database::getInstance()->getConnection();

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $stmt = $db->query('SELECT setting_key, setting_value FROM email_settings ORDER BY setting_key');
        $rows = $stmt->fetchAll();

        $settings = [
            'smtp_host' => '',
            'smtp_port' => '587',
            'smtp_username' => '',
            'smtp_password' => '',
            'smtp_encryption' => 'tls',
            'from_email' => '',
            'from_name' => 'Alumni System',
        ];

        foreach ($rows as $row) {
            $settings[$row['setting_key']] = (string)($row['setting_value'] ?? '');
        }

        $safeSettings = $settings;
        if (!empty($safeSettings['smtp_password'])) {
            $safeSettings['smtp_password'] = '********';
        }

        $stmt = $db->query('SELECT * FROM email_templates ORDER BY template_key');
        $templates = $stmt->fetchAll();

        foreach ($templates as &$template) {
            $template['type'] = $template['template_key'];
            $template['key'] = $template['template_key'];
        }
        unset($template);

        $camelSettings = [
            'smtpHost' => $safeSettings['smtp_host'],
            'smtpPort' => $safeSettings['smtp_port'],
            'smtpUsername' => $safeSettings['smtp_username'],
            'smtpPassword' => $safeSettings['smtp_password'],
            'smtpSecure' => strtolower((string)$safeSettings['smtp_encryption']) === 'ssl',
            'fromEmail' => $safeSettings['from_email'],
            'fromName' => $safeSettings['from_name'],
        ];

        echo json_encode([
            'success' => true,
            'data' => array_merge($camelSettings, [
                'settings' => $safeSettings,
                'templates' => $templates,
            ]),
        ]);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
        $data = json_decode(file_get_contents('php://input'), true) ?? [];

        $settingsInput = $data['settings'] ?? $data;

        $normalized = [
            'smtp_host' => $settingsInput['smtp_host'] ?? ($settingsInput['smtpHost'] ?? null),
            'smtp_port' => $settingsInput['smtp_port'] ?? ($settingsInput['smtpPort'] ?? null),
            'smtp_username' => $settingsInput['smtp_username'] ?? ($settingsInput['smtpUsername'] ?? null),
            'smtp_password' => $settingsInput['smtp_password'] ?? ($settingsInput['smtpPassword'] ?? null),
            'smtp_encryption' => $settingsInput['smtp_encryption'] ?? ((isset($settingsInput['smtpSecure']) && $settingsInput['smtpSecure']) ? 'ssl' : null),
            'from_email' => $settingsInput['from_email'] ?? ($settingsInput['fromEmail'] ?? null),
            'from_name' => $settingsInput['from_name'] ?? ($settingsInput['fromName'] ?? null),
        ];

        $stmt = $db->prepare(
            "INSERT INTO email_settings (setting_key, setting_value, setting_type, description, updated_by, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, NOW(), NOW())
             ON DUPLICATE KEY UPDATE
                 setting_value = VALUES(setting_value),
                 setting_type = VALUES(setting_type),
                 description = VALUES(description),
                 updated_by = VALUES(updated_by),
                 updated_at = NOW()"
        );

        foreach ($normalized as $key => $value) {
            if ($value === null) {
                continue;
            }

            if ($key === 'smtp_password' && $value === '********') {
                continue;
            }

            $type = $key === 'from_email' ? 'email' : 'text';
            $description = ucwords(str_replace('_', ' ', $key));
            $stmt->execute([$key, (string)$value, $type, $description, (int)$admin['id']]);
        }

        if (!empty($data['template']) && is_array($data['template'])) {
            $template = $data['template'];
            $templateKey = trim((string)($template['key'] ?? ($template['type'] ?? '')));
            $subject = $template['subject'] ?? '';
            $body = $template['body'] ?? '';

            if ($templateKey !== '') {
                $templateName = $template['template_name'] ?? ucwords(str_replace('_', ' ', $templateKey));

                $stmt = $db->prepare(
                    "INSERT INTO email_templates (template_key, template_name, subject, body, is_active, updated_by, created_at, updated_at)
                     VALUES (?, ?, ?, ?, 1, ?, NOW(), NOW())
                     ON DUPLICATE KEY UPDATE
                         template_name = VALUES(template_name),
                         subject = VALUES(subject),
                         body = VALUES(body),
                         updated_by = VALUES(updated_by),
                         updated_at = NOW()"
                );
                $stmt->execute([$templateKey, $templateName, (string)$subject, (string)$body, (int)$admin['id']]);
            }
        }

        echo json_encode(['success' => true, 'message' => 'Email settings updated']);
        exit;
    }

    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
} catch (PDOException $e) {
    error_log('Admin Email Settings Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}

