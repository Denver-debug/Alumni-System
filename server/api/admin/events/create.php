<?php
/**
 * Admin Create Event API
 * POST /api/admin/events
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

$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
$isMultipart = stripos($contentType, 'multipart/form-data') !== false;
$data = $isMultipart ? $_POST : (json_decode(file_get_contents('php://input'), true) ?? []);

$uploadedCoverImage = null;
$imageFile = $_FILES['event_image'] ?? ($_FILES['image'] ?? null);

if ($imageFile && (int)($imageFile['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
    try {
        $uploadedCoverImage = uploadEventImage($imageFile)['url'];
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

$title = $nullIfEmpty($data['title'] ?? null);
$eventDate = $normalizeDate($data['event_date'] ?? null);

if ($title === null || $eventDate === null) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Title and event date are required']);
    exit;
}

try {
    $db = Database::getInstance()->getConnection();

    $eventTypes = ['seminar', 'reunion', 'workshop', 'webinar', 'networking', 'career_fair', 'sports', 'cultural', 'community_service', 'other'];
    $venueTypes = ['physical', 'online', 'hybrid'];
    $statuses = ['draft', 'upcoming', 'ongoing', 'completed', 'cancelled'];
    $targetTypes = ['all', 'college', 'program', 'section', 'batch_year'];

    $rawEventType = $data['event_type'] ?? 'other';
    $rawVenueType = $data['venue_type'] ?? 'physical';
    $rawStatus = $data['status'] ?? 'draft';
    $rawTargetType = $data['target_type'] ?? 'all';

    $eventType = in_array($rawEventType, $eventTypes, true) ? $rawEventType : 'other';
    $venueType = in_array($rawVenueType, $venueTypes, true) ? $rawVenueType : 'physical';
    $status = in_array($rawStatus, $statuses, true) ? $rawStatus : 'draft';
    $targetType = in_array($rawTargetType, $targetTypes, true) ? $rawTargetType : 'all';

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

    $attendanceCode = strtoupper(trim((string)($data['attendance_code'] ?? '')));
    if ($attendanceCode === '') {
        $attendanceCode = strtoupper(substr(md5(uniqid('', true)), 0, 6));
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

    $primaryCampusId = $campusIds[0] ?? null;

    $description = $nullIfEmpty($data['description'] ?? null);
    $eventTime = $normalizeTime($data['event_time'] ?? null);
    $endDate = $normalizeDate($data['end_date'] ?? null);
    $endTime = $normalizeTime($data['end_time'] ?? null);
    if ($status !== 'cancelled') {
        $status = eventStatusFromDates([
            'event_date' => $eventDate,
            'end_date' => $endDate,
            'status' => $status,
        ]);
    }
    $location = $nullIfEmpty($data['location'] ?? null);
    $meetingLink = $nullIfEmpty($data['meeting_link'] ?? ($data['online_link'] ?? ($data['virtual_link'] ?? null)));
    $registrationDeadline = $normalizeDateTime($data['registration_deadline'] ?? null);

    $coverImageSource = $uploadedCoverImage ?? ($data['cover_image'] ?? ($data['image_url'] ?? null));
    $coverImage = is_string($coverImageSource) ? $nullIfEmpty($coverImageSource) : null;

    $db->beginTransaction();

    $stmt = $db->prepare(
        "INSERT INTO events (
            title, description, event_date, event_time, end_date, end_time, location,
            venue_type, meeting_link, event_type, campus_id,
            target_type, target_id, target_batch_year,
            max_attendees, registration_deadline, attendance_code,
            points_reward, cover_image, status, created_by, created_at
        ) VALUES (
            :title, :description, :event_date, :event_time, :end_date, :end_time, :location,
            :venue_type, :meeting_link, :event_type, :campus_id,
            :target_type, :target_id, :target_batch_year,
            :max_attendees, :registration_deadline, :attendance_code,
            :points_reward, :cover_image, :status, :created_by, NOW()
        )"
    );

    $stmt->execute([
        'title' => $title,
        'description' => $description,
        'event_date' => $eventDate,
        'event_time' => $eventTime,
        'end_date' => $endDate,
        'end_time' => $endTime,
        'location' => $location,
        'venue_type' => $venueType,
        'meeting_link' => $meetingLink,
        'event_type' => $eventType,
        'campus_id' => $primaryCampusId,
        'target_type' => $targetType,
        'target_id' => ($targetId === '' || $targetId === null) ? null : (int)$targetId,
        'target_batch_year' => ($targetBatchYear === '' || $targetBatchYear === null) ? null : (int)$targetBatchYear,
        'max_attendees' => isset($data['max_attendees']) && $data['max_attendees'] !== '' ? (int)$data['max_attendees'] : null,
        'registration_deadline' => $registrationDeadline,
        'attendance_code' => $attendanceCode,
        'points_reward' => isset($data['points_reward']) ? (int)$data['points_reward'] : 0,
        'cover_image' => $coverImage,
        'status' => $status,
        'created_by' => (int)$admin['id'],
    ]);

    $eventId = (int)$db->lastInsertId();

    if (!empty($campusIds)) {
        $junction = $db->prepare('INSERT INTO event_campuses (event_id, campus_id, created_at) VALUES (?, ?, NOW())');
        foreach ($campusIds as $campusId) {
            $junction->execute([$eventId, $campusId]);
        }
    }

    $db->commit();

    logAdminActivity((int)$admin['id'], 'create', 'Created event: ' . $title, 'event', $eventId);

    echo json_encode([
        'success' => true,
        'message' => 'Event created successfully',
        'data' => [
            'event_id' => $eventId,
            'attendance_code' => $attendanceCode,
        ],
    ]);
} catch (PDOException $e) {
    if (isset($db) && $db instanceof PDO && $db->inTransaction()) {
        $db->rollBack();
    }

    if ($uploadedCoverImage !== null && isLocalUploadPath($uploadedCoverImage)) {
        deleteLocalImage($uploadedCoverImage);
    }

    error_log('Admin Create Event - PDOException: ' . $e->getMessage());
    error_log('Admin Create Event - Error Code: ' . $e->getCode());
    error_log('Admin Create Event - Stack Trace: ' . $e->getTraceAsString());
    
    http_response_code(500);
    
    $debug = getenv('APP_DEBUG') === 'true';
    $errorMessage = $debug ? $e->getMessage() : 'Database error';
    
    echo json_encode([
        'success' => false, 
        'message' => $errorMessage,
        'error_code' => $e->getCode(),
        'debug' => $debug
    ]);
} catch (Exception $e) {
    if ($uploadedCoverImage !== null && isLocalUploadPath($uploadedCoverImage)) {
        deleteLocalImage($uploadedCoverImage);
    }

    error_log('Admin Create Event - General Exception: ' . $e->getMessage());
    error_log('Admin Create Event - Stack Trace: ' . $e->getTraceAsString());
    
    http_response_code(500);
    
    $debug = getenv('APP_DEBUG') === 'true';
    $errorMessage = $debug ? $e->getMessage() : 'Internal server error';
    
    echo json_encode([
        'success' => false,
        'message' => $errorMessage,
        'debug' => $debug
    ]);
}

