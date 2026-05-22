<?php
/**
 * Admin Update Event API
 * PUT /api/admin/events/{id}
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

$eventId = $GLOBALS['url_params']['id'] ?? null;
if (!$eventId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Event ID required']);
    exit;
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

$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
$isMultipart = stripos($contentType, 'multipart/form-data') !== false;
$data = $isMultipart ? $_POST : (json_decode(file_get_contents('php://input'), true) ?? []);

$uploadedCoverImage = null;
$imageFile = $_FILES['event_image'] ?? ($_FILES['image'] ?? null);

if ($imageFile && (int)($imageFile['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
    try {
        $uploadedCoverImage = uploadEventImage($imageFile)['url'];
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
} elseif (array_key_exists('cover_image', $data)) {
    unset($data['image'], $data['image_url']);
} elseif (array_key_exists('image_url', $data)) {
    $data['cover_image'] = $data['image_url'];
    unset($data['image_url'], $data['image']);
} elseif (array_key_exists('image', $data)) {
    $data['cover_image'] = $data['image'];
    unset($data['image']);
}

$normalizeDate = static function ($value) use ($nullIfEmpty): ?string {
    $value = $nullIfEmpty($value);
    if ($value === null || !is_string($value)) {
        return null;
    }

    return preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) ? $value : null;
};

$normalizeTime = static function ($value) use ($nullIfEmpty): ?string {
    $value = $nullIfEmpty($value);
    if ($value === null || !is_string($value)) {
        return null;
    }

    if (preg_match('/^\d{2}:\d{2}$/', $value)) {
        return $value . ':00';
    }

    return preg_match('/^\d{2}:\d{2}:\d{2}$/', $value) ? $value : null;
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

    $stmt = $db->prepare("SELECT id, cover_image FROM events WHERE id = ?");
    $stmt->execute([$eventId]);
    $existingEvent = $stmt->fetch();
    if (!$existingEvent) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Event not found']);
        exit;
    }

    $oldCoverImage = $existingEvent['cover_image'] ?? null;
    $newCoverImage = null;
    $coverImageTouched = false;
    $campusTouched = false;

    $eventTypes = ['seminar', 'reunion', 'workshop', 'webinar', 'networking', 'career_fair', 'sports', 'cultural', 'community_service', 'other'];
    $venueTypes = ['physical', 'online', 'hybrid'];
    $statuses = ['draft', 'upcoming', 'ongoing', 'completed', 'cancelled'];
    $targetTypes = ['all', 'college', 'program', 'section', 'batch_year'];

    $columnMap = [
        'title' => 'title',
        'description' => 'description',
        'event_date' => 'event_date',
        'event_time' => 'event_time',
        'end_date' => 'end_date',
        'end_time' => 'end_time',
        'location' => 'location',
        'max_attendees' => 'max_attendees',
        'registration_deadline' => 'registration_deadline',
        'points_reward' => 'points_reward',
        'attendance_code' => 'attendance_code',
        'cover_image' => 'cover_image',
        'image' => 'cover_image',
        'image_url' => 'cover_image',
        'meeting_link' => 'meeting_link',
        'online_link' => 'meeting_link',
        'virtual_link' => 'meeting_link',
    ];

    $campusIdsProvided = array_key_exists('campus_ids', $data) || array_key_exists('campus_id', $data);
    $campusIds = [];
    if ($campusIdsProvided) {
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

        $campusTouched = true;
    }

    $updates = [];
    $params = [];

    foreach ($columnMap as $inputKey => $column) {
        if (!array_key_exists($inputKey, $data)) {
            continue;
        }

        $value = $data[$inputKey];
        if ($column === 'event_date' || $column === 'end_date') {
            $value = $normalizeDate($value);
        } elseif ($column === 'event_time' || $column === 'end_time') {
            $value = $normalizeTime($value);
        } elseif ($column === 'registration_deadline') {
            $value = $normalizeDateTime($value);
        } elseif ($column === 'cover_image' || $column === 'title' || $column === 'description' || $column === 'location' || $column === 'meeting_link') {
            $value = is_string($value) ? $nullIfEmpty($value) : null;
        }

        if ($column === 'max_attendees' || $column === 'points_reward') {
            $value = ($value === '' || $value === null) ? null : (int)$value;
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

    if (array_key_exists('event_type', $data)) {
        $updates[] = 'event_type = ?';
        $params[] = in_array($data['event_type'], $eventTypes, true) ? $data['event_type'] : 'other';
    }

    if (array_key_exists('venue_type', $data)) {
        $updates[] = 'venue_type = ?';
        $params[] = in_array($data['venue_type'], $venueTypes, true) ? $data['venue_type'] : 'physical';
    }

    if (array_key_exists('status', $data)) {
        $updates[] = 'status = ?';
        $params[] = in_array($data['status'], $statuses, true) ? $data['status'] : 'draft';
    }

    if (array_key_exists('target_type', $data)) {
        $updates[] = 'target_type = ?';
        $params[] = in_array($data['target_type'], $targetTypes, true) ? $data['target_type'] : 'all';
    }

    if (array_key_exists('target_id', $data)) {
        $updates[] = 'target_id = ?';
        $params[] = $data['target_id'] !== '' ? (int)$data['target_id'] : null;
    }

    if (array_key_exists('target_batch_year', $data)) {
        $updates[] = 'target_batch_year = ?';
        $params[] = $data['target_batch_year'] !== '' ? (int)$data['target_batch_year'] : null;
    } elseif (!empty($data['target_batch_years'][0])) {
        $updates[] = 'target_batch_year = ?';
        $params[] = (int)$data['target_batch_years'][0];
    }

    if (empty($updates)) {
        echo json_encode(['success' => true, 'message' => 'No changes']);
        exit;
    }

    $updates[] = 'updated_at = NOW()';
    $params[] = $eventId;

    $db->beginTransaction();

    $stmt = $db->prepare("UPDATE events SET " . implode(', ', $updates) . " WHERE id = ?");
    $stmt->execute($params);

    if ($campusTouched) {
        $db->prepare('DELETE FROM event_campuses WHERE event_id = ?')->execute([$eventId]);
        if (!empty($campusIds)) {
            $junction = $db->prepare('INSERT INTO event_campuses (event_id, campus_id, created_at) VALUES (?, ?, NOW())');
            foreach ($campusIds as $campusId) {
                $junction->execute([$eventId, $campusId]);
            }
        }
    }

    syncEventStatuses($db);

    if ($coverImageTouched && $newCoverImage !== $oldCoverImage && isLocalUploadPath($oldCoverImage)) {
        deleteLocalImage($oldCoverImage);
    }

    $db->commit();

    logAdminActivity((int)$admin['id'], 'update', 'Updated event #' . $eventId, 'event', (int)$eventId);

    echo json_encode(['success' => true, 'message' => 'Event updated']);
} catch (PDOException $e) {
    if (isset($db) && $db instanceof PDO && $db->inTransaction()) {
        $db->rollBack();
    }

    if ($uploadedCoverImage !== null && isLocalUploadPath($uploadedCoverImage)) {
        deleteLocalImage($uploadedCoverImage);
    }

    error_log("Admin Update Event Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
