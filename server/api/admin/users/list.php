<?php
/**
 * Admin Users API
 * GET /api/admin/users
 * POST /api/admin/users
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../config/auth.php';
require_once __DIR__ . '/../../../utils/helpers.php';
require_once __DIR__ . '/../../../middleware/auth.php';

requireTopAdmin();

try {
    $db = Database::getInstance()->getConnection();
    $method = $_SERVER['REQUEST_METHOD'];

    if ($method === 'GET') {
        $role = $_GET['role'] ?? null;
        $search = trim($_GET['search'] ?? '');
        $campusId = isset($_GET['campus_id']) && $_GET['campus_id'] !== '' ? (int) $_GET['campus_id'] : null;

        $where = ['1=1'];
        $params = [];

        if ($role === 'admin') {
            $where[] = "u.role IN ('admin', 'system_admin', 'campus_admin', 'staff')";
        } elseif ($role) {
            $where[] = 'u.role = ?';
            $params[] = $role;
        }

        if ($search !== '') {
            $where[] = '(u.name LIKE ? OR u.email LIKE ? OR u.alumni_id LIKE ?)';
            $searchTerm = '%' . $search . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        if ($campusId) {
            $where[] = 'u.campus_id = ?';
            $params[] = $campusId;
        }

        $sql = "
            SELECT u.id, u.alumni_id, u.name, u.email, u.role, u.campus_id, u.status, u.last_login, u.created_at,
                   c.name as campus_name, c.code as campus_code
            FROM users u
            LEFT JOIN campuses c ON u.campus_id = c.id
            WHERE " . implode(' AND ', $where) . "
            ORDER BY u.created_at DESC
            LIMIT 500
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        respondSuccess($stmt->fetchAll());
    }

    if ($method === 'POST') {
        $data = getRequestBody();

        $name = trim($data['name'] ?? '');
        $email = trim($data['email'] ?? '');
        $password = (string) ($data['password'] ?? '');
        $role = $data['role'] ?? 'admin';
        $campusId = $data['campus_id'] ?? null;

        if ($name === '' || $email === '' || $password === '') {
            respondError('Name, email and password are required', 400);
        }

        if (!validateEmail($email)) {
            respondError('Invalid email format', 400);
        }

        if (!in_array($role, ['alumni', 'admin', 'campus_admin', 'staff', 'system_admin'], true)) {
            respondError('Invalid user role', 400);
        }

        // Campus admin and staff require campus assignment
        if (in_array($role, ['campus_admin', 'staff']) && !$campusId) {
            respondError('Campus assignment required for this role', 400);
        }

        // Verify campus exists if provided
        if ($campusId) {
            $campusCheck = $db->prepare('SELECT id FROM campuses WHERE id = ? LIMIT 1');
            $campusCheck->execute([$campusId]);
            if (!$campusCheck->fetch()) {
                respondError('Invalid campus ID', 400);
            }
        }

        $existing = $db->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $existing->execute([$email]);
        if ($existing->fetch()) {
            respondError('Email already exists', 409);
        }

        $stmt = $db->prepare('
            INSERT INTO users (email, password, name, role, campus_id, auth_provider, status, email_verified, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ');
        $stmt->execute([
            $email,
            Password::hash($password),
            $name,
            $role,
            $campusId,
            'email',
            'active',
            1,
        ]);

        respondSuccess([
            'id' => (int) $db->lastInsertId(),
            'name' => $name,
            'email' => $email,
            'role' => $role,
            'campus_id' => $campusId
        ], 201);
    }

    respondError('Method not allowed', 405);
} catch (Exception $e) {
    respondError('User operation failed: ' . $e->getMessage(), 500);
}
