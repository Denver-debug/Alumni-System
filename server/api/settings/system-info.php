<?php
/**
 * Legacy Settings API - System Info
 * GET /api/settings/system-info
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../utils/helpers.php';
require_once __DIR__ . '/../../middleware/auth.php';

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    respondError('Method not allowed', 405);
}

try {
    $db = Database::getInstance()->getConnection();

    $alumniStmt = $db->query("SELECT COUNT(*) as total FROM users WHERE role = 'alumni'");
    $eventsStmt = $db->query("SELECT COUNT(*) as total FROM events");

    respondSuccess([
        'php_version' => PHP_VERSION,
        'total_alumni' => (int) ($alumniStmt->fetch()['total'] ?? 0),
        'total_events' => (int) ($eventsStmt->fetch()['total'] ?? 0),
    ]);
} catch (Exception $e) {
    respondError('Failed to load system info: ' . $e->getMessage(), 500);
}
