<?php
/**
 * Program Campus Association API
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../config/auth.php';
require_once __DIR__ . '/../../../utils/helpers.php';
require_once __DIR__ . '/../../../middleware/auth.php';

requireAdmin();

try {
    $db = Database::getInstance()->getConnection();
    $method = $_SERVER['REQUEST_METHOD'];
    $id = $GLOBALS['url_params']['id'] ?? null;
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '';
    $isByCampus = str_contains($path, 'program-campuses-by-campus');

    if (!$id || !is_numeric($id)) {
        error($isByCampus ? 'Invalid campus ID' : 'Invalid program ID', 400);
    }

    if ($isByCampus) {
        if ($method !== 'GET') {
            error('Method not allowed', 405);
        }

        $stmt = $db->prepare('
            SELECT p.id, p.name, p.code, p.college_id, p.degree_type
            FROM program_campus pc
            JOIN programs p ON pc.program_id = p.id
            WHERE pc.campus_id = ?
              AND COALESCE(p.status, "active") = "active"
            ORDER BY p.name ASC
        ');
        $stmt->execute([(int)$id]);
        success($stmt->fetchAll());
    }

    $programId = (int)$id;
    $stmt = $db->prepare('SELECT id FROM programs WHERE id = ? LIMIT 1');
    $stmt->execute([$programId]);
    if (!$stmt->fetch()) {
        notFound('Program not found');
    }

    if ($method === 'GET') {
        $stmt = $db->prepare('
            SELECT c.id, c.name, c.code, c.location
            FROM program_campus pc
            JOIN campuses c ON pc.campus_id = c.id
            WHERE pc.program_id = ?
            ORDER BY c.name ASC
        ');
        $stmt->execute([$programId]);
        success($stmt->fetchAll());
    }

    if ($method === 'POST') {
        $data = getRequestBody();
        $campusIds = $data['campus_ids'] ?? [];

        if (!is_array($campusIds) || empty($campusIds)) {
            validationError(['campus_ids' => 'At least one campus must be selected']);
        }

        $campusIds = array_values(array_unique(array_map('intval', $campusIds)));
        foreach ($campusIds as $campusId) {
            $stmt = $db->prepare('SELECT id FROM campuses WHERE id = ? LIMIT 1');
            $stmt->execute([$campusId]);
            if (!$stmt->fetch()) {
                validationError(['campus_ids' => 'Invalid campus ID: ' . $campusId]);
            }
        }

        $db->beginTransaction();

        $stmt = $db->prepare('DELETE FROM program_campus WHERE program_id = ?');
        $stmt->execute([$programId]);

        $insertStmt = $db->prepare('
            INSERT INTO program_campus (program_id, campus_id, created_at)
            VALUES (?, ?, NOW())
        ');

        foreach ($campusIds as $campusId) {
            $insertStmt->execute([$programId, $campusId]);
        }

        $db->commit();
        success([], 'Program campus assignments updated successfully');
    }

    error('Method not allowed', 405);
} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }

    error('Program campus operation failed: ' . $e->getMessage(), 500);
}
