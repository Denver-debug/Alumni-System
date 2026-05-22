<?php
/**
 * Messaging API
 * Handles conversations, messages, and group chats
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../utils/helpers.php';
require_once __DIR__ . '/../utils/uploads.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '';
$uri = rtrim($uri, '/');

try {
    requireAuth();
    
    $user = getCurrentUser();
    $userId = $user['id'];
    $pdo = Database::getInstance()->getConnection();
    
    // GET /api/v1/messages/conversations OR /api/v1/messaging/conversations - Get all conversations for current user
    if ($method === 'GET' && preg_match('/\/(messages|messaging)\/conversations$/', $uri)) {
        
        $stmt = $pdo->prepare("
            SELECT 
                c.*,
                (SELECT COUNT(*) FROM conversation_participants WHERE conversation_id = c.id) as participant_count,
                (SELECT content FROM messages WHERE conversation_id = c.id AND is_deleted = FALSE ORDER BY created_at DESC LIMIT 1) as last_message,
                (SELECT message_type FROM messages WHERE conversation_id = c.id AND is_deleted = FALSE ORDER BY created_at DESC LIMIT 1) as last_message_type,
                (SELECT sender_id FROM messages WHERE conversation_id = c.id AND is_deleted = FALSE ORDER BY created_at DESC LIMIT 1) as last_sender_id,
                (SELECT created_at FROM messages WHERE conversation_id = c.id AND is_deleted = FALSE ORDER BY created_at DESC LIMIT 1) as last_message_time,
                (SELECT u.name FROM users u WHERE u.id = (SELECT sender_id FROM messages WHERE conversation_id = c.id AND is_deleted = FALSE ORDER BY created_at DESC LIMIT 1)) as last_sender_name,
                COALESCE((SELECT COUNT(*) FROM messages m WHERE m.conversation_id = c.id AND m.created_at > COALESCE(cp.last_read_at, '1970-01-01') AND m.sender_id != ? AND m.is_deleted = FALSE), 0) as unread_count
            FROM conversations c
            INNER JOIN conversation_participants cp ON c.id = cp.conversation_id
            WHERE cp.user_id = ?
            ORDER BY COALESCE(last_message_time, c.created_at) DESC
        ");
        
        $stmt->execute([$userId, $userId]);
        
        $conversations = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get other participants for each conversation
        foreach ($conversations as &$conv) {
            $participantsStmt = $pdo->prepare("
                SELECT u.id, u.name, u.profile_image, u.alumni_id as alumni_number
                FROM conversation_participants cp
                INNER JOIN users u ON cp.user_id = u.id
                WHERE cp.conversation_id = ?
                AND cp.user_id != ?
            ");
            $participantsStmt->execute([$conv['id'], $userId]);
            $conv['participants'] = $participantsStmt->fetchAll(PDO::FETCH_ASSOC);
            
            // For direct conversations, set display name from other participant
            if ($conv['type'] === 'personal' && count($conv['participants']) > 0) {
                $conv['display_name'] = $conv['participants'][0]['name'];
                $conv['participant_image'] = $conv['participants'][0]['profile_image'];
            } else {
                $conv['display_name'] = $conv['name'] ?: 'Group Chat';
            }
        }
        
        success($conversations);
    }
    
    // GET /api/v1/messages/conversations/:id OR /api/v1/messaging/conversations/:id - Get conversation details
    if ($method === 'GET' && preg_match('/\/(messages|messaging)\/conversations\/(\d+)$/', $uri, $matches)) {
        
        $conversationId = $matches[2];
        
        // Verify user is participant
        $stmt = $pdo->prepare("
            SELECT COUNT(*) FROM conversation_participants
            WHERE conversation_id = ? AND user_id = ?
        ");
        $stmt->execute([$conversationId, $userId]);
        
        if ($stmt->fetchColumn() == 0) {
            throw new Exception('Access denied', 403);
        }
        
        // Get conversation details
        $stmt = $pdo->prepare("SELECT * FROM conversations WHERE id = ?");
        $stmt->execute([$conversationId]);
        $conversation = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$conversation) {
            throw new Exception('Conversation not found', 404);
        }
        
        // Get participants
        $stmt = $pdo->prepare("
            SELECT u.id, u.name, u.profile_image, u.alumni_id as alumni_number, cp.role
            FROM conversation_participants cp
            INNER JOIN users u ON cp.user_id = u.id
            WHERE cp.conversation_id = ?
        ");
        $stmt->execute([$conversationId]);
        $conversation['participants'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        success($conversation);
    }
    
    // GET /api/v1/messages/conversations/:id/messages OR /api/v1/messaging/conversations/:id/messages - Get messages in conversation
    if ($method === 'GET' && preg_match('/\/(messages|messaging)\/conversations\/(\d+)\/messages$/', $uri, $matches)) {
        
        $conversationId = $matches[2];
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
        $before = isset($_GET['before']) ? $_GET['before'] : null;
        
        // Verify user is participant
        $stmt = $pdo->prepare("
            SELECT COUNT(*) FROM conversation_participants
            WHERE conversation_id = ? AND user_id = ?
        ");
        $stmt->execute([$conversationId, $userId]);
        
        if ($stmt->fetchColumn() == 0) {
            throw new Exception('Access denied', 403);
        }
        
        // Get messages
        $sql = "
            SELECT 
                m.*,
                u.name as sender_name,
                u.profile_image as sender_image
            FROM messages m
            INNER JOIN users u ON m.sender_id = u.id
            WHERE m.conversation_id = ?
            AND m.is_deleted = FALSE
        ";
        
        if ($before) {
            $sql .= " AND m.created_at < ?";
        }
        
        $sql .= " ORDER BY m.created_at DESC LIMIT ?";
        
        $stmt = $pdo->prepare($sql);
        
        if ($before) {
            $stmt->execute([$conversationId, $before, $limit]);
        } else {
            $stmt->execute([$conversationId, $limit]);
        }
        
        $messages = array_reverse($stmt->fetchAll(PDO::FETCH_ASSOC));
        
        success($messages);
    }
    
    // POST /api/v1/messages/conversations/:id/messages OR /api/v1/messaging/conversations/:id/messages - Send message
    if ($method === 'POST' && preg_match('/\/(messages|messaging)\/conversations\/(\d+)\/messages$/', $uri, $matches)) {
        
        $conversationId = $matches[2];
        $isMultipart = stripos($_SERVER['CONTENT_TYPE'] ?? '', 'multipart/form-data') !== false;
        $data = $isMultipart ? $_POST : json_decode(file_get_contents('php://input'), true);
        $data = is_array($data) ? $data : [];
        
        if (
            !$isMultipart &&
            (!isset($data['content']) || trim((string) $data['content']) === '')
        ) {
            throw new Exception('Message cannot be empty');
        }
        
        // Verify user is participant
        $stmt = $pdo->prepare("
            SELECT COUNT(*) FROM conversation_participants
            WHERE conversation_id = ? AND user_id = ?
        ");
        $stmt->execute([$conversationId, $userId]);
        
        if ($stmt->fetchColumn() == 0) {
            throw new Exception('Access denied', 403);
        }
        
        $content = trim((string) ($data['content'] ?? ''));
        $messageType = $data['message_type'] ?? 'text';
        $allowedMessageTypes = ['text', 'image', 'file', 'system'];
        if (!in_array($messageType, $allowedMessageTypes, true)) {
            $messageType = 'file';
        }

        $fileUrl = null;
        $fileName = null;
        $fileSize = null;

        if ($isMultipart && isset($_FILES['attachment']) && ($_FILES['attachment']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $allowedTypes = array_merge(
                ALLOWED_IMAGE_TYPES,
                ALLOWED_DOC_TYPES,
                ['audio/webm', 'audio/ogg', 'audio/mpeg', 'audio/mp4', 'audio/wav', 'audio/x-wav', 'video/webm']
            );
            $uploader = new FileUploader('messages/' . (int) $conversationId, $allowedTypes, UPLOAD_MAX_SIZE);
            $filename = $uploader->upload($_FILES['attachment']);

            if (!$filename) {
                throw new Exception(implode(', ', $uploader->getErrors()) ?: 'File upload failed', 422);
            }

            $fileUrl = $uploader->getUrl($filename);
            $fileName = basename((string) ($_FILES['attachment']['name'] ?? $filename));
            $fileSize = (int) ($_FILES['attachment']['size'] ?? 0);

            if ($content === '') {
                $content = $messageType === 'image' ? 'Photo' : $fileName;
            }
        }

        if ($content === '' && !$fileUrl) {
            throw new Exception('Message cannot be empty');
        }

        // Insert message
        $stmt = $pdo->prepare("
            INSERT INTO messages (conversation_id, sender_id, content, message_type, file_url, file_name, file_size)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([$conversationId, $userId, $content, $messageType, $fileUrl, $fileName, $fileSize]);
        
        $messageId = $pdo->lastInsertId();
        
        // Update conversation timestamp
        $pdo->prepare("UPDATE conversations SET updated_at = NOW() WHERE id = ?")->execute([$conversationId]);
        
        // Get the created message
        $stmt = $pdo->prepare("
            SELECT 
                m.*,
                u.name as sender_name,
                u.profile_image as sender_image
            FROM messages m
            INNER JOIN users u ON m.sender_id = u.id
            WHERE m.id = ?
        ");
        $stmt->execute([$messageId]);
        
        success($stmt->fetch(PDO::FETCH_ASSOC), 'Message sent successfully');
    }
    
    // POST /api/v1/messages/conversations OR /api/v1/messaging/conversations - Create new conversation
    if ($method === 'POST' && preg_match('/\/(messages|messaging)\/conversations$/', $uri)) {
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['participant_ids']) || !is_array($data['participant_ids'])) {
            throw new Exception('Participant IDs required');
        }
        
        $participantIds = array_unique($data['participant_ids']);
        
        // For direct messages, check if conversation already exists
        if (count($participantIds) === 1) {
            $otherUserId = $participantIds[0];
            
            $stmt = $pdo->prepare("
                SELECT c.id
                FROM conversations c
                INNER JOIN conversation_participants cp1 ON c.id = cp1.conversation_id AND cp1.user_id = ?
                INNER JOIN conversation_participants cp2 ON c.id = cp2.conversation_id AND cp2.user_id = ?
                WHERE c.type = 'personal'
                LIMIT 1
            ");
            $stmt->execute([$userId, $otherUserId]);
            
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($existing) {
                success(['id' => $existing['id']], 'Conversation already exists');
                exit;
            }
        }
        
        $pdo->beginTransaction();
        
        try {
            // Create conversation
            $type = count($participantIds) > 1 ? 'group' : 'personal';
            $name = $data['name'] ?? null;
            
            $stmt = $pdo->prepare("
                INSERT INTO conversations (type, name, created_by)
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$type, $name, $userId]);
            
            $conversationId = $pdo->lastInsertId();
            
            // Add creator as participant with owner role
            $stmt = $pdo->prepare("
                INSERT INTO conversation_participants (conversation_id, user_id, role)
                VALUES (?, ?, 'owner')
            ");
            $stmt->execute([$conversationId, $userId]);
            
            // Add other participants
            $stmt = $pdo->prepare("
                INSERT INTO conversation_participants (conversation_id, user_id, role)
                VALUES (?, ?, 'member')
            ");
            
            foreach ($participantIds as $participantId) {
                if ($participantId != $userId) {
                    $stmt->execute([$conversationId, $participantId]);
                }
            }
            
            $pdo->commit();
            
            success(['id' => $conversationId], 'Conversation created successfully');
            
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
    
    // PUT /api/v1/messages/conversations/:id/read OR /api/v1/messaging/conversations/:id/read - Mark conversation as read
    if ($method === 'PUT' && preg_match('/\/(messages|messaging)\/conversations\/(\d+)\/read$/', $uri, $matches)) {
        
        $conversationId = $matches[2];
        
        $stmt = $pdo->prepare("
            UPDATE conversation_participants
            SET last_read_at = NOW()
            WHERE conversation_id = ? AND user_id = ?
        ");
        
        $stmt->execute([$conversationId, $userId]);
        
        success([], 'Marked as read');
    }
    
    // GET /api/v1/messages/alumni/search OR /api/v1/messaging/alumni/search - Search alumni for messaging
    if ($method === 'GET' && preg_match('/\/(messages|messaging)\/alumni\/search$/', $uri)) {
        
        $query = $_GET['q'] ?? '';
        $batch = $_GET['batch'] ?? null;
        $college = $_GET['college'] ?? null;
        $program = $_GET['program'] ?? null;
        
        $sql = "
            SELECT 
                u.id,
                u.name,
                u.email,
                u.profile_image,
                u.alumni_id as alumni_number,
                ap.batch_year,
                c.name as college_name,
                p.name as program_name
            FROM users u
            LEFT JOIN alumni_profiles ap ON u.id = ap.user_id
            LEFT JOIN colleges c ON ap.college_id = c.id
            LEFT JOIN programs p ON ap.program_id = p.id
            WHERE u.role = 'alumni'
            AND u.id != ?
        ";
        
        $params = [$userId];
        
        if ($query) {
            $sql .= " AND (u.name LIKE ? OR u.email LIKE ? OR u.alumni_id LIKE ?)";
            $searchTerm = "%$query%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        if ($batch) {
            $sql .= " AND ap.batch_year = ?";
            $params[] = $batch;
        }
        
        if ($college) {
            $sql .= " AND ap.college_id = ?";
            $params[] = $college;
        }
        
        if ($program) {
            $sql .= " AND ap.program_id = ?";
            $params[] = $program;
        }
        
        $sql .= " ORDER BY u.name LIMIT 50";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        success($stmt->fetchAll(PDO::FETCH_ASSOC));
    }
    
    // If no route matched
    error('Endpoint not found', 404);
    
} catch (Exception $e) {
    $code = $e->getCode() ?: 500;
    error($e->getMessage(), $code);
}
