<?php
/**
 * Campus Management API
 * GET /api/admin/campuses
 * POST /api/admin/campuses
 * PUT /api/admin/campuses/{id}
 * DELETE /api/admin/campuses/{id}
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../utils/helpers.php';
require_once __DIR__ . '/../../middleware/auth.php';

requireAdmin();

try {
    $db = Database::getInstance()->getConnection();
    $method = $_SERVER['REQUEST_METHOD'];
    $pathInfo = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $pathParts = array_filter(explode('/', $pathInfo));
    $resourceId = end($pathParts);
    $isNumeric = is_numeric($resourceId);

    if ($method === 'GET') {
        if ($isNumeric) {
            // Get single campus
            $stmt = $db->prepare('
                SELECT c.id, c.name, c.code, c.description, c.location, c.status, c.created_at, c.updated_at,
                       c.assigned_admin_id, u.name AS assigned_admin_name
                FROM campuses c
                LEFT JOIN users u ON c.assigned_admin_id = u.id
                WHERE c.id = ?
                LIMIT 1
            ');
            $stmt->execute([$resourceId]);
            $campus = $stmt->fetch();
            
            if (!$campus) {
                notFound('Campus not found');
            }
            
            success($campus);
        } else {
            // Get all campuses
            $search = trim($_GET['search'] ?? '');
            $status = $_GET['status'] ?? null;
            
            $where = ['1=1'];
            $params = [];
            
            if ($search !== '') {
                $where[] = '(name LIKE ? OR code LIKE ? OR description LIKE ?)';
                $searchTerm = '%' . $search . '%';
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }
            
            if ($status) {
                $where[] = 'status = ?';
                $params[] = $status;
            }
            
            $sql = "
                SELECT c.id, c.name, c.code, c.description, c.location, c.status, c.created_at, c.updated_at,
                       c.assigned_admin_id, u.name AS assigned_admin_name
                FROM campuses c
                LEFT JOIN users u ON c.assigned_admin_id = u.id
                WHERE " . implode(' AND ', $where) . "
                ORDER BY c.created_at DESC
            ";
            
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            success($stmt->fetchAll());
        }
    }

    if ($method === 'POST') {
        $data = getRequestBody();
        
        $name = trim($data['name'] ?? '');
        $code = trim($data['code'] ?? '');
        $description = trim($data['description'] ?? '');
        $location = trim($data['location'] ?? '');
        $status = $data['status'] ?? 'active';
        $assignedAdminId = !empty($data['assigned_admin_id']) ? (int)$data['assigned_admin_id'] : null;
        
        // Validate required fields
        $errors = validateRequired(['name' => $name, 'code' => $code], ['name', 'code']);
        
        // Validate name minimum length (3 characters)
        if ($name !== '' && strlen($name) < 3) {
            $errors['name'] = 'Campus name must be at least 3 characters';
        }
        
        // Validate code is alphanumeric
        if ($code !== '' && !ctype_alnum($code)) {
            $errors['code'] = 'Campus code must be alphanumeric';
        }
        
        if (!empty($errors)) {
            validationError($errors);
        }
        
        if (!in_array($status, ['active', 'inactive'])) {
            validationError(['status' => 'Invalid status']);
        }

        if ($assignedAdminId) {
            $adminCheck = $db->prepare('SELECT id FROM users WHERE id = ? AND role IN (\'admin\', \'campus_admin\', \'staff\', \'system_admin\') LIMIT 1');
            $adminCheck->execute([$assignedAdminId]);
            if (!$adminCheck->fetch()) {
                validationError(['assigned_admin_id' => 'Invalid admin user']);
            }
        }
        
        // Check for duplicate code
        $existing = $db->prepare('SELECT id FROM campuses WHERE code = ? LIMIT 1');
        $existing->execute([$code]);
        if ($existing->fetch()) {
            validationError(['code' => 'Campus code already exists']);
        }
        
        $stmt = $db->prepare('
            INSERT INTO campuses (name, code, description, location, status, assigned_admin_id, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())
        ');
        $stmt->execute([$name, $code, $description, $location, $status, $assignedAdminId]);
        
        $campusId = (int) $db->lastInsertId();
        
        // Log admin activity
        $currentUser = getCurrentUser();
        if ($currentUser) {
            logAdminActivity(
                $currentUser['id'],
                'campus_create',
                "Created campus: {$name} ({$code})",
                'campus',
                $campusId
            );
        }
        
        success([
            'id' => $campusId,
            'name' => $name,
            'code' => $code,
            'description' => $description,
            'location' => $location,
            'status' => $status,
            'assigned_admin_id' => $assignedAdminId
        ], 'Campus created successfully', 201);
    }

    if ($method === 'PUT') {
        if (!$isNumeric) {
            error('Campus ID required', 400);
        }
        
        $stmt = $db->prepare('SELECT id, name, code FROM campuses WHERE id = ? LIMIT 1');
        $stmt->execute([$resourceId]);
        $existingCampus = $stmt->fetch();
        if (!$existingCampus) {
            notFound('Campus not found');
        }
        
        $data = getRequestBody();
        
        $name = isset($data['name']) ? trim($data['name']) : null;
        $code = isset($data['code']) ? trim($data['code']) : null;
        $description = isset($data['description']) ? trim($data['description']) : null;
        $location = isset($data['location']) ? trim($data['location']) : null;
        $status = $data['status'] ?? null;
        $assignedAdminId = array_key_exists('assigned_admin_id', $data) ? (!empty($data['assigned_admin_id']) ? (int)$data['assigned_admin_id'] : null) : null;
        
        $errors = [];
        $updates = [];
        $params = [];
        
        if ($name !== null && $name !== '') {
            // Validate name minimum length (3 characters)
            if (strlen($name) < 3) {
                $errors['name'] = 'Campus name must be at least 3 characters';
            } else {
                $updates[] = 'name = ?';
                $params[] = $name;
            }
        }
        
        if ($code !== null && $code !== '') {
            // Validate code is alphanumeric
            if (!ctype_alnum($code)) {
                $errors['code'] = 'Campus code must be alphanumeric';
            } else {
                // Check for duplicate code
                $existing = $db->prepare('SELECT id FROM campuses WHERE code = ? AND id != ? LIMIT 1');
                $existing->execute([$code, $resourceId]);
                if ($existing->fetch()) {
                    $errors['code'] = 'Campus code already exists';
                } else {
                    $updates[] = 'code = ?';
                    $params[] = $code;
                }
            }
        }
        
        if (!empty($errors)) {
            validationError($errors);
        }
        
        if ($description !== null) {
            $updates[] = 'description = ?';
            $params[] = $description;
        }
        
        if ($location !== null) {
            $updates[] = 'location = ?';
            $params[] = $location;
        }
        
        if ($status !== null) {
            if (!in_array($status, ['active', 'inactive'])) {
                validationError(['status' => 'Invalid status']);
            }
            $updates[] = 'status = ?';
            $params[] = $status;
        }

        if ($assignedAdminId !== null) {
            $adminCheck = $db->prepare('SELECT id FROM users WHERE id = ? AND role IN (\'admin\', \'campus_admin\', \'staff\', \'system_admin\') LIMIT 1');
            $adminCheck->execute([$assignedAdminId]);
            if (!$adminCheck->fetch()) {
                validationError(['assigned_admin_id' => 'Invalid admin user']);
            }
            $updates[] = 'assigned_admin_id = ?';
            $params[] = $assignedAdminId;
        }
        
        if (empty($updates)) {
            error('No fields to update', 400);
        }
        
        $updates[] = 'updated_at = NOW()';
        $params[] = $resourceId;
        
        $sql = 'UPDATE campuses SET ' . implode(', ', $updates) . ' WHERE id = ?';
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        
        // Log admin activity
        $currentUser = getCurrentUser();
        if ($currentUser) {
            $oldName = $existingCampus['name'];
            $oldCode = $existingCampus['code'];
            $newName = $name ?? $oldName;
            $newCode = $code ?? $oldCode;
            
            logAdminActivity(
                $currentUser['id'],
                'campus_update',
                "Updated campus: {$oldName} ({$oldCode}) to {$newName} ({$newCode})",
                'campus',
                $resourceId
            );
        }
        
        success([], 'Campus updated successfully');
    }

    if ($method === 'DELETE') {
        if (!$isNumeric) {
            error('Campus ID required', 400);
        }
        
        $stmt = $db->prepare('SELECT id, name, code FROM campuses WHERE id = ? LIMIT 1');
        $stmt->execute([$resourceId]);
        $campus = $stmt->fetch();
        if (!$campus) {
            notFound('Campus not found');
        }
        
        // Check if campus is in use
        $check = $db->prepare('
            SELECT COUNT(*) as cnt FROM users WHERE campus_id = ? LIMIT 1
        ');
        $check->execute([$resourceId]);
        $result = $check->fetch();
        if ($result['cnt'] > 0) {
            error('Cannot delete campus with assigned users', 409);
        }
        
        $stmt = $db->prepare('DELETE FROM campuses WHERE id = ?');
        $stmt->execute([$resourceId]);
        
        // Log admin activity
        $currentUser = getCurrentUser();
        if ($currentUser) {
            logAdminActivity(
                $currentUser['id'],
                'campus_delete',
                "Deleted campus: {$campus['name']} ({$campus['code']})",
                'campus',
                $resourceId
            );
        }
        
        success([], 'Campus deleted successfully');
    }

    error('Method not allowed', 405);
} catch (Exception $e) {
    error('Campus operation failed: ' . $e->getMessage(), 500);
}
