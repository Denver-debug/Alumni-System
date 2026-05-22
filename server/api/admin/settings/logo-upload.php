<?php
/**
 * Admin Theme Logo Upload API
 * POST /api/admin/settings/theme/logo-upload
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../utils/helpers.php';
require_once __DIR__ . '/../../../utils/uploads.php';
require_once __DIR__ . '/../../../middleware/auth.php';

$admin = requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respondError('Method not allowed', 405);
}

if (!isset($_FILES['logo']) || !is_array($_FILES['logo'])) {
    respondError('No logo file uploaded', 400);
}

$logoFile = $_FILES['logo'];
if (($logoFile['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
    respondError('Logo upload failed', 400);
}

try {
    $uploader = new FileUploader('site', ALLOWED_IMAGE_TYPES, 5 * 1024 * 1024);
    $filename = $uploader->upload($logoFile, 'logo_' . date('Ymd_His'));

    if (!$filename) {
        respondError('Failed to upload logo: ' . implode(', ', $uploader->getErrors()), 400);
    }

    $logoUrl = $uploader->getUrl($filename);

    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare(
        "INSERT INTO theme_settings (setting_key, setting_value, setting_type, description, updated_by, created_at, updated_at)
         VALUES (?, ?, 'image', 'Logo URL', ?, NOW(), NOW())
         ON DUPLICATE KEY UPDATE
            setting_value = VALUES(setting_value),
            setting_type = VALUES(setting_type),
            description = VALUES(description),
            updated_by = VALUES(updated_by),
            updated_at = NOW()"
    );
    $stmt->execute(['logo_url', $logoUrl, (int)$admin['id']]);

    respondSuccess([
        'logo_url' => $logoUrl,
        'url' => $logoUrl,
        'filename' => $filename,
    ], 200, 'Logo uploaded successfully');
} catch (Exception $e) {
    respondError('Failed to upload logo: ' . $e->getMessage(), 500);
}
