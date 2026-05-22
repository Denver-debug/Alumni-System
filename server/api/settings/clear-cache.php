<?php
/**
 * Legacy Settings API - Clear Cache
 * POST /api/settings/clear-cache
 */

require_once __DIR__ . '/../../utils/helpers.php';
require_once __DIR__ . '/../../middleware/auth.php';

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respondError('Method not allowed', 405);
}

respondSuccess(['message' => 'Cache clear request completed']);
