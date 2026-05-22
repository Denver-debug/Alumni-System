<?php
/**
 * Legacy Settings API - General
 * GET/PUT /api/settings/general
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../utils/helpers.php';
require_once __DIR__ . '/../../middleware/auth.php';

requireAdmin();

try {
    $db = Database::getInstance()->getConnection();

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $stmt = $db->query("SELECT content_key, content_value FROM site_content WHERE section = 'settings' AND content_key IN ('maintenance_mode', 'registration_enabled', 'email_verification_required')");
        $items = $stmt->fetchAll();

        $data = [
            'maintenance_mode' => false,
            'registration_enabled' => true,
            'email_verification_required' => true,
        ];

        foreach ($items as $item) {
            $key = $item['content_key'];
            $data[$key] = in_array(strtolower((string) $item['content_value']), ['1', 'true', 'yes'], true);
        }

        respondSuccess($data);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
        $data = getRequestBody();
        $keys = ['maintenance_mode', 'registration_enabled', 'email_verification_required'];

        foreach ($keys as $key) {
            if (!array_key_exists($key, $data)) {
                continue;
            }

            $value = !empty($data[$key]) ? '1' : '0';
            $stmt = $db->prepare("\n                INSERT INTO site_content (section, content_key, title, content_value, content_type, is_active, created_at, updated_at)\n                VALUES ('settings', ?, ?, ?, 'text', 1, NOW(), NOW())\n                ON DUPLICATE KEY UPDATE\n                    content_value = VALUES(content_value),\n                    title = VALUES(title),\n                    updated_at = NOW()\n            ");
            $stmt->execute([$key, ucwords(str_replace('_', ' ', $key)), $value]);
        }

        respondSuccess(['message' => 'General settings updated']);
    }

    respondError('Method not allowed', 405);
} catch (Exception $e) {
    respondError('Failed to process general settings: ' . $e->getMessage(), 500);
}
