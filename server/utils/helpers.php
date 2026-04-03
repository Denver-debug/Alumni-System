<?php
/**
 * API Response Helpers
 * Alumni Management System
 */

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
