<?php
/**
 * Public Site Content API
 * GET /api/site/content
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../utils/helpers.php';

try {
    $db = Database::getInstance()->getConnection();

    $key = $_GET['key'] ?? null;
    $section = $_GET['section'] ?? null;

    if ($key) {
        if ($section) {
            $stmt = $db->prepare("SELECT * FROM site_content WHERE section = ? AND content_key = ? AND is_active = 1 LIMIT 1");
            $stmt->execute([$section, $key]);
        } else {
            $stmt = $db->prepare("SELECT * FROM site_content WHERE content_key = ? AND is_active = 1 ORDER BY updated_at DESC LIMIT 1");
            $stmt->execute([$key]);
        }
        $content = $stmt->fetch();
        respondSuccess($content ?: []);
    }

    if ($section) {
        $stmt = $db->prepare("SELECT * FROM site_content WHERE section = ? AND is_active = 1 ORDER BY display_order, content_key");
        $stmt->execute([$section]);
        respondSuccess($stmt->fetchAll());
    }

    $stmt = $db->query("SELECT * FROM site_content WHERE is_active = 1 ORDER BY section, display_order, content_key");
    respondSuccess($stmt->fetchAll());
} catch (Exception $e) {
    respondError('Failed to load site content: ' . $e->getMessage(), 500);
}
