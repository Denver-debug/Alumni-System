<?php
/**
 * Admin Create Announcement API
 * POST /api/admin/announcements
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../middleware/auth.php';
require_once __DIR__ . '/../../../config/auth.php';
require_once __DIR__ . '/../../../utils/helpers.php';

header('Content-Type: application/json');

$admin = requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
$isMultipart = stripos($contentType, 'multipart/form-data') !== false;
$data = $isMultipart ? $_POST : (json_decode(file_get_contents('php://input'), true) ?? []);

$uploadedCoverImage = null;
$imageFile = $_FILES['cover_image_file'] ?? ($_FILES['cover_image'] ?? ($_FILES['image'] ?? null));

if ($imageFile && (int)($imageFile['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
    try {
        $uploadedCoverImage = uploadAnnouncementImage($imageFile)['url'];
    } catch (RuntimeException $e) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Image upload failed',
            'errors' => [$e->getMessage()],
        ]);
        exit;
    }
}

$nullIfEmpty = static function ($value) {
    if ($value === null) {
        return null;
    }

    if (is_string($value)) {
        $trimmed = trim($value);
        return $trimmed === '' ? null : $trimmed;
    }

    return $value;
};

$normalizeDateTime = static function ($value) use ($nullIfEmpty): ?string {
    $value = $nullIfEmpty($value);
    if ($value === null || !is_string($value)) {
        return null;
    }

    $value = str_replace('T', ' ', $value);

    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
        return $value . ' 00:00:00';
    }

    if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}$/', $value)) {
        return $value . ':00';
    }

    return preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $value) ? $value : null;
};

$normalizeCampusIds = static function ($value) use ($nullIfEmpty): array {
    if ($value === null) {
        return [];
    }

    if (is_string($value)) {
        $trimmed = $nullIfEmpty($value);
        if ($trimmed === null) {
            return [];
        }

        $decoded = json_decode($trimmed, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $value = $decoded;
        } else {
            $value = [$trimmed];
        }
    }

    if (!is_array($value)) {
        return [];
    }

    $campusIds = [];
    foreach ($value as $campusId) {
        if ($campusId === null || $campusId === '') {
            continue;
        }

        if (!is_numeric($campusId)) {
            continue;
        }

        $campusIds[] = (int)$campusId;
    }

    return array_values(array_unique(array_filter($campusIds, static function ($campusId) {
        return $campusId > 0;
    })));
};

if (empty($data['title'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Title is required']);
    exit;
}

try {
    $db = Database::getInstance()->getConnection();

    $targetTypes = ['all', 'college', 'program', 'section', 'batch_year'];
    $statuses = ['draft', 'published', 'archived'];
    $priorities = ['low', 'normal', 'high', 'urgent'];

    $rawTargetType = $data['target_type'] ?? 'all';
    $rawStatus = $data['status'] ?? 'draft';
    $rawPriority = $data['priority'] ?? 'normal';

    $targetType = in_array($rawTargetType, $targetTypes, true) ? $rawTargetType : 'all';
    $status = in_array($rawStatus, $statuses, true) ? $rawStatus : 'draft';
    $priority = in_array($rawPriority, $priorities, true) ? $rawPriority : 'normal';
    if ($priority === 'important') {
        $priority = 'high';
    }

    $campusIds = $normalizeCampusIds($data['campus_ids'] ?? ($data['campus_id'] ?? null));
    if (empty($campusIds) && in_array($admin['role'], ['campus_admin', 'staff'], true) && !empty($admin['campus_id'])) {
        $campusIds = [(int)$admin['campus_id']];
    }
    if (!empty($campusIds)) {
        $placeholders = implode(',', array_fill(0, count($campusIds), '?'));
        $campusCheck = $db->prepare("SELECT id FROM campuses WHERE id IN ($placeholders)");
        $campusCheck->execute($campusIds);
        $validCampusIds = array_map('intval', $campusCheck->fetchAll(PDO::FETCH_COLUMN, 0));

        if (count($validCampusIds) !== count($campusIds)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'One or more selected campuses are invalid']);
            exit;
        }
    }

    $campusId = $campusIds[0] ?? null;

    $targetId = $data['target_id'] ?? null;
    if (($targetId === null || $targetId === '') && $targetType === 'college' && !empty($data['target_colleges'][0])) {
        $targetId = (int)$data['target_colleges'][0];
    }
    if (($targetId === null || $targetId === '') && $targetType === 'program' && !empty($data['target_programs'][0])) {
        $targetId = (int)$data['target_programs'][0];
    }
    if (($targetId === null || $targetId === '') && $targetType === 'section' && !empty($data['target_sections'][0])) {
        $targetId = (int)$data['target_sections'][0];
    }

    $targetBatchYear = $data['target_batch_year'] ?? null;
    if (($targetBatchYear === null || $targetBatchYear === '') && !empty($data['target_batch_years'][0])) {
        $targetBatchYear = (int)$data['target_batch_years'][0];
    }
    if (($targetBatchYear === null || $targetBatchYear === '') && $targetType === 'batch_year' && $targetId !== null && $targetId !== '') {
        $targetBatchYear = (int)$targetId;
        $targetId = null;
    }

    $title = $nullIfEmpty($data['title'] ?? null);
    $content = $data['content'] ?? '';
    if (!is_string($content)) {
        $content = '';
    }

    $excerpt = $nullIfEmpty($data['excerpt'] ?? null);
    $coverImageSource = $uploadedCoverImage ?? ($data['cover_image'] ?? ($data['image_url'] ?? null));
    $coverImage = is_string($coverImageSource) ? $nullIfEmpty($coverImageSource) : null;
    $publishDate = $normalizeDateTime($data['publish_date'] ?? ($data['publish_at'] ?? null));
    $expireDate = $normalizeDateTime($data['expire_date'] ?? ($data['expires_at'] ?? null));

    $db->beginTransaction();

    $stmt = $db->prepare(
        "INSERT INTO announcements (
            title, content, excerpt, cover_image, campus_id,
            target_type, target_id, target_batch_year,
            priority, is_pinned, publish_date, expire_date, status,
            created_by, created_at
        ) VALUES (
            :title, :content, :excerpt, :cover_image, :campus_id,
            :target_type, :target_id, :target_batch_year,
            :priority, :is_pinned, :publish_date, :expire_date, :status,
            :created_by, NOW()
        )"
    );

    $stmt->execute([
        'title' => $title,
        'content' => $content,
        'excerpt' => $excerpt,
        'cover_image' => $coverImage,
        'campus_id' => $campusId,
        'target_type' => $targetType,
        'target_id' => ($targetId === '' || $targetId === null) ? null : (int)$targetId,
        'target_batch_year' => ($targetBatchYear === '' || $targetBatchYear === null) ? null : (int)$targetBatchYear,
        'priority' => $priority,
        'is_pinned' => !empty($data['is_pinned']) ? 1 : 0,
        'publish_date' => $publishDate,
        'expire_date' => $expireDate,
        'status' => $status,
        'created_by' => (int)$admin['id'],
    ]);

    $announcementId = (int)$db->lastInsertId();

    if (!empty($campusIds)) {
        $junction = $db->prepare('INSERT INTO announcement_campuses (announcement_id, campus_id, created_at) VALUES (?, ?, NOW())');
        foreach ($campusIds as $campusIdValue) {
            $junction->execute([$announcementId, $campusIdValue]);
        }
    }

    $db->commit();
    logAdminActivity((int)$admin['id'], 'create', 'Created announcement: ' . $title, 'announcement', $announcementId);

    echo json_encode([
        'success' => true,
        'message' => 'Announcement created',
        'data' => ['id' => $announcementId],
    ]);
} catch (PDOException $e) {
    if (isset($db) && $db instanceof PDO && $db->inTransaction()) {
        $db->rollBack();
    }

    if ($uploadedCoverImage !== null && isLocalUploadPath($uploadedCoverImage)) {
        deleteLocalImage($uploadedCoverImage);
    }

    error_log('Admin Create Announcement Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}

