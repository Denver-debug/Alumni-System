<?php
/**
 * Admin Email Settings API
 * GET /api/admin/settings/email - Get email settings and templates
 * PUT /api/admin/settings/email - Update email settings
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../middleware/auth.php';

header('Content-Type: application/json');

requireAdmin();

try {
    $db = Database::getInstance()->getConnection();
    
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Get settings
        $stmt = $db->query("SELECT * FROM email_settings ORDER BY id DESC LIMIT 1");
        $settings = $stmt->fetch();
        
        // Hide sensitive data
        if ($settings) {
            $settings['smtp_password'] = $settings['smtp_password'] ? '********' : null;
        }
        
        // Get templates
        $stmt = $db->query("SELECT * FROM email_templates ORDER BY template_key");
        $templates = $stmt->fetchAll();
        
        echo json_encode([
            'success' => true,
            'data' => [
                'settings' => $settings,
                'templates' => $templates
            ]
        ]);
        
    } elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (isset($data['settings'])) {
            $settings = $data['settings'];
            
            $stmt = $db->query("SELECT id FROM email_settings LIMIT 1");
            $existing = $stmt->fetch();
            
            if ($existing) {
                $updates = [];
                $params = [];
                
                $fields = ['smtp_host', 'smtp_port', 'smtp_username', 'smtp_password', 
                          'smtp_encryption', 'from_email', 'from_name', 'is_enabled'];
                
                foreach ($fields as $field) {
                    if (isset($settings[$field])) {
                        // Don't update password if placeholder
                        if ($field === 'smtp_password' && $settings[$field] === '********') continue;
                        $updates[] = "$field = ?";
                        $params[] = $settings[$field];
                    }
                }
                
                if (!empty($updates)) {
                    $updates[] = "updated_at = NOW()";
                    $params[] = $existing['id'];
                    $stmt = $db->prepare("UPDATE email_settings SET " . implode(', ', $updates) . " WHERE id = ?");
                    $stmt->execute($params);
                }
            } else {
                $stmt = $db->prepare("
                    INSERT INTO email_settings (smtp_host, smtp_port, smtp_username, smtp_password, smtp_encryption, from_email, from_name, is_enabled, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
                ");
                $stmt->execute([
                    $settings['smtp_host'] ?? '',
                    $settings['smtp_port'] ?? 587,
                    $settings['smtp_username'] ?? '',
                    $settings['smtp_password'] ?? '',
                    $settings['smtp_encryption'] ?? 'tls',
                    $settings['from_email'] ?? '',
                    $settings['from_name'] ?? 'Alumni System',
                    $settings['is_enabled'] ?? 0
                ]);
            }
        }
        
        if (isset($data['template'])) {
            $template = $data['template'];
            
            $stmt = $db->prepare("SELECT id FROM email_templates WHERE template_key = ?");
            $stmt->execute([$template['key']]);
            $existing = $stmt->fetch();
            
            if ($existing) {
                $stmt = $db->prepare("UPDATE email_templates SET subject = ?, body = ?, updated_at = NOW() WHERE id = ?");
                $stmt->execute([$template['subject'], $template['body'], $existing['id']]);
            } else {
                $stmt = $db->prepare("INSERT INTO email_templates (template_key, subject, body, created_at) VALUES (?, ?, ?, NOW())");
                $stmt->execute([$template['key'], $template['subject'], $template['body']]);
            }
        }
        
        echo json_encode(['success' => true, 'message' => 'Email settings updated']);
        
    } else {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    }
    
} catch (PDOException $e) {
    error_log("Admin Email Settings Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
