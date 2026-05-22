<?php
/**
 * Legacy Settings API - Email
 * PUT /api/settings/email
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../utils/helpers.php';
require_once __DIR__ . '/../../middleware/auth.php';

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    respondError('Method not allowed', 405);
}

try {
    $db = Database::getInstance()->getConnection();
    $data = getRequestBody();

    $settings = [
        'smtp_host' => [
            'value' => $data['smtp_host'] ?? ($data['smtpHost'] ?? ''),
            'type' => 'text',
            'description' => 'SMTP server host',
        ],
        'smtp_port' => [
            'value' => (string)($data['smtp_port'] ?? ($data['smtpPort'] ?? 587)),
            'type' => 'text',
            'description' => 'SMTP server port',
        ],
        'smtp_username' => [
            'value' => $data['smtp_username'] ?? ($data['smtpUsername'] ?? ''),
            'type' => 'text',
            'description' => 'SMTP username',
        ],
        'smtp_password' => [
            'value' => $data['smtp_password'] ?? ($data['smtpPassword'] ?? ''),
            'type' => 'text',
            'description' => 'SMTP password',
        ],
        'smtp_encryption' => [
            'value' => $data['smtp_encryption'] ?? (($data['smtpSecure'] ?? false) ? 'ssl' : 'tls'),
            'type' => 'text',
            'description' => 'SMTP encryption type',
        ],
        'from_email' => [
            'value' => $data['from_email'] ?? ($data['fromEmail'] ?? ''),
            'type' => 'email',
            'description' => 'Sender email address',
        ],
        'from_name' => [
            'value' => $data['from_name'] ?? ($data['fromName'] ?? 'Alumni System'),
            'type' => 'text',
            'description' => 'Sender display name',
        ],
    ];

    $stmt = $db->prepare("\n        INSERT INTO email_settings (setting_key, setting_value, setting_type, description, created_at, updated_at)\n        VALUES (?, ?, ?, ?, NOW(), NOW())\n        ON DUPLICATE KEY UPDATE\n            setting_value = VALUES(setting_value),\n            setting_type = VALUES(setting_type),\n            description = VALUES(description),\n            updated_at = NOW()\n    ");

    foreach ($settings as $key => $meta) {
        $stmt->execute([
            $key,
            (string)$meta['value'],
            $meta['type'],
            $meta['description'],
        ]);
    }

    respondSuccess(['message' => 'Email settings updated', 'updated_keys' => array_keys($settings)]);
} catch (Exception $e) {
    respondError('Failed to update email settings: ' . $e->getMessage(), 500);
}
