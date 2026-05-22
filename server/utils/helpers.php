<?php
/**
 * API Response Helpers
 * Alumni Management System
 */

if (!defined('UPLOAD_DIR')) {
    require_once __DIR__ . '/../config/constants.php';
}

/**
 * Send JSON response
 */
function jsonResponse(array $data, int $statusCode = 200): void {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Success response
 */
function success(array $data = [], string $message = 'Success', int $statusCode = 200): void {
    jsonResponse([
        'success' => true,
        'message' => $message,
        'data' => $data
    ], $statusCode);
}

/**
 * Backward-compatible success helper used by older endpoints.
 */
function respondSuccess($data = [], int $statusCode = 200, string $message = 'Success'): void {
    if (!is_array($data)) {
        $data = ['value' => $data];
    }

    success($data, $message, $statusCode);
}

/**
 * Error response
 */
function error(string $message, int $statusCode = 400, array $errors = []): void {
    $response = [
        'success' => false,
        'message' => $message
    ];
    
    if (!empty($errors)) {
        $response['errors'] = $errors;
    }
    
    jsonResponse($response, $statusCode);
}

/**
 * Backward-compatible error helper used by older endpoints.
 */
function respondError(string $message, int $statusCode = 400, array $errors = []): void {
    error($message, $statusCode, $errors);
}

/**
 * Validation error response
 */
function validationError(array $errors): void {
    error('Validation failed', 422, $errors);
}

/**
 * Unauthorized response
 */
function unauthorized(string $message = 'Unauthorized'): void {
    error($message, 401);
}

/**
 * Forbidden response
 */
function forbidden(string $message = 'Forbidden'): void {
    error($message, 403);
}

/**
 * Not found response
 */
function notFound(string $message = 'Resource not found'): void {
    error($message, 404);
}

/**
 * Server error response
 */
function serverError(string $message = 'Internal server error'): void {
    error($message, 500);
}

/**
 * Get request body as array
 */
function getRequestBody(): array {
    $body = file_get_contents('php://input');
    return json_decode($body, true) ?? [];
}

/**
 * Get query parameter
 */
function getQuery(string $key, $default = null) {
    return $_GET[$key] ?? $default;
}

/**
 * Get multiple query parameters
 */
function getQueries(array $keys): array {
    $result = [];
    foreach ($keys as $key => $default) {
        if (is_int($key)) {
            $key = $default;
            $default = null;
        }
        $result[$key] = $_GET[$key] ?? $default;
    }
    return $result;
}

/**
 * Validate required fields
 */
function validateRequired(array $data, array $fields): array {
    $errors = [];
    foreach ($fields as $field) {
        if (!isset($data[$field]) || (is_string($data[$field]) && trim($data[$field]) === '')) {
            $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
        }
    }
    return $errors;
}

/**
 * Validate email format
 */
function validateEmail(string $email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Sanitize string
 */
function sanitize(string $value): string {
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

/**
 * Sanitize array recursively
 */
function sanitizeArray(array $data): array {
    $sanitized = [];
    foreach ($data as $key => $value) {
        if (is_array($value)) {
            $sanitized[$key] = sanitizeArray($value);
        } elseif (is_string($value)) {
            $sanitized[$key] = sanitize($value);
        } else {
            $sanitized[$key] = $value;
        }
    }
    return $sanitized;
}

/**
 * Get pagination parameters
 */
function getPagination(): array {
    $page = max(1, (int) getQuery('page', 1));
    $limit = min(MAX_PAGE_SIZE, max(1, (int) getQuery('limit', DEFAULT_PAGE_SIZE)));
    $offset = ($page - 1) * $limit;
    
    return [
        'page' => $page,
        'limit' => $limit,
        'offset' => $offset
    ];
}

/**
 * Format pagination response
 */
function paginatedResponse(array $items, int $total, int $page, int $limit): void {
    success([
        'items' => $items,
        'pagination' => [
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'pages' => ceil($total / $limit)
        ]
    ]);
}

/**
 * Normalize campus/college codes for Alumni ID use.
 */
function normalizeAlumniIdCode(?string $code, string $fallback): string {
    $normalized = strtoupper(preg_replace('/[^A-Z0-9]/', '', trim((string)$code)) ?? '');
    $fallback = strtoupper(preg_replace('/[^A-Z0-9]/', '', trim($fallback)) ?? '');

    $resolved = $normalized !== '' ? $normalized : ($fallback !== '' ? $fallback : 'GEN');

    return substr($resolved, 0, 8);
}

/**
 * Resolve the static Alumni ID prefix for a campus/year/college profile.
 * Format: CAMPUS-GRADUATIONYEAR-COLLEGECODE
 */
function getAlumniIdProfilePrefix($db, int $campusId, int $collegeId, int $graduationYear): ?string {
    $pdo = $db instanceof Database ? $db->getConnection() : $db;

    if (!$pdo instanceof PDO || $campusId <= 0 || $collegeId <= 0 || $graduationYear < 1900) {
        return null;
    }

    try {
        $campusStmt = $pdo->prepare('SELECT code FROM campuses WHERE id = ? LIMIT 1');
        $campusStmt->execute([$campusId]);
        $campus = $campusStmt->fetch();
        if (!$campus) {
            return null;
        }

        $collegeStmt = $pdo->prepare('SELECT code FROM colleges WHERE id = ? LIMIT 1');
        $collegeStmt->execute([$collegeId]);
        $college = $collegeStmt->fetch();
        if (!$college) {
            return null;
        }

        $campusCode = normalizeAlumniIdCode($campus['code'] ?? '', 'CAMPUS');
        $collegeCode = normalizeAlumniIdCode($college['code'] ?? '', 'COLLEGE');

        return sprintf('%s-%d-%s', $campusCode, (int)$graduationYear, $collegeCode);
    } catch (Throwable $e) {
        error_log('Failed to resolve Alumni ID profile prefix: ' . $e->getMessage());
        return null;
    }
}

/**
 * Generate alumni ID in format: CAMPUS-GRADUATIONYEAR-COLLEGECODE-5_DIGIT_NUMBER
 * Example: BBC-2024-CCS-00001
 */
function generateAlumniId($db, int $campusId, int $collegeId, int $graduationYear): ?string {
    $pdo = $db instanceof Database ? $db->getConnection() : $db;

    if (!$pdo instanceof PDO) {
        return null;
    }

    $prefix = getAlumniIdProfilePrefix($pdo, $campusId, $collegeId, $graduationYear);
    if (!$prefix) {
        return null;
    }

    try {
        $sequenceLength = 5;
        $sequenceKey = preg_replace('/-\d{4}-/', '-', $prefix, 1);

        $stmt = $pdo->prepare(
            "INSERT INTO alumni_id_sequences (year, college_code, last_sequence)
             VALUES (?, ?, LAST_INSERT_ID(1))
             ON DUPLICATE KEY UPDATE last_sequence = LAST_INSERT_ID(last_sequence + 1)"
        );
        $stmt->execute([(int)$graduationYear, $sequenceKey]);

        $sequence = (int)$pdo->query('SELECT LAST_INSERT_ID()')->fetchColumn();
        if ($sequence < 1) {
            $sequence = 1;
        }

        return sprintf('%s-%0' . $sequenceLength . 'd', $prefix, $sequence);
    } catch (Throwable $e) {
        error_log('Failed to generate Alumni ID: ' . $e->getMessage());
        return null;
    }
}

/**
 * Generate UUID
 */
function generateUuid(): string {
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}

/**
 * Get client IP address
 */
function getClientIp(): string {
    $headers = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'];
    
    foreach ($headers as $header) {
        if (!empty($_SERVER[$header])) {
            $ips = explode(',', $_SERVER[$header]);
            return trim($ips[0]);
        }
    }
    
    return '0.0.0.0';
}

/**
 * Format date for display
 */
function formatDate(string $date, string $format = 'F j, Y'): string {
    return date($format, strtotime($date));
}

/**
 * Format datetime for display
 */
function formatDateTime(string $datetime, string $format = 'F j, Y g:i A'): string {
    return date($format, strtotime($datetime));
}

/**
 * Derive an event lifecycle status from its scheduled dates.
 */
function eventStatusFromDates(array $event, ?string $today = null): string {
    $currentStatus = strtolower((string)($event['status'] ?? ''));

    if ($currentStatus === 'cancelled') {
        return 'cancelled';
    }

    $startDate = substr(trim((string)($event['event_date'] ?? '')), 0, 10);
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate)) {
        return $currentStatus !== '' ? $currentStatus : 'upcoming';
    }

    $endDate = substr(trim((string)($event['end_date'] ?? '')), 0, 10);
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $endDate)) {
        $endDate = $startDate;
    }

    $today = $today ?: date('Y-m-d');

    if ($today < $startDate) {
        return 'upcoming';
    }

    if ($today > $endDate) {
        return 'completed';
    }

    return 'ongoing';
}

/**
 * Keep event statuses aligned with their dates whenever event APIs are read.
 */
function syncEventStatuses(PDO $db): void {
    try {
        $db->exec(
            "UPDATE events
             SET status = CASE
                 WHEN event_date > CURDATE() THEN 'upcoming'
                 WHEN COALESCE(end_date, event_date) < CURDATE() THEN 'completed'
                 ELSE 'ongoing'
             END
             WHERE status <> 'cancelled'
               AND event_date IS NOT NULL"
        );
    } catch (Throwable $e) {
        error_log('Failed to synchronize event statuses: ' . $e->getMessage());
    }
}

/**
 * Check if string is valid JSON
 */
function isJson(string $string): bool {
    json_decode($string);
    return json_last_error() === JSON_ERROR_NONE;
}

/**
 * Slug generator
 */
function slugify(string $text): string {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    return strtolower($text);
}

/**
 * Excerpt generator
 */
function excerpt(string $text, int $length = 150): string {
    $text = strip_tags($text);
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . '...';
}

/**
 * Check whether an image path points to an external HTTP(S) URL.
 */
function isExternalUrl(?string $path): bool {
    if ($path === null) {
        return false;
    }

    return (bool) preg_match('/^https?:\/\//i', trim($path));
}

/**
 * Check whether an image path is a locally stored upload path.
 */
function isLocalUploadPath(?string $path): bool {
    if ($path === null || isExternalUrl($path)) {
        return false;
    }

    $urlPath = parse_url(trim($path), PHP_URL_PATH);
    $urlPath = $urlPath !== false && $urlPath !== null ? $urlPath : trim($path);

    return str_starts_with($urlPath, '/uploads/');
}

/**
 * Convert a /uploads/... URL path into a safe filesystem path.
 */
function localUploadPathToFile(?string $path): ?string {
    if (!isLocalUploadPath($path)) {
        return null;
    }

    $uploadRoot = realpath(UPLOAD_DIR);
    if ($uploadRoot === false) {
        return null;
    }

    $urlPath = parse_url(trim((string) $path), PHP_URL_PATH);
    $urlPath = $urlPath !== false && $urlPath !== null ? $urlPath : trim((string) $path);
    $relativePath = ltrim(substr($urlPath, strlen('/uploads/')), '/\\');
    $relativePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relativePath);
    $candidate = $uploadRoot . DIRECTORY_SEPARATOR . $relativePath;
    $candidateDir = realpath(dirname($candidate));

    if ($candidateDir === false || !str_starts_with($candidateDir, $uploadRoot)) {
        return null;
    }

    return $candidate;
}

/**
 * Delete a locally uploaded image, leaving external URLs untouched.
 */
function deleteLocalImage(?string $path): bool {
    $filePath = localUploadPathToFile($path);

    if ($filePath === null || !is_file($filePath)) {
        return false;
    }

    return unlink($filePath);
}

/**
 * Resolve an image path into a browser-usable URL.
 */
function getImageUrl(?string $path): string {
    $path = trim((string) $path);

    if ($path === '') {
        return '';
    }

    if (isExternalUrl($path) || str_starts_with($path, 'data:')) {
        return $path;
    }

    if (str_starts_with($path, '/uploads/')) {
        return rtrim(APP_URL, '/') . $path;
    }

    return rtrim(APP_URL, '/') . '/' . ltrim($path, '/');
}

/**
 * Store and optimize an event image upload.
 */
function uploadEventImage(array $file): array {
    require_once __DIR__ . '/uploads.php';

    $uploader = new FileUploader('events', ALLOWED_IMAGE_TYPES, UPLOAD_MAX_SIZE);
    $filename = $uploader->upload($file);

    if (!$filename) {
        throw new RuntimeException(implode('; ', $uploader->getErrors()) ?: 'Image upload failed');
    }

    $filepath = UPLOAD_DIR . '/events/' . $filename;
    $optimized = ImageProcessor::resize($filepath, $filepath, 1920, 1080);

    return [
        'url' => $uploader->getUrl($filename),
        'filename' => $filename,
        'optimized' => $optimized,
    ];
}

/**
 * Store and optimize an announcement cover image upload.
 */
function uploadAnnouncementImage(array $file): array {
    require_once __DIR__ . '/uploads.php';

    $uploader = new FileUploader('announcements', ALLOWED_IMAGE_TYPES, UPLOAD_MAX_SIZE);
    $filename = $uploader->upload($file);

    if (!$filename) {
        throw new RuntimeException(implode('; ', $uploader->getErrors()) ?: 'Image upload failed');
    }

    $filepath = UPLOAD_DIR . '/announcements/' . $filename;
    $optimized = ImageProcessor::resize($filepath, $filepath, 1920, 1080);

    return [
        'url' => $uploader->getUrl($filename),
        'filename' => $filename,
        'optimized' => $optimized,
    ];
}


/**
 * Resolve profile image URL
 * Ensures profile images always have the correct path
 */
function resolveProfileImageUrl(?string $profileImage): ?string {
    if (empty($profileImage)) {
        return null;
    }
    
    // If already a full URL, return as is
    if (preg_match('/^https?:\/\//', $profileImage)) {
        return $profileImage;
    }
    
    // If starts with /uploads, return as is
    if (strpos($profileImage, '/uploads/') === 0) {
        return $profileImage;
    }
    
    // If it's just a filename or relative path, prepend /uploads/profiles/
    if (strpos($profileImage, '/') === false) {
        return '/uploads/profiles/' . $profileImage;
    }
    
    // If it starts with uploads (without leading slash), add the slash
    if (strpos($profileImage, 'uploads/') === 0) {
        return '/' . $profileImage;
    }
    
    return $profileImage;
}

/**
 * Process user data to ensure profile image URL is correct
 */
function processUserData(array $user): array {
    if (isset($user['profile_image'])) {
        $user['profile_image'] = resolveProfileImageUrl($user['profile_image']);
    }
    return $user;
}

/**
 * Process array of users to ensure profile image URLs are correct
 */
function processUsersData(array $users): array {
    return array_map('processUserData', $users);
}
