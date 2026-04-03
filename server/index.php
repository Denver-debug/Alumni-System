<?php
/**
 * Main Router / API Entry Point
 * Alumni Management System
 */

// Error handling
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Set timezone
date_default_timezone_set('Asia/Manila');

// Start session for CSRF
session_start();

// Load configurations
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/utils/helpers.php';
require_once __DIR__ . '/utils/validators.php';
require_once __DIR__ . '/middleware/auth.php';

// Apply CORS
cors();

// Security headers
securityHeaders();

// Get request info
$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = rtrim($uri, '/');

// Remove base path if exists
$basePath = '/api/v1';
if (strpos($uri, $basePath) === 0) {
    $uri = substr($uri, strlen($basePath));
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
    'GET /auth/profile' => 'api/auth/profile.php',
    'PUT /auth/profile' => 'api/auth/update-profile.php',
    'POST /auth/change-password' => 'api/auth/change-password.php',
    
    // Alumni Routes
    'GET /alumni/profile' => 'api/alumni/profile.php',
    'PUT /alumni/profile' => 'api/alumni/update.php',
    'GET /alumni/search' => 'api/alumni/search.php',
    
    // Announcements (public)
    'GET /announcements' => 'api/announcements/list.php',
    
    // Events Routes
    'GET /events' => 'api/events/list.php',
    'GET /events/{id}' => 'api/events/get.php',
    'POST /events/{id}/register' => 'api/events/register.php',
    'POST /events/{id}/checkin' => 'api/events/checkin.php',
    
    // Gamification Routes
    'GET /gamification/points' => 'api/gamification/points.php',
    'GET /gamification/history' => 'api/gamification/history.php',
    'GET /gamification/leaderboard' => 'api/gamification/leaderboard.php',
    'GET /rewards' => 'api/gamification/rewards.php',
    'POST /rewards/{id}/redeem' => 'api/gamification/redeem.php',
    
    // Organization Routes (public)
    'GET /colleges' => 'api/organization/colleges.php',
    'GET /programs' => 'api/organization/programs.php',
    'GET /sections' => 'api/organization/sections.php',
    
    // Form Fields (public)
    'GET /form-fields' => 'api/form-builder/list.php',
    
    // Messaging Routes
    'GET /messaging/conversations' => 'api/messaging/conversations.php',
    'POST /messaging/conversations' => 'api/messaging/conversations.php',
    'GET /messaging/messages/{conversation_id}' => 'api/messaging/messages.php',
    'POST /messaging/messages/{conversation_id}' => 'api/messaging/messages.php',
    'POST /messaging/group' => 'api/messaging/group.php',
    
    // ========== ADMIN ROUTES ==========
    
    // Admin Authentication
    'POST /admin/login' => 'api/admin/login.php',
    
    // Admin Dashboard
    'GET /admin/dashboard' => 'api/admin/dashboard.php',
    'GET /admin/activities' => 'api/admin/activities.php',
    
    // Alumni Management
    'GET /admin/alumni' => 'api/admin/alumni/list.php',
    'GET /admin/alumni/export' => 'api/admin/alumni/export.php',
    'GET /admin/alumni/{id}' => 'api/admin/alumni/get.php',
    'PUT /admin/alumni/{id}' => 'api/admin/alumni/update.php',
    'DELETE /admin/alumni/{id}' => 'api/admin/alumni/delete.php',
    
    // Event Management
    'GET /admin/events' => 'api/admin/events/list.php',
    'POST /admin/events' => 'api/admin/events/create.php',
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
    
    // Settings
    'GET /admin/settings/theme' => 'api/admin/settings/theme.php',
    'PUT /admin/settings/theme' => 'api/admin/settings/theme.php',
    'GET /admin/settings/site' => 'api/admin/settings/site.php',
    'PUT /admin/settings/site' => 'api/admin/settings/site.php',
    'POST /admin/settings/site' => 'api/admin/settings/site.php',
    'GET /admin/settings/email' => 'api/admin/settings/email.php',
    'PUT /admin/settings/email' => 'api/admin/settings/email.php',
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
