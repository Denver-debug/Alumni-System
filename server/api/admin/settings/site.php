<?php
/**
 * Admin Site Content API
 * GET /api/admin/settings/site - Get site content
 * PUT /api/admin/settings/site - Update site content
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../middleware/auth.php';

header('Content-Type: application/json');

requireAdmin();

try {
    $db = Database::getInstance()->getConnection();
    
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $key = $_GET['key'] ?? null;
        
        if ($key) {
            $stmt = $db->prepare("SELECT * FROM site_content WHERE content_key = ?");
            $stmt->execute([$key]);
            $content = $stmt->fetch();
            echo json_encode(['success' => true, 'data' => $content]);
        } else {
            $stmt = $db->query("SELECT * FROM site_content ORDER BY content_key");
            $contents = $stmt->fetchAll();
            echo json_encode(['success' => true, 'data' => $contents]);
        }
        
    } elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (empty($data['key'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Content key required']);
            exit;
        }
        
        $stmt = $db->prepare("SELECT id FROM site_content WHERE content_key = ?");
        $stmt->execute([$data['key']]);
        $existing = $stmt->fetch();
        
        if ($existing) {
            $stmt = $db->prepare("UPDATE site_content SET content_value = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$data['value'], $existing['id']]);
        } else {
            $stmt = $db->prepare("INSERT INTO site_content (content_key, content_value, created_at) VALUES (?, ?, NOW())");
            $stmt->execute([$data['key'], $data['value']]);
        }
        
        echo json_encode(['success' => true, 'message' => 'Content updated']);
        
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Batch update
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!is_array($data['items'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Items array required']);
            exit;
        }
        
        $db->beginTransaction();
        
        foreach ($data['items'] as $item) {
            if (empty($item['key'])) continue;
            
            $stmt = $db->prepare("
                INSERT INTO site_content (content_key, content_value, created_at)
                VALUES (?, ?, NOW())
                ON DUPLICATE KEY UPDATE content_value = ?, updated_at = NOW()
            ");
            $stmt->execute([$item['key'], $item['value'], $item['value']]);
        }
        
        $db->commit();
        
        echo json_encode(['success' => true, 'message' => 'Content batch updated']);
        
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
