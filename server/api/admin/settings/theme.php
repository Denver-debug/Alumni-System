<?php
/**
 * Admin Theme Settings API
 * GET /api/admin/settings/theme - Get theme settings
 * PUT /api/admin/settings/theme - Update theme settings
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../middleware/auth.php';

header('Content-Type: application/json');

$admin = requireAdmin();

$allowedSettings = [
    'primary_color' => ['default' => '#10b981', 'type' => 'color', 'description' => 'Primary brand color'],
    'secondary_color' => ['default' => '#6b7280', 'type' => 'color', 'description' => 'Secondary color'],
    'accent_color' => ['default' => '#f59e0b', 'type' => 'color', 'description' => 'Accent color'],
    'background_color' => ['default' => '#f8fafc', 'type' => 'color', 'description' => 'Background color'],
    'text_color' => ['default' => '#1f2937', 'type' => 'color', 'description' => 'Primary text color'],
    'heading_font' => ['default' => 'Inter', 'type' => 'font', 'description' => 'Heading font family'],
    'body_font' => ['default' => 'Inter', 'type' => 'font', 'description' => 'Body font family'],
    'font_family' => ['default' => 'Inter', 'type' => 'font', 'description' => 'Legacy font family alias'],
    'logo_url' => ['default' => '', 'type' => 'image', 'description' => 'Logo URL'],
    'auth_background_image_url' => ['default' => '', 'type' => 'image', 'description' => 'Authentication page background image URL'],
    'favicon_url' => ['default' => '', 'type' => 'image', 'description' => 'Favicon URL'],
    'sidebar_style' => ['default' => 'dark', 'type' => 'text', 'description' => 'Sidebar visual style'],
    'border_radius' => ['default' => 'md', 'type' => 'size', 'description' => 'Global border radius'],
    'custom_css' => ['default' => '', 'type' => 'text', 'description' => 'Custom CSS overrides'],
];

try {
    $db = Database::getInstance()->getConnection();
    
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $settings = [];
        foreach ($allowedSettings as $key => $meta) {
            $settings[$key] = $meta['default'];
        }

        $keys = array_keys($allowedSettings);
        $placeholders = implode(', ', array_fill(0, count($keys), '?'));
        $stmt = $db->prepare("SELECT setting_key, setting_value FROM theme_settings WHERE setting_key IN ($placeholders)");
        $stmt->execute($keys);
        $rows = $stmt->fetchAll();

        foreach ($rows as $row) {
            $settings[$row['setting_key']] = (string)($row['setting_value'] ?? '');
        }

        if ($settings['body_font'] === '' && $settings['font_family'] !== '') {
            $settings['body_font'] = $settings['font_family'];
        }
        if ($settings['heading_font'] === '' && $settings['font_family'] !== '') {
            $settings['heading_font'] = $settings['font_family'];
        }
        if ($settings['font_family'] === '' && $settings['body_font'] !== '') {
            $settings['font_family'] = $settings['body_font'];
        }
        
        echo json_encode(['success' => true, 'data' => $settings]);
        
    } elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
        $data = json_decode(file_get_contents('php://input'), true) ?? [];

        if (!is_array($data)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid payload']);
            exit;
        }

        $updates = [];
        foreach ($allowedSettings as $key => $meta) {
            if (!array_key_exists($key, $data)) {
                continue;
            }

            $value = $data[$key];
            if (is_bool($value)) {
                $value = $value ? '1' : '0';
            } elseif (is_array($value)) {
                $value = json_encode($value);
            } elseif ($value === null) {
                $value = '';
            } else {
                $value = (string)$value;
            }

            $updates[$key] = $value;
        }

        // Keep backward compatibility with older clients that only send one of these keys.
        if (array_key_exists('font_family', $data) && !array_key_exists('body_font', $updates)) {
            $updates['body_font'] = (string)$data['font_family'];
        }
        if (array_key_exists('body_font', $data) && !array_key_exists('font_family', $updates)) {
            $updates['font_family'] = (string)$data['body_font'];
        }

        if (empty($updates)) {
            echo json_encode(['success' => true, 'message' => 'No changes']);
            exit;
        }

        $stmt = $db->prepare(
            "INSERT INTO theme_settings (setting_key, setting_value, setting_type, description, updated_by, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, NOW(), NOW())
             ON DUPLICATE KEY UPDATE
                setting_value = VALUES(setting_value),
                setting_type = VALUES(setting_type),
                description = VALUES(description),
                updated_by = VALUES(updated_by),
                updated_at = NOW()"
        );

        foreach ($updates as $key => $value) {
            $meta = $allowedSettings[$key] ?? ['type' => 'text', 'description' => ucwords(str_replace('_', ' ', $key))];
            $stmt->execute([
                $key,
                $value,
                $meta['type'],
                $meta['description'],
                (int)$admin['id'],
            ]);
        }
        
        echo json_encode(['success' => true, 'message' => 'Theme settings updated', 'data' => ['updated_keys' => array_keys($updates)]]);
        
    } else {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    }
    
} catch (PDOException $e) {
    error_log("Admin Theme Settings Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
