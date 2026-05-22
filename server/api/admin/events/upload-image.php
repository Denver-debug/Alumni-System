<?php
/**
 * Admin Event Image Upload API
 * POST /api/admin/events/upload-image
 * 
 * Handles event image uploads with validation and optimization
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../middleware/auth.php';
require_once __DIR__ . '/../../../utils/helpers.php';

header('Content-Type: application/json');

// Require admin authentication
$admin = requireAdmin();

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Check if image file was provided
if (!isset($_FILES['image'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'No image file provided']);
    exit;
}

try {
    $upload = uploadEventImage($_FILES['image']);

    echo json_encode([
        'success' => true,
        'message' => 'Image uploaded successfully',
        'data' => [
            'url' => $upload['url'],
            'filename' => $upload['filename'],
            'optimized' => $upload['optimized']
        ]
    ]);
    
} catch (RuntimeException $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Upload failed',
        'errors' => [$e->getMessage()]
    ]);
} catch (Exception $e) {
    // Log the error
    error_log("Event image upload error: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    
    // Return generic error to client
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error during upload'
    ]);
}
