<?php
/**
 * Admin Settings - Test SMTP Connection
 * POST /api/admin/settings/test-email
 */

require_once __DIR__ . '/../../../utils/helpers.php';
require_once __DIR__ . '/../../../middleware/auth.php';

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respondError('Method not allowed', 405);
}

$data = getRequestBody();
$host = trim($data['smtpHost'] ?? '');
$port = (int) ($data['smtpPort'] ?? 0);

if ($host === '' || $port <= 0) {
    respondError('smtpHost and smtpPort are required', 400);
}

$errno = 0;
$errstr = '';
$conn = @fsockopen($host, $port, $errno, $errstr, 6);

if (!$conn) {
    respondError('SMTP connection failed: ' . ($errstr ?: 'Unknown error'), 400);
}

fclose($conn);

respondSuccess([
    'host' => $host,
    'port' => $port,
    'message' => 'SMTP connection successful'
]);
