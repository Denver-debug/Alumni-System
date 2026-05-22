<?php
/**
 * Messaging Messages API
 * GET /api/messaging/messages/{conversation_id} - Get messages
 * POST /api/messaging/messages/{conversation_id} - Send message
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../middleware/auth.php';
require_once __DIR__ . '/../../config/auth.php';

header('Content-Type: application/json');

requireAuth();

$user = getCurrentUser();
$conversationId = $GLOBALS['url_params']['conversation_id'] ?? null;

if (!$conversationId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Conversation ID required']);
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    
    // Verify user is participant
    $stmt = $db->prepare("SELECT id FROM conversation_participants WHERE conversation_id = ? AND user_id = ? AND left_at IS NULL");
    $stmt->execute([$conversationId, $user['id']]);
    if (!$stmt->fetch()) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Not a participant']);
        exit;
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $limit = 50;
        $offset = ($page - 1) * $limit;
        
        $stmt = $db->prepare("
            SELECT m.*, u.name as sender_name, u.profile_image as sender_image,
                (CASE WHEN mr.id IS NOT NULL THEN 1 ELSE 0 END) as is_read
            FROM messages m
            JOIN users u ON m.sender_id = u.id
            LEFT JOIN message_reads mr ON m.id = mr.message_id AND mr.user_id = ?
            WHERE m.conversation_id = ?
            ORDER BY m.created_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$user['id'], $conversationId, $limit, $offset]);
        $messages = $stmt->fetchAll();
        
        // Process profile images
        foreach ($messages as &$message) {
            if (isset($message['sender_image'])) {
                $message['sender_image'] = resolveProfileImageUrl($message['sender_image']);
            }
        }
        
        // Mark as read
        $stmt = $db->prepare("
            INSERT IGNORE INTO message_reads (message_id, user_id, read_at)
            SELECT m.id, ?, NOW()
            FROM messages m
            WHERE m.conversation_id = ? AND m.sender_id != ?
        ");
        $stmt->execute([$user['id'], $conversationId, $user['id']]);
        
        echo json_encode(['success' => true, 'data' => array_reverse($messages)]);
        
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (empty($data['content'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Message content required']);
            exit;
        }
        
        $stmt = $db->prepare("
            INSERT INTO messages (conversation_id, sender_id, content, message_type, created_at)
            VALUES (?, ?, ?, ?, NOW())
        ");
        $stmt->execute([
            $conversationId,
            $user['id'],
            trim($data['content']),
            $data['message_type'] ?? 'text'
        ]);
        
        $messageId = $db->lastInsertId();
        
        // Update conversation timestamp
        $stmt = $db->prepare("UPDATE conversations SET updated_at = NOW() WHERE id = ?");
        $stmt->execute([$conversationId]);
        
        // Return the message
        $stmt = $db->prepare("
            SELECT m.*, u.name as sender_name, u.profile_image as sender_image
            FROM messages m
            JOIN users u ON m.sender_id = u.id
            WHERE m.id = ?
        ");
        $stmt->execute([$messageId]);
        $message = $stmt->fetch();
        
        echo json_encode(['success' => true, 'data' => $message]);
        
    } else {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    }
    
} catch (PDOException $e) {
    error_log("Messaging Messages Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
