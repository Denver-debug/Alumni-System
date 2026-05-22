<?php
/**
 * Admin Update Announcement API
 * PUT /api/admin/announcements/{id}
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../middleware/auth.php';
require_once __DIR__ . '/../../../config/auth.php';
require_once __DIR__ . '/../../../utils/helpers.php';

header('Content-Type: application/json');

$admin = requireAdmin();

if (!in_array($_SERVER['REQUEST_METHOD'], ['PUT', 'POST'], true)) {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$announcementId = $GLOBALS['url_params']['id'] ?? null;
$currentUser = getCurrentUser();
$userRole = $currentUser['role'] ?? 'alumni';
$userCampusId = $currentUser['campus_id'] ?? null;

if (!$announcementId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Announcement ID required']);
    exit;
}

if (in_array($userRole, ['campus_admin', 'staff'], true) && !$userCampusId) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Campus assignment required']);
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
        $data['cover_image'] = $uploadedCoverImage;
        unset($data['image'], $data['image_url']);
    } catch (RuntimeException $e) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Image upload failed',
            'errors' => [$e->getMessage()],
        ]);
        exit;
    }
} elseif (array_key_exists('image_url', $data)) {
    $data['cover_image'] = $data['image_url'];
    unset($data['image_url'], $data['image']);
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

try {
    $db = Database::getInstance()->getConnection();
    
    $stmt = $db->prepare("SELECT id, cover_image, campus_id FROM announcements WHERE id = ?");
    $stmt->execute([$announcementId]);
    $existingAnnouncement = $stmt->fetch();
    if (!$existingAnnouncement) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Announcement not found']);
        exit;
    }

    if (in_array($userRole, ['campus_admin', 'staff'], true) && $userCampusId) {
        $campusMatch = $db->prepare(
            'SELECT 1 FROM announcement_campuses WHERE announcement_id = ? AND campus_id = ? LIMIT 1'
        );
        $campusMatch->execute([$announcementId, $userCampusId]);
        $announcementCampusId = (int)($existingAnnouncement['campus_id'] ?? 0);
        if ($announcementCampusId !== (int)$userCampusId && !$campusMatch->fetchColumn() && $announcementCampusId !== 0) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Announcement not found']);
            exit;
        }
    }

    $oldCoverImage = $existingAnnouncement['cover_image'] ?? null;
    $newCoverImage = null;
    $coverImageTouched = false;
    $campusTouched = false;
    $campusIdsProvided = array_key_exists('campus_ids', $data) || array_key_exists('campus_id', $data);
    $campusIds = [];

    if ($campusIdsProvided) {
        $campusIds = $normalizeCampusIds($data['campus_ids'] ?? ($data['campus_id'] ?? null));
        if (empty($campusIds) && in_array($userRole, ['campus_admin', 'staff'], true) && !empty($userCampusId)) {
            $campusIds = [(int)$userCampusId];
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

        $campusTouched = true;
    }
    
    $targetTypes = ['all', 'college', 'program', 'section', 'batch_year'];
    $statuses = ['draft', 'published', 'archived'];
    $priorities = ['low', 'normal', 'high', 'urgent'];

    $updates = [];
    $params = [];

    $columnMap = [
        'title' => 'title',
        'content' => 'content',
        'excerpt' => 'excerpt',
        'cover_image' => 'cover_image',
        'image_url' => 'cover_image',
        'publish_date' => 'publish_date',
        'publish_at' => 'publish_date',
        'expire_date' => 'expire_date',
        'expires_at' => 'expire_date',
        'target_id' => 'target_id',
        'target_batch_year' => 'target_batch_year',
    ];

    foreach ($columnMap as $input => $column) {
        if (!array_key_exists($input, $data)) {
            continue;
        }

        $value = $data[$input];
        if ($column === 'publish_date' || $column === 'expire_date') {
            $value = $normalizeDateTime($value);
        } elseif ($column === 'target_id' || $column === 'target_batch_year') {
            $value = ($value === '' || $value === null) ? null : (int)$value;
        } elseif ($column === 'campus_id') {
            $value = ($value === '' || $value === null) ? null : (int)$value;
        } elseif ($column === 'cover_image' || $column === 'title' || $column === 'content' || $column === 'excerpt') {
            $value = is_string($value) ? $nullIfEmpty($value) : null;
            if ($column === 'content' && $value === null) {
                $value = '';
            }
        }

        $updates[] = "$column = ?";
        $params[] = $value;

        if ($column === 'cover_image') {
            $newCoverImage = $value;
            $coverImageTouched = true;
        }

    }

    if ($campusTouched) {
        $updates[] = 'campus_id = ?';
        $params[] = $campusIds[0] ?? null;
    }

    if (array_key_exists('target_type', $data)) {
        $updates[] = 'target_type = ?';
        $params[] = in_array($data['target_type'], $targetTypes, true) ? $data['target_type'] : 'all';
    }

    if (array_key_exists('status', $data)) {
        $updates[] = 'status = ?';
        $params[] = in_array($data['status'], $statuses, true) ? $data['status'] : 'draft';
    }

    if (array_key_exists('priority', $data)) {
        $priority = $data['priority'];
        if ($priority === 'important') {
            $priority = 'high';
        }
        $updates[] = 'priority = ?';
        $params[] = in_array($priority, $priorities, true) ? $priority : 'normal';
    }

    if (array_key_exists('is_pinned', $data)) {
        $updates[] = 'is_pinned = ?';
        $params[] = !empty($data['is_pinned']) ? 1 : 0;
    }
    
    if (empty($updates)) {
        echo json_encode(['success' => true, 'message' => 'No changes']);
        exit;
    }
    
    $updates[] = "updated_at = NOW()";
    $params[] = $announcementId;

    $db->beginTransaction();
    
    $stmt = $db->prepare("UPDATE announcements SET " . implode(', ', $updates) . " WHERE id = ?");
    $stmt->execute($params);

    if ($campusTouched) {
        $db->prepare('DELETE FROM announcement_campuses WHERE announcement_id = ?')->execute([$announcementId]);
        if (!empty($campusIds)) {
            $junction = $db->prepare('INSERT INTO announcement_campuses (announcement_id, campus_id, created_at) VALUES (?, ?, NOW())');
            foreach ($campusIds as $campusIdValue) {
                $junction->execute([$announcementId, $campusIdValue]);
            }
        }
    }

    if ($coverImageTouched && $newCoverImage !== $oldCoverImage && isLocalUploadPath($oldCoverImage)) {
        deleteLocalImage($oldCoverImage);
    }

    $db->commit();
    
    logAdminActivity((int)$admin['id'], 'update', 'Updated announcement #' . $announcementId, 'announcement', (int)$announcementId);
    
    echo json_encode(['success' => true, 'message' => 'Announcement updated']);
    
} catch (PDOException $e) {
    if (isset($db) && $db instanceof PDO && $db->inTransaction()) {
        $db->rollBack();
    }

    if ($uploadedCoverImage !== null && isLocalUploadPath($uploadedCoverImage)) {
        deleteLocalImage($uploadedCoverImage);
    }

    error_log("Admin Update Announcement Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
