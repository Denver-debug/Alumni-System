<?php
/**
 * Admin Theme Settings API
 * GET /api/admin/settings/theme - Get theme settings
 * PUT /api/admin/settings/theme - Update theme settings
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../middleware/auth.php';

header('Content-Type: application/json');

requireAdmin();

try {
    $db = Database::getInstance()->getConnection();
    
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $stmt = $db->query("SELECT * FROM theme_settings ORDER BY id DESC LIMIT 1");
        $settings = $stmt->fetch();
        
        if (!$settings) {
            $settings = [
                'primary_color' => '#4f46e5',
                'secondary_color' => '#06b6d4',
                'accent_color' => '#f59e0b',
                'background_color' => '#f8fafc',
                'text_color' => '#1e293b',
                'font_family' => 'Inter',
                'logo_url' => null,
                'favicon_url' => null
            ];
        }
        
        echo json_encode(['success' => true, 'data' => $settings]);
        
    } elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
        $data = json_decode(file_get_contents('php://input'), true);
        
        $fields = ['primary_color', 'secondary_color', 'accent_color', 'background_color', 
                   'text_color', 'font_family', 'logo_url', 'favicon_url', 'custom_css'];
        
        // Check if exists
        $stmt = $db->query("SELECT id FROM theme_settings LIMIT 1");
        $existing = $stmt->fetch();
        
        if ($existing) {
            $updates = [];
            $params = [];
            foreach ($fields as $field) {
                if (isset($data[$field])) {
                    $updates[] = "$field = ?";
                    $params[] = $data[$field];
                }
            }
            if (!empty($updates)) {
                $updates[] = "updated_at = NOW()";
                $params[] = $existing['id'];
                $stmt = $db->prepare("UPDATE theme_settings SET " . implode(', ', $updates) . " WHERE id = ?");
                $stmt->execute($params);
            }
        } else {
            $stmt = $db->prepare("
                INSERT INTO theme_settings (primary_color, secondary_color, accent_color, background_color, text_color, font_family, logo_url, favicon_url, custom_css, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([
                $data['primary_color'] ?? '#4f46e5',
                $data['secondary_color'] ?? '#06b6d4',
                $data['accent_color'] ?? '#f59e0b',
                $data['background_color'] ?? '#f8fafc',
                $data['text_color'] ?? '#1e293b',
                $data['font_family'] ?? 'Inter',
                $data['logo_url'] ?? null,
                $data['favicon_url'] ?? null,
                $data['custom_css'] ?? null
            ]);
        }
        
        echo json_encode(['success' => true, 'message' => 'Theme settings updated']);
        
    } else {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    }
    
} catch (PDOException $e) {
    error_log("Admin Theme Settings Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
