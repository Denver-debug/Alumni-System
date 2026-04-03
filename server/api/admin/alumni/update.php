<?php
/**
 * Admin Update Alumni API
 * PUT /api/admin/alumni/{id} - Update alumni info
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../middleware/auth.php';
require_once __DIR__ . '/../../../config/auth.php';

header('Content-Type: application/json');

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$userId = $GLOBALS['url_params']['id'] ?? null;

if (!$userId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Alumni ID required']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

try {
    $db = Database::getInstance()->getConnection();
    
    $stmt = $db->prepare("SELECT id FROM users WHERE id = ? AND role = 'alumni'");
    $stmt->execute([$userId]);
    if (!$stmt->fetch()) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Alumni not found']);
        exit;
    }
    
    $db->beginTransaction();
    
    // Update user table
    $userUpdates = [];
    $userParams = [];
    
    if (isset($data['name'])) {
        $userUpdates[] = "name = ?";
        $userParams[] = trim($data['name']);
    }
    
    if (isset($data['status']) && in_array($data['status'], ['active', 'inactive', 'blocked'])) {
        $userUpdates[] = "status = ?";
        $userParams[] = $data['status'];
    }
    
    if (isset($data['email_verified'])) {
        $userUpdates[] = "email_verified = ?";
        $userParams[] = $data['email_verified'] ? 1 : 0;
    }
    
    if (!empty($userUpdates)) {
        $userUpdates[] = "updated_at = NOW()";
        $userParams[] = $userId;
        $stmt = $db->prepare("UPDATE users SET " . implode(', ', $userUpdates) . " WHERE id = ?");
        $stmt->execute($userParams);
    }
    
    // Update profile
    $profileUpdates = [];
    $profileParams = [];
    
    $profileFields = ['college_id', 'program_id', 'section_id', 'batch_year', 'graduation_year',
                      'phone', 'address', 'city', 'state', 'country', 'postal_code',
                      'company', 'job_title', 'industry', 'bio'];
    
    foreach ($profileFields as $field) {
        if (isset($data[$field])) {
            $profileUpdates[] = "$field = ?";
            $profileParams[] = $data[$field] ?: null;
        }
    }
    
    if (isset($data['adjust_points']) && is_numeric($data['adjust_points'])) {
        $pointsAdjust = intval($data['adjust_points']);
        if ($pointsAdjust !== 0) {
            $profileUpdates[] = "total_points = total_points + ?";
            $profileParams[] = $pointsAdjust;
            
            $admin = getCurrentUser();
            $stmt = $db->prepare("INSERT INTO point_transactions (user_id, points, type, description, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$userId, abs($pointsAdjust), $pointsAdjust > 0 ? 'earned' : 'redeemed', "Admin adjustment by " . $admin['name']]);
        }
    }
    
    if (!empty($profileUpdates)) {
        $profileUpdates[] = "updated_at = NOW()";
        $profileParams[] = $userId;
        $stmt = $db->prepare("UPDATE alumni_profiles SET " . implode(', ', $profileUpdates) . " WHERE user_id = ?");
        $stmt->execute($profileParams);
    }
    
    $db->commit();
    
    $admin = getCurrentUser();
    $stmt = $db->prepare("INSERT INTO admin_activities (admin_id, action, target_type, target_id, details, ip_address, created_at) VALUES (?, 'update', 'alumni', ?, ?, ?, NOW())");
    $stmt->execute([$admin['id'], $userId, json_encode(['fields_updated' => array_keys($data)]), $_SERVER['REMOTE_ADDR'] ?? null]);
    
    echo json_encode(['success' => true, 'message' => 'Alumni updated successfully']);
    
} catch (PDOException $e) {
    if (isset($db) && $db->inTransaction()) $db->rollBack();
    error_log("Admin Update Alumni Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
