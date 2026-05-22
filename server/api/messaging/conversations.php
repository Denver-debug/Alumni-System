<?php
/**
 * Messaging Conversations API
 * GET /api/messaging/conversations - List user's conversations
 * POST /api/messaging/conversations - Create new conversation
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../middleware/auth.php';
require_once __DIR__ . '/../../config/auth.php';

header('Content-Type: application/json');

requireAuth();

$user = getCurrentUser();
$method = $_SERVER['REQUEST_METHOD'];

try {
    $db = Database::getInstance()->getConnection();
    
    if ($method === 'GET') {
        // Get user's conversations with last message
        $stmt = $db->prepare("
            SELECT 
                c.*,
                (SELECT content FROM messages WHERE conversation_id = c.id ORDER BY created_at DESC LIMIT 1) as last_message,
                (SELECT sender_id FROM messages WHERE conversation_id = c.id ORDER BY created_at DESC LIMIT 1) as last_sender_id,
                (SELECT created_at FROM messages WHERE conversation_id = c.id ORDER BY created_at DESC LIMIT 1) as last_message_at,
                (SELECT COUNT(*) FROM messages m 
                 LEFT JOIN message_reads mr ON m.id = mr.message_id AND mr.user_id = ?
                 WHERE m.conversation_id = c.id AND m.sender_id != ? AND mr.id IS NULL) as unread_count
            FROM conversations c
            JOIN conversation_participants cp ON c.id = cp.conversation_id
            WHERE cp.user_id = ? AND cp.left_at IS NULL
            ORDER BY last_message_at DESC
        ");
        $stmt->execute([$user['id'], $user['id'], $user['id']]);
        $conversations = $stmt->fetchAll();
        
        // Get participants for each conversation
        foreach ($conversations as &$conv) {
            $stmt = $db->prepare("
                SELECT u.id, u.name, u.profile_image, u.alumni_id
                FROM conversation_participants cp
                JOIN users u ON cp.user_id = u.id
                WHERE cp.conversation_id = ? AND cp.left_at IS NULL
            ");
            $stmt->execute([$conv['id']]);
            $participants = $stmt->fetchAll();
            
            // Process profile images for participants
            foreach ($participants as &$participant) {
                if (isset($participant['profile_image'])) {
                    $participant['profile_image'] = resolveProfileImageUrl($participant['profile_image']);
                }
            }
            $conv['participants'] = $participants;
            
            // For personal chats, get the other person's name
            if ($conv['type'] === 'personal') {
                foreach ($conv['participants'] as $p) {
                    if ($p['id'] != $user['id']) {
                        $conv['display_name'] = $p['name'];
                        $conv['display_image'] = resolveProfileImageUrl($p['profile_image']);
                        break;
                    }
                }
            }
        }
        
        echo json_encode(['success' => true, 'data' => $conversations]);
        
    } elseif ($method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        
        $type = $data['type'] ?? 'personal';
        $participants = $data['participants'] ?? [];
        
        // Validate
        if ($type === 'personal' && count($participants) !== 1) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Personal chat requires exactly one participant']);
            exit;
        }
        
        // For personal chats, check if conversation already exists
        if ($type === 'personal') {
            $otherId = $participants[0];
            $stmt = $db->prepare("
                SELECT c.id FROM conversations c
                JOIN conversation_participants cp1 ON c.id = cp1.conversation_id AND cp1.user_id = ?
                JOIN conversation_participants cp2 ON c.id = cp2.conversation_id AND cp2.user_id = ?
                WHERE c.type = 'personal'
                LIMIT 1
            ");
            $stmt->execute([$user['id'], $otherId]);
            $existing = $stmt->fetch();
            
            if ($existing) {
                echo json_encode(['success' => true, 'data' => ['conversation_id' => $existing['id'], 'existing' => true]]);
                exit;
            }
        }
        
        $db->beginTransaction();
        
        // Create conversation
        $stmt = $db->prepare("
            INSERT INTO conversations (type, name, created_by, created_at)
            VALUES (?, ?, ?, NOW())
        ");
        $stmt->execute([
            $type,
            $data['name'] ?? null,
            $user['id']
        ]);
        $conversationId = $db->lastInsertId();
        
        // Add creator as participant
        $stmt = $db->prepare("INSERT INTO conversation_participants (conversation_id, user_id, role, joined_at) VALUES (?, ?, 'admin', NOW())");
        $stmt->execute([$conversationId, $user['id']]);
        
        // Add other participants
        $stmt = $db->prepare("INSERT INTO conversation_participants (conversation_id, user_id, role, joined_at) VALUES (?, ?, 'member', NOW())");
        foreach ($participants as $pid) {
            $stmt->execute([$conversationId, $pid]);
        }
        
        $db->commit();
        
        echo json_encode([
            'success' => true,
            'data' => ['conversation_id' => $conversationId]
        ]);
        
    } else {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    }
    
} catch (PDOException $e) {
    if (isset($db) && $db->inTransaction()) $db->rollBack();
    error_log("Messaging Conversations Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
