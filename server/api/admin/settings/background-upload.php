<?php
/**
 * Admin Theme Background Upload API
 * POST /api/admin/settings/theme/background-upload
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../utils/helpers.php';
require_once __DIR__ . '/../../../utils/uploads.php';
require_once __DIR__ . '/../../../middleware/auth.php';

$admin = requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respondError('Method not allowed', 405);
}

if (!isset($_FILES['background']) || !is_array($_FILES['background'])) {
    respondError('No background file uploaded', 400);
}

$backgroundFile = $_FILES['background'];
if (($backgroundFile['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
    respondError('Background upload failed', 400);
}

try {
    $uploader = new FileUploader('site', ALLOWED_IMAGE_TYPES, 7 * 1024 * 1024);
    $filename = $uploader->upload($backgroundFile, 'auth_background_' . date('Ymd_His'));

    if (!$filename) {
        respondError('Failed to upload background image: ' . implode(', ', $uploader->getErrors()), 400);
    }

    $backgroundUrl = $uploader->getUrl($filename);

    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare(
        "INSERT INTO theme_settings (setting_key, setting_value, setting_type, description, updated_by, created_at, updated_at)
         VALUES (?, ?, 'image', 'Authentication page background image URL', ?, NOW(), NOW())
         ON DUPLICATE KEY UPDATE
            setting_value = VALUES(setting_value),
            setting_type = VALUES(setting_type),
            description = VALUES(description),
            updated_by = VALUES(updated_by),
            updated_at = NOW()"
    );
    $stmt->execute(['auth_background_image_url', $backgroundUrl, (int)$admin['id']]);

    respondSuccess([
        'auth_background_image_url' => $backgroundUrl,
        'url' => $backgroundUrl,
        'filename' => $filename,
    ], 200, 'Background image uploaded successfully');
} catch (Exception $e) {
    respondError('Failed to upload background image: ' . $e->getMessage(), 500);
}
