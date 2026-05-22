<?php
/**
 * Main Router / API Entry Point
 * Alumni Management System
 */

// Error handling
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

$sendBootstrapJsonError = static function (int $statusCode, string $message, string $debug = ''): void {
    if (!headers_sent()) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
    }

    $response = [
        'success' => false,
        'message' => $message,
    ];

    if ((getenv('APP_DEBUG') === 'true' || getenv('APP_DEBUG') === '1') && $debug !== '') {
        $response['debug'] = $debug;
    }

    echo json_encode($response);
};

set_error_handler(static function ($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        return false;
    }

    throw new ErrorException($message, 0, $severity, $file, $line);
});

set_exception_handler(static function (Throwable $exception) use ($sendBootstrapJsonError): void {
    $sendBootstrapJsonError(500, 'Internal server error', $exception->getMessage());
    exit;
});

register_shutdown_function(static function () use ($sendBootstrapJsonError): void {
    $error = error_get_last();

    if (!$error) {
        return;
    }

    $fatalTypes = [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR];
    if (!in_array($error['type'], $fatalTypes, true)) {
        return;
    }

    $sendBootstrapJsonError(500, 'Internal server error', $error['message']);
});

// Set timezone
date_default_timezone_set('Asia/Manila');

// Start session for CSRF
session_start();

// Load configurations
try {
    require_once __DIR__ . '/config/database.php';
    require_once __DIR__ . '/config/auth.php';
    require_once __DIR__ . '/config/constants.php';
    require_once __DIR__ . '/utils/helpers.php';
    require_once __DIR__ . '/utils/validators.php';
    require_once __DIR__ . '/middleware/auth.php';
} catch (Throwable $e) {
    $sendBootstrapJsonError(500, 'Server bootstrap failed', $e->getMessage());
    exit;
}

// Apply CORS
cors();

// Security headers
securityHeaders();

// Get request info
$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = rtrim($uri, '/');

// Let the built-in PHP server serve static files directly (e.g., /uploads/*).
if (PHP_SAPI === 'cli-server') {
    $requestedFile = __DIR__ . ($uri === '' ? '/' : $uri);
    if (is_file($requestedFile)) {
        return false;
    }
}

// Normalize API mount points used by local, root-domain, and Hostinger deploys.
$basePaths = [
    '/client/server/api/' . API_VERSION,
    '/client/api/' . API_VERSION,
    '/server/api/' . API_VERSION,
    API_PREFIX,
    '/client/server/api',
    '/server/api',
];

foreach ($basePaths as $basePath) {
    if ($uri === $basePath || strpos($uri, $basePath . '/') === 0) {
        $uri = substr($uri, strlen($basePath));
        $uri = rtrim($uri, '/');
        $uri = $uri === '' ? '/' : $uri;
        break;
    }
}

// API Routes
$routes = [
    // Authentication Routes
    'POST /auth/register' => 'api/auth/register.php',
    'POST /auth/login' => 'api/auth/login.php',
    'POST /auth/google' => 'api/auth/google.php',
    'POST /auth/verify-email' => 'api/auth/verify-email.php',
    'POST /auth/resend-verification' => 'api/auth/resend-verification.php',
    'POST /auth/forgot-password' => 'api/auth/forgot-password.php',
    'POST /auth/reset-password' => 'api/auth/reset-password.php',
    'POST /auth/refresh' => 'api/auth/refresh.php',
    'GET /auth/profile' => 'api/auth/profile.php',
    'POST /auth/profile' => 'api/auth/update-profile.php',
    'PUT /auth/profile' => 'api/auth/update-profile.php',
    'POST /auth/change-password' => 'api/auth/change-password.php',
    
    // Alumni Routes
    'GET /alumni/dashboard' => 'api/alumni/dashboard.php',
    'GET /alumni/profile' => 'api/alumni/profile.php',
    'POST /alumni/profile' => 'api/alumni/update.php',
    'PUT /alumni/profile' => 'api/alumni/update.php',
    'GET /alumni/id-card' => 'api/alumni/id-card.php',
    'GET /alumni/search' => 'api/alumni/search.php',
    'GET /alumni/verification-status' => 'api/verification.php',
    'GET /alumni/notifications' => 'api/verification.php',
    'PUT /alumni/notifications/{id}/read' => 'api/verification.php',
    
    // Announcements (public)
    'GET /announcements' => 'api/announcements/list.php',
    'GET /announcements/{id}' => 'api/announcements/get.php',
    'POST /announcements' => 'api/admin/announcements/create.php',
    'PUT /announcements/{id}' => 'api/admin/announcements/update.php',
    'DELETE /announcements/{id}' => 'api/admin/announcements/delete.php',
    
    // Events Routes
    'GET /events' => 'api/events/list.php',
    'GET /events/my-stats' => 'api/events/my-stats.php',
    'GET /events/{id}' => 'api/events/get.php',
    'POST /events' => 'api/admin/events/create.php',
    'PUT /events/{id}' => 'api/admin/events/update.php',
    'DELETE /events/{id}' => 'api/admin/events/delete.php',
    'POST /events/{id}/register' => 'api/events/register.php',
    'POST /events/{id}/rsvp' => 'api/events/register.php',
    'POST /events/{id}/cancel-registration' => 'api/events/cancel-registration.php',
    'POST /events/{id}/checkin' => 'api/events/checkin.php',
    'POST /events/{id}/check-in' => 'api/events/checkin.php',
    'POST /events/checkin' => 'api/events/checkin.php',
    'POST /events/check-in' => 'api/events/checkin.php',
    
    // Gamification Routes
    'GET /gamification/points' => 'api/gamification/points.php',
    'GET /gamification/history' => 'api/gamification/history.php',
    'GET /gamification/leaderboard' => 'api/gamification/leaderboard.php',
    'GET /gamification/rewards' => 'api/gamification/rewards.php',
    'GET /gamification/redemptions' => 'api/gamification/redemptions.php',
    'POST /gamification/rewards/{id}/redeem' => 'api/gamification/redeem.php',
    'GET /rewards' => 'api/gamification/rewards.php',
    'POST /rewards/{id}/redeem' => 'api/gamification/redeem.php',
    
    // Organization Routes (public)
    'GET /colleges' => 'api/organization/colleges.php',
    'GET /programs' => 'api/organization/programs.php',
    'GET /sections' => 'api/organization/sections.php',
    
    // Form Fields (public)
    'GET /form-fields' => 'api/form-builder/list.php',
    
    // Messaging Routes
    'GET /messaging/conversations' => 'api/messaging.php',
    'POST /messaging/conversations' => 'api/messaging.php',
    'GET /messaging/conversations/{id}' => 'api/messaging.php',
    'GET /messaging/conversations/{id}/messages' => 'api/messaging.php',
    'POST /messaging/conversations/{id}/messages' => 'api/messaging.php',
    'PUT /messaging/conversations/{id}/read' => 'api/messaging.php',
    'GET /messaging/alumni/search' => 'api/messaging.php',
    'POST /messaging/conversations/{id}/read' => 'api/messaging.php',
    'GET /messaging/messages/{conversation_id}' => 'api/messaging.php',
    'POST /messaging/messages/{conversation_id}' => 'api/messaging.php',
    'POST /messaging/group' => 'api/messaging/group.php',
    'POST /messaging/calls' => 'api/messaging/calls.php',
    'GET /messaging/calls/incoming' => 'api/messaging/calls.php',
    'GET /messaging/calls/{id}/signals' => 'api/messaging/calls.php',
    'POST /messaging/calls/{id}/signals' => 'api/messaging/calls.php',
    'GET /messaging/calls/{id}' => 'api/messaging/calls.php',
    'PUT /messaging/calls/{id}/respond' => 'api/messaging/calls.php',
    'PUT /messaging/calls/{id}/end' => 'api/messaging/calls.php',
    
    // Alternative messaging routes (for compatibility)
    'GET /messages/conversations' => 'api/messaging.php',
    'POST /messages/conversations' => 'api/messaging.php',
    'GET /messages/conversations/{id}' => 'api/messaging.php',
    'GET /messages/conversations/{id}/messages' => 'api/messaging.php',
    'POST /messages/conversations/{id}/messages' => 'api/messaging.php',
    'PUT /messages/conversations/{id}/read' => 'api/messaging.php',
    'GET /messages/alumni/search' => 'api/messaging.php',
    'POST /messages/group' => 'api/messaging/group.php',
    'POST /messages/calls' => 'api/messaging/calls.php',
    'GET /messages/calls/incoming' => 'api/messaging/calls.php',
    'GET /messages/calls/{id}/signals' => 'api/messaging/calls.php',
    'POST /messages/calls/{id}/signals' => 'api/messaging/calls.php',
    'GET /messages/calls/{id}' => 'api/messaging/calls.php',
    'PUT /messages/calls/{id}/respond' => 'api/messaging/calls.php',
    'PUT /messages/calls/{id}/end' => 'api/messaging/calls.php',

    // Public Site Settings
    'GET /site/theme' => 'api/site/theme.php',
    'GET /site/content' => 'api/site/content.php',
    'GET /site/firebase-config' => 'api/site/firebase-config.php',

    // Legacy Settings Compatibility
    'GET /settings/theme' => 'api/admin/settings/theme.php',
    'PUT /settings/theme' => 'api/admin/settings/theme.php',
    'GET /settings/general' => 'api/settings/general.php',
    'PUT /settings/general' => 'api/settings/general.php',
    'GET /settings/system-info' => 'api/settings/system-info.php',
    'PUT /settings/email' => 'api/settings/email.php',
    'POST /settings/clear-cache' => 'api/settings/clear-cache.php',
    
    // ========== ADMIN ROUTES ==========
    
    // Admin Authentication
    'POST /admin/login' => 'api/admin/login.php',
    
    // Admin Dashboard
    'GET /admin/dashboard' => 'api/admin/dashboard.php',
    'GET /admin/activities' => 'api/admin/activities.php',

    // Admin Analytics
    'GET /admin/analytics/dashboard' => 'api/analytics.php',
    'GET /admin/analytics/alumni-distribution' => 'api/analytics.php',
    'GET /admin/analytics/engagement' => 'api/analytics.php',
    'GET /admin/analytics/export' => 'api/analytics.php',
    
    // Alumni Management
    'GET /admin/alumni' => 'api/admin/alumni/list.php',
    'GET /admin/alumni/export' => 'api/admin/alumni/export.php',
    'GET /admin/alumni/id-card' => 'api/admin/alumni/id-card.php',
    'GET /admin/alumni/pending' => 'api/verification.php',
    'GET /admin/alumni/verification-stats' => 'api/verification.php',
    'PUT /admin/alumni/{id}/verify' => 'api/verification.php',
    'PUT /admin/alumni/{id}/reject' => 'api/verification.php',
    'GET /admin/alumni/{id}' => 'api/admin/alumni/get.php',
    'PUT /admin/alumni/{id}' => 'api/admin/alumni/update.php',
    'DELETE /admin/alumni/{id}' => 'api/admin/alumni/delete.php',
    
    // Event Management
    'GET /admin/events' => 'api/admin/events/list.php',
    'POST /admin/events' => 'api/admin/events/create.php',
    'POST /admin/events/upload-image' => 'api/admin/events/upload-image.php',
    'POST /admin/events/scan-qr' => 'api/events/scan-qr.php',
    'POST /admin/events/{id}' => 'api/admin/events/update.php',
    'GET /admin/events/{id}' => 'api/admin/events/get.php',
    'PUT /admin/events/{id}' => 'api/admin/events/update.php',
    'DELETE /admin/events/{id}' => 'api/admin/events/delete.php',
    'GET /admin/events/{id}/codes' => 'api/admin/events/codes.php',
    'POST /admin/events/{id}/codes' => 'api/admin/events/codes.php',
    'DELETE /admin/events/{id}/codes' => 'api/admin/events/codes.php',
    'POST /admin/events/{id}/attendance' => 'api/admin/events/attendance.php',
    
    // Announcement Management
    'GET /admin/announcements' => 'api/admin/announcements/list.php',
    'POST /admin/announcements' => 'api/admin/announcements/create.php',
    'POST /admin/announcements/{id}' => 'api/admin/announcements/update.php',
    'GET /admin/announcements/{id}' => 'api/admin/announcements/get.php',
    'PUT /admin/announcements/{id}' => 'api/admin/announcements/update.php',
    'DELETE /admin/announcements/{id}' => 'api/admin/announcements/delete.php',
    
    // Organization Management
    'GET /admin/organization/colleges' => 'api/admin/organization/colleges.php',
    'POST /admin/organization/colleges' => 'api/admin/organization/colleges.php',
    'GET /admin/organization/colleges/{id}' => 'api/admin/organization/colleges.php',
    'PUT /admin/organization/colleges/{id}' => 'api/admin/organization/colleges.php',
    'DELETE /admin/organization/colleges/{id}' => 'api/admin/organization/colleges.php',
    'GET /admin/organization/programs' => 'api/admin/organization/programs.php',
    'POST /admin/organization/programs' => 'api/admin/organization/programs.php',
    'GET /admin/organization/programs/{id}' => 'api/admin/organization/programs.php',
    'PUT /admin/organization/programs/{id}' => 'api/admin/organization/programs.php',
    'DELETE /admin/organization/programs/{id}' => 'api/admin/organization/programs.php',
    'GET /admin/organization/program-campuses/{id}' => 'api/admin/organization/program-campuses.php',
    'POST /admin/organization/program-campuses/{id}' => 'api/admin/organization/program-campuses.php',
    'GET /admin/organization/program-campuses-by-campus/{id}' => 'api/admin/organization/program-campuses.php',
    'GET /admin/organization/sections' => 'api/admin/organization/sections.php',
    'POST /admin/organization/sections' => 'api/admin/organization/sections.php',
    'GET /admin/organization/sections/{id}' => 'api/admin/organization/sections.php',
    'PUT /admin/organization/sections/{id}' => 'api/admin/organization/sections.php',
    'DELETE /admin/organization/sections/{id}' => 'api/admin/organization/sections.php',
    
    // Form Builder
    'GET /admin/form-fields' => 'api/form-builder/list.php',
    'POST /admin/form-fields' => 'api/form-builder/manage.php',
    'PUT /admin/form-fields/{id}' => 'api/form-builder/manage.php',
    'DELETE /admin/form-fields/{id}' => 'api/form-builder/manage.php',
    
    // Gamification Management
    'GET /admin/gamification/points' => 'api/admin/gamification/points.php',
    'POST /admin/gamification/points/adjust' => 'api/admin/gamification/points.php',
    'GET /admin/gamification/rewards' => 'api/admin/gamification/rewards.php',
    'POST /admin/gamification/rewards' => 'api/admin/gamification/rewards.php',
    'GET /admin/gamification/rewards/{id}' => 'api/admin/gamification/rewards.php',
    'PUT /admin/gamification/rewards/{id}' => 'api/admin/gamification/rewards.php',
    'DELETE /admin/gamification/rewards/{id}' => 'api/admin/gamification/rewards.php',
    'GET /admin/gamification/redemptions' => 'api/admin/gamification/redemptions.php',
    'PUT /admin/gamification/redemptions/{id}' => 'api/admin/gamification/redemptions.php',

    // Security Settings
    'GET /admin/security/settings' => 'api/security-settings.php',
    'PUT /admin/security/settings' => 'api/security-settings.php',
    'GET /admin/security/locked-accounts' => 'api/security-settings.php',
    'PUT /admin/security/unlock/{id}' => 'api/security-settings.php',
    'GET /admin/security/login-attempts' => 'api/security-settings.php',
    'GET /admin/security/stats' => 'api/security-settings.php',
    
    // Settings
    'GET /admin/settings/theme' => 'api/admin/settings/theme.php',
    'PUT /admin/settings/theme' => 'api/admin/settings/theme.php',
    'POST /admin/settings/theme/logo-upload' => 'api/admin/settings/logo-upload.php',
    'POST /admin/settings/theme/background-upload' => 'api/admin/settings/background-upload.php',
    'GET /admin/settings/site' => 'api/admin/settings/site.php',
    'PUT /admin/settings/site' => 'api/admin/settings/site.php',
    'POST /admin/settings/site' => 'api/admin/settings/site.php',
    'GET /admin/settings/email' => 'api/admin/settings/email.php',
    'PUT /admin/settings/email' => 'api/admin/settings/email.php',
    'POST /admin/settings/test-email' => 'api/admin/settings/test-email.php',

    // User Management
    'GET /admin/users' => 'api/admin/users/list.php',
    'POST /admin/users' => 'api/admin/users/list.php',
    'PUT /admin/users/{id}' => 'api/admin/users/manage.php',
    'DELETE /admin/users/{id}' => 'api/admin/users/manage.php',
    'POST /admin/users/{id}/reset-password' => 'api/admin/users/reset-password.php',

    // Campus Management
    'GET /admin/campuses' => 'api/admin/campuses.php',
    'POST /admin/campuses' => 'api/admin/campuses.php',
    'GET /admin/campuses/{id}' => 'api/admin/campuses.php',
    'PUT /admin/campuses/{id}' => 'api/admin/campuses.php',
    'DELETE /admin/campuses/{id}' => 'api/admin/campuses.php',
    'GET /campuses' => 'api/campuses/list.php',
    'GET /campuses/list' => 'api/campuses/list.php',
];

// Find matching route
$matched = false;
$params = [];

foreach ($routes as $route => $handler) {
    list($routeMethod, $routePath) = explode(' ', $route, 2);
    
    if ($method !== $routeMethod) {
        continue;
    }
    
    // Convert route pattern to regex
    $pattern = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $routePath);
    $pattern = '#^' . $pattern . '$#';
    
    if (preg_match($pattern, $uri, $matches)) {
        $matched = true;
        
        // Extract route parameters
        foreach ($matches as $key => $value) {
            if (is_string($key)) {
                $params[$key] = $value;
            }
        }
        
        // Set route params globally
        $_REQUEST['_route_params'] = $params;
        $GLOBALS['url_params'] = $params;
        
        // Include handler
        $handlerPath = __DIR__ . '/' . $handler;
        
        if (file_exists($handlerPath)) {
            require $handlerPath;
        } else {
            serverError('Handler not found: ' . $handler);
        }
        
        break;
    }
}

// 404 if no route matched
if (!$matched) {
    if ($uri === '' || $uri === '/') {
        success([
            'name' => APP_NAME,
            'version' => APP_VERSION,
            'status' => 'running'
        ], 'API is running');
    } else {
        notFound('Endpoint not found');
    }
}
