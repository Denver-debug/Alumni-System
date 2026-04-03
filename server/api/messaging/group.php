<?php
/**
 * Organization Group Chat API
 * POST /api/messaging/group/section - Create/get section group chat
 * POST /api/messaging/group/program - Create/get program group chat
 * POST /api/messaging/group/college - Create/get college group chat
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../middleware/auth.php';
require_once __DIR__ . '/../../config/auth.php';

header('Content-Type: application/json');

requireAuth();

$user = getCurrentUser();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$groupType = $data['type'] ?? null; // section, program, college

if (!in_array($groupType, ['section', 'program', 'college'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid group type']);
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    
    // Get user's profile
    $stmt = $db->prepare("
        SELECT ap.*, c.name as college_name, p.name as program_name, s.name as section_name
        FROM alumni_profiles ap
        LEFT JOIN colleges c ON ap.college_id = c.id
        LEFT JOIN programs p ON ap.program_id = p.id
        LEFT JOIN sections s ON ap.section_id = s.id
        WHERE ap.user_id = ?
    ");
    $stmt->execute([$user['id']]);
    $profile = $stmt->fetch();
    
    if (!$profile) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Profile not found']);
        exit;
    }
    
    // Determine reference
    $referenceId = null;
    $conversationType = null;
    $groupName = null;
    
    switch ($groupType) {
        case 'section':
            if (!$profile['section_id']) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Not assigned to a section']);
                exit;
            }
            $referenceId = $profile['section_id'];
            $conversationType = 'section';
            $groupName = "Section: " . $profile['section_name'] . " (" . $profile['batch_year'] . ")";
            break;
            
        case 'program':
            if (!$profile['program_id']) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Not assigned to a program']);
                exit;
            }
            $referenceId = $profile['program_id'];
            $conversationType = 'program';
            $batchYear = $data['batch_year'] ?? $profile['batch_year'];
            $groupName = $profile['program_name'] . " Batch " . $batchYear;
            break;
            
        case 'college':
            if (!$profile['college_id']) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Not assigned to a college']);
                exit;
            }
            $referenceId = $profile['college_id'];
            $conversationType = 'college';
            $groupName = $profile['college_name'] . " Alumni";
            break;
    }
    
    // Check if conversation exists
    $stmt = $db->prepare("SELECT id FROM conversations WHERE type = ? AND reference_id = ?");
    $stmt->execute([$conversationType, $referenceId]);
    $existing = $stmt->fetch();
    
    if ($existing) {
        // Add user if not participant
        $stmt = $db->prepare("SELECT id FROM conversation_participants WHERE conversation_id = ? AND user_id = ?");
        $stmt->execute([$existing['id'], $user['id']]);
        if (!$stmt->fetch()) {
            $stmt = $db->prepare("INSERT INTO conversation_participants (conversation_id, user_id, role, joined_at) VALUES (?, ?, 'member', NOW())");
            $stmt->execute([$existing['id'], $user['id']]);
        }
        
        echo json_encode(['success' => true, 'data' => ['conversation_id' => $existing['id'], 'existing' => true]]);
        exit;
    }
    
    // Create new conversation
    $db->beginTransaction();
    
    $stmt = $db->prepare("INSERT INTO conversations (type, name, reference_id, created_by, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([$conversationType, $groupName, $referenceId, $user['id']]);
    $conversationId = $db->lastInsertId();
    
    // Add all matching alumni as participants
    $participantQuery = match($groupType) {
        'section' => "SELECT user_id FROM alumni_profiles WHERE section_id = ?",
        'program' => "SELECT user_id FROM alumni_profiles WHERE program_id = ? AND batch_year = ?",
        'college' => "SELECT user_id FROM alumni_profiles WHERE college_id = ?"
    };
    
    $params = match($groupType) {
        'section' => [$referenceId],
        'program' => [$referenceId, $batchYear ?? $profile['batch_year']],
        'college' => [$referenceId]
    };
    
    $stmt = $db->prepare($participantQuery);
    $stmt->execute($params);
    $members = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $insertStmt = $db->prepare("INSERT INTO conversation_participants (conversation_id, user_id, role, joined_at) VALUES (?, ?, 'member', NOW())");
    foreach ($members as $memberId) {
        $insertStmt->execute([$conversationId, $memberId]);
    }
    
    $db->commit();
    
    echo json_encode([
        'success' => true,
        'data' => [
            'conversation_id' => $conversationId,
            'name' => $groupName,
            'member_count' => count($members)
        ]
    ]);
    
} catch (PDOException $e) {
    if (isset($db) && $db->inTransaction()) $db->rollBack();
    error_log("Org Group Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
