<?php
/**
 * Admin Site Content API
 * GET /api/admin/settings/site - Get site content
 * PUT /api/admin/settings/site - Update site content
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../middleware/auth.php';
require_once __DIR__ . '/../../../utils/helpers.php';

header('Content-Type: application/json');

$admin = requireAdmin();

try {
    $db = Database::getInstance()->getConnection();
    
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $key = $_GET['key'] ?? null;
        
        if ($key) {
            $stmt = $db->prepare("SELECT * FROM site_content WHERE content_key = ? ORDER BY updated_at DESC LIMIT 1");
            $stmt->execute([$key]);
            $content = $stmt->fetch();
            echo json_encode(['success' => true, 'data' => $content]);
        } else {
            $stmt = $db->query("SELECT content_key, content_value FROM site_content WHERE section = 'settings' ORDER BY content_key");
            $contents = $stmt->fetchAll();

            $mapped = [];
            foreach ($contents as $item) {
                $mapped[$item['content_key']] = $item['content_value'];
            }

            $savedPrefix = strtoupper(trim((string)($mapped['alumni_id_prefix'] ?? '')));
            if (!preg_match('/^[A-Z]{3}$/', $savedPrefix)) {
                if (function_exists('getAlumniIdPrefix')) {
                    $mapped['alumni_id_prefix'] = getAlumniIdPrefix();
                } else {
                    $mapped['alumni_id_prefix'] = defined('ALUMNI_ID_PREFIX') ? ALUMNI_ID_PREFIX : 'ALM';
                }
            }

            echo json_encode(['success' => true, 'data' => $mapped]);
        }
        
    } elseif ($_SERVER['REQUEST_METHOD'] === 'PUT' || $_SERVER['REQUEST_METHOD'] === 'POST') {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        $data = stripos($contentType, 'application/json') !== false
            ? (json_decode(file_get_contents('php://input'), true) ?? [])
            : $_POST;

        $items = [];

        // Single-key update payload.
        if (!empty($data['key'])) {
            $items[] = [
                'key' => $data['key'],
                'value' => $data['value'] ?? '',
            ];
        }

        // Form payload from settings page.
        foreach ($data as $k => $v) {
            if (in_array($k, ['key', 'value', 'items'], true)) {
                continue;
            }
            $items[] = ['key' => $k, 'value' => $v];
        }

        // Explicit batch payload.
        if (!empty($data['items']) && is_array($data['items'])) {
            foreach ($data['items'] as $item) {
                if (!empty($item['key'])) {
                    $items[] = [
                        'key' => $item['key'],
                        'value' => $item['value'] ?? '',
                    ];
                }
            }
        }

        if (empty($items)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'No content items provided']);
            exit;
        }

        $normalizedItems = [];
        foreach ($items as $item) {
            $keyName = trim((string)($item['key'] ?? ''));
            if ($keyName === '') {
                continue;
            }

            $value = (string)($item['value'] ?? '');

            if ($keyName === 'alumni_id_prefix') {
                if (($admin['role'] ?? '') !== 'system_admin') {
                    http_response_code(403);
                    echo json_encode([
                        'success' => false,
                        'message' => 'Only system administrators can update the Alumni ID Prefix',
                    ]);
                    exit;
                }

                $value = strtoupper(trim($value));
                if (!preg_match('/^[A-Z]{3}$/', $value)) {
                    http_response_code(422);
                    echo json_encode([
                        'success' => false,
                        'message' => 'Alumni ID Prefix must be exactly 3 letters (A-Z)',
                    ]);
                    exit;
                }
            }

            $normalizedItems[$keyName] = $value;
        }

        if (empty($normalizedItems)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'No valid content items provided']);
            exit;
        }
        
        $db->beginTransaction();

        $stmt = $db->prepare("
            INSERT INTO site_content (section, content_key, title, content_value, content_type, is_active, created_at, updated_at)
            VALUES ('settings', ?, ?, ?, 'text', 1, NOW(), NOW())
            ON DUPLICATE KEY UPDATE
                content_value = VALUES(content_value),
                title = VALUES(title),
                updated_at = NOW()
        ");
        
        foreach ($normalizedItems as $keyName => $value) {
            $title = ucwords(preg_replace('/([a-z])([A-Z])/', '$1 $2', str_replace('_', ' ', $keyName)));
            $stmt->execute([$keyName, $title, $value]);
        }
        
        $db->commit();
        
        echo json_encode(['success' => true, 'message' => 'Content updated']);

    } else {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    }
    
} catch (PDOException $e) {
    if (isset($db) && $db->inTransaction()) $db->rollBack();
    error_log("Admin Site Content Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
