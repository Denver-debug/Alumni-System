<?php
/**
 * Messaging API - Mark Conversation as Read
 * POST /api/messaging/conversations/{id}/read
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../utils/helpers.php';
require_once __DIR__ . '/../../middleware/auth.php';

requireAuth();

try {
    $db = Database::getInstance()->getConnection();
    $user = getCurrentUser();

    $conversationId = $GLOBALS['url_params']['id'] ?? null;

    if (!$conversationId) {
        respondError('Conversation ID required', 400);
    }

    // Ensure user belongs to this conversation.
    $stmt = $db->prepare("SELECT id FROM conversation_participants WHERE conversation_id = ? AND user_id = ? AND left_at IS NULL");
    $stmt->execute([$conversationId, $user['id']]);
    if (!$stmt->fetch()) {
        respondError('Not a participant in this conversation', 403);
    }

    $stmt = $db->prepare("
        INSERT IGNORE INTO message_reads (message_id, user_id, read_at)
        SELECT m.id, :user_id, NOW()
        FROM messages m
        WHERE m.conversation_id = :conversation_id
          AND m.sender_id != :user_id
    ");
    $stmt->execute([
        'user_id' => $user['id'],
        'conversation_id' => $conversationId
    ]);

    respondSuccess([
        'conversation_id' => (int) $conversationId,
        'message' => 'Conversation marked as read'
    ]);
} catch (Exception $e) {
    respondError('Failed to mark conversation as read: ' . $e->getMessage(), 500);
}
