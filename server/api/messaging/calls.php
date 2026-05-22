<?php
/**
 * Messaging Call Invites API
 * Lightweight call signaling for alumni direct conversations.
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../middleware/auth.php';
require_once __DIR__ . '/../../utils/helpers.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '';
$uri = rtrim($uri, '/');
$routeParams = $_REQUEST['_route_params'] ?? [];
const CALL_RING_TIMEOUT_SECONDS = 45;

try {
    requireAuth();

    $user = getCurrentUser();
    $userId = (int) $user['id'];
    $pdo = Database::getInstance()->getConnection();

    ensureCallInvitesTable($pdo);
    expireStaleCallInvites($pdo);

    if ($method === 'POST' && preg_match('/\/(messages|messaging)\/calls$/', $uri)) {
        $data = getRequestBody();
        $conversationId = (int) ($data['conversation_id'] ?? 0);
        $callType = strtolower(trim((string) ($data['call_type'] ?? 'audio')));

        if ($conversationId <= 0) {
            throw new Exception('Conversation is required', 422);
        }

        if (!in_array($callType, ['audio', 'video'], true)) {
            $callType = 'audio';
        }

        $conversation = getCallableDirectConversation($pdo, $conversationId, $userId);
        $recipientId = (int) $conversation['recipient_id'];

        $pdo->prepare("
            UPDATE call_invites
            SET status = 'missed', ended_at = NOW()
            WHERE conversation_id = ?
              AND caller_id = ?
              AND recipient_id = ?
              AND status = 'ringing'
        ")->execute([$conversationId, $userId, $recipientId]);

        $stmt = $pdo->prepare("
            INSERT INTO call_invites (conversation_id, caller_id, recipient_id, call_type, status)
            VALUES (?, ?, ?, ?, 'ringing')
        ");
        $stmt->execute([$conversationId, $userId, $recipientId, $callType]);

        $call = fetchCallInvite($pdo, (int) $pdo->lastInsertId(), $userId);
        $historyMessage = recordCallHistoryMessage($pdo, $call, $userId, 'started');
        if ($historyMessage) {
            $call['history_message'] = $historyMessage;
        }
        success($call, 'Call invite sent');
    }

    if ($method === 'GET' && preg_match('/\/(messages|messaging)\/calls\/incoming$/', $uri)) {
        $stmt = $pdo->prepare("
            SELECT
                ci.*,
                caller.name AS caller_name,
                caller.profile_image AS caller_image,
                recipient.name AS recipient_name,
                c.name AS conversation_name,
                c.type AS conversation_type
            FROM call_invites ci
            INNER JOIN users caller ON caller.id = ci.caller_id
            INNER JOIN users recipient ON recipient.id = ci.recipient_id
            INNER JOIN conversations c ON c.id = ci.conversation_id
            WHERE ci.recipient_id = ?
              AND ci.status = 'ringing'
            ORDER BY ci.created_at DESC
            LIMIT 5
        ");
        $stmt->execute([$userId]);

        success($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    if ($method === 'GET' && preg_match('/\/(messages|messaging)\/calls\/(\d+)\/signals$/', $uri, $matches)) {
        $callId = (int) ($routeParams['id'] ?? $matches[2]);
        fetchCallInvite($pdo, $callId, $userId);
        $afterId = max(0, (int) ($_GET['after'] ?? 0));

        $stmt = $pdo->prepare("
            SELECT id, call_id, sender_id, signal_type, payload, created_at
            FROM call_signals
            WHERE call_id = ?
              AND sender_id != ?
              AND id > ?
            ORDER BY id ASC
            LIMIT 100
        ");
        $stmt->execute([$callId, $userId, $afterId]);
        $signals = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($signals as &$signal) {
            $decoded = json_decode((string) $signal['payload'], true);
            $signal['payload'] = $decoded ?? $signal['payload'];
        }

        success($signals);
    }

    if ($method === 'POST' && preg_match('/\/(messages|messaging)\/calls\/(\d+)\/signals$/', $uri, $matches)) {
        $callId = (int) ($routeParams['id'] ?? $matches[2]);
        $call = fetchCallInvite($pdo, $callId, $userId);

        if (in_array($call['status'], ['ended', 'declined', 'missed'], true)) {
            throw new Exception('This call has already ended', 409);
        }

        $data = getRequestBody();
        $signalType = strtolower(trim((string) ($data['signal_type'] ?? $data['type'] ?? '')));
        $payload = $data['payload'] ?? null;

        if (!in_array($signalType, ['offer', 'answer', 'ice'], true)) {
            throw new Exception('Invalid call signal type', 422);
        }

        if ($payload === null || $payload === '') {
            throw new Exception('Signal payload is required', 422);
        }

        $encodedPayload = is_string($payload)
            ? $payload
            : json_encode($payload, JSON_UNESCAPED_UNICODE);

        $stmt = $pdo->prepare("
            INSERT INTO call_signals (call_id, sender_id, signal_type, payload)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$callId, $userId, $signalType, $encodedPayload]);

        success(['id' => (int) $pdo->lastInsertId()], 'Call signal sent');
    }

    if ($method === 'GET' && preg_match('/\/(messages|messaging)\/calls\/(\d+)$/', $uri, $matches)) {
        $callId = (int) ($routeParams['id'] ?? $matches[2]);
        success(fetchCallInvite($pdo, $callId, $userId));
    }

    if ($method === 'PUT' && preg_match('/\/(messages|messaging)\/calls\/(\d+)\/respond$/', $uri, $matches)) {
        $callId = (int) ($routeParams['id'] ?? $matches[2]);
        $data = getRequestBody();
        $action = strtolower(trim((string) ($data['action'] ?? '')));

        if (!in_array($action, ['accepted', 'declined'], true)) {
            throw new Exception('Invalid call response', 422);
        }

        $call = fetchCallInvite($pdo, $callId, $userId);
        if ((int) $call['recipient_id'] !== $userId) {
            throw new Exception('Only the receiving alumni can answer this call', 403);
        }

        if ($call['status'] !== 'ringing') {
            throw new Exception('This call is no longer ringing', 409);
        }

        if ($action === 'accepted') {
            $pdo->prepare("
                UPDATE call_invites
                SET status = 'missed', ended_at = NOW()
                WHERE recipient_id = ?
                  AND id != ?
                  AND status = 'ringing'
            ")->execute([$userId, $callId]);
        }

        $stmt = $pdo->prepare("
            UPDATE call_invites
            SET status = ?, responded_at = NOW()
            WHERE id = ?
        ");
        $stmt->execute([$action, $callId]);

        $updatedCall = fetchCallInvite($pdo, $callId, $userId);
        $historyMessage = recordCallHistoryMessage($pdo, $updatedCall, $userId, $action);
        if ($historyMessage) {
            $updatedCall['history_message'] = $historyMessage;
        }
        success($updatedCall, $action === 'accepted' ? 'Call accepted' : 'Call declined');
    }

    if ($method === 'PUT' && preg_match('/\/(messages|messaging)\/calls\/(\d+)\/end$/', $uri, $matches)) {
        $callId = (int) ($routeParams['id'] ?? $matches[2]);
        $call = fetchCallInvite($pdo, $callId, $userId);

        if (!in_array($call['status'], ['ended', 'declined', 'missed'], true)) {
            $pdo->prepare("
                UPDATE call_invites
                SET status = 'ended', ended_at = NOW()
                WHERE id = ?
            ")->execute([$callId]);

            $call = fetchCallInvite($pdo, $callId, $userId);
            $historyMessage = recordCallHistoryMessage($pdo, $call, $userId, 'ended');
            if ($historyMessage) {
                $call['history_message'] = $historyMessage;
            }
        }

        success($call, 'Call ended');
    }

    error('Endpoint not found', 404);
} catch (Exception $e) {
    $code = (int) ($e->getCode() ?: 500);
    error($e->getMessage(), $code);
}

function ensureCallInvitesTable(PDO $pdo): void
{
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS call_invites (
            id INT AUTO_INCREMENT PRIMARY KEY,
            conversation_id INT NOT NULL,
            caller_id INT NOT NULL,
            recipient_id INT NOT NULL,
            call_type ENUM('audio', 'video') NOT NULL DEFAULT 'audio',
            status ENUM('ringing', 'accepted', 'declined', 'ended', 'missed') NOT NULL DEFAULT 'ringing',
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            responded_at DATETIME NULL,
            ended_at DATETIME NULL,
            INDEX idx_call_recipient_status (recipient_id, status),
            INDEX idx_call_caller_status (caller_id, status),
            INDEX idx_call_conversation (conversation_id),
            INDEX idx_call_created (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS call_signals (
            id INT AUTO_INCREMENT PRIMARY KEY,
            call_id INT NOT NULL,
            sender_id INT NOT NULL,
            signal_type ENUM('offer', 'answer', 'ice') NOT NULL,
            payload LONGTEXT NOT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_call_signal_call (call_id, id),
            INDEX idx_call_signal_sender (sender_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
}

function expireStaleCallInvites(PDO $pdo): void
{
    $timeout = CALL_RING_TIMEOUT_SECONDS;
    $stmt = $pdo->query("
        SELECT *
        FROM call_invites
        WHERE status = 'ringing'
          AND created_at < DATE_SUB(NOW(), INTERVAL {$timeout} SECOND)
    ");
    $staleCalls = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];

    $pdo->exec("
        UPDATE call_invites
        SET status = 'missed', ended_at = NOW()
        WHERE status = 'ringing'
          AND created_at < DATE_SUB(NOW(), INTERVAL {$timeout} SECOND)
    ");

    foreach ($staleCalls as $call) {
        recordCallHistoryMessage($pdo, $call, (int) $call['caller_id'], 'missed');
    }
}

function getCallableDirectConversation(PDO $pdo, int $conversationId, int $userId): array
{
    $stmt = $pdo->prepare("
        SELECT c.id, c.name, c.type
        FROM conversations c
        INNER JOIN conversation_participants cp ON cp.conversation_id = c.id
        WHERE c.id = ?
          AND cp.user_id = ?
        LIMIT 1
    ");
    $stmt->execute([$conversationId, $userId]);
    $conversation = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$conversation) {
        throw new Exception('Conversation not found or access denied', 404);
    }

    if (strtolower((string) $conversation['type']) !== 'personal') {
        throw new Exception('Calls are available for direct conversations right now', 422);
    }

    $stmt = $pdo->prepare("
        SELECT u.id, u.name, u.profile_image
        FROM conversation_participants cp
        INNER JOIN users u ON u.id = cp.user_id
        WHERE cp.conversation_id = ?
          AND cp.user_id != ?
        LIMIT 1
    ");
    $stmt->execute([$conversationId, $userId]);
    $recipient = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$recipient) {
        throw new Exception('No recipient found for this conversation', 422);
    }

    return array_merge($conversation, [
        'recipient_id' => (int) $recipient['id'],
        'recipient_name' => $recipient['name'],
        'recipient_image' => $recipient['profile_image'],
    ]);
}

function fetchCallInvite(PDO $pdo, int $callId, int $userId): array
{
    $stmt = $pdo->prepare("
        SELECT
            ci.*,
            caller.name AS caller_name,
            caller.profile_image AS caller_image,
            recipient.name AS recipient_name,
            c.name AS conversation_name,
            c.type AS conversation_type
        FROM call_invites ci
        INNER JOIN users caller ON caller.id = ci.caller_id
        INNER JOIN users recipient ON recipient.id = ci.recipient_id
        INNER JOIN conversations c ON c.id = ci.conversation_id
        WHERE ci.id = ?
        LIMIT 1
    ");
    $stmt->execute([$callId]);
    $call = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$call) {
        throw new Exception('Call not found', 404);
    }

    if ((int) $call['caller_id'] !== $userId && (int) $call['recipient_id'] !== $userId) {
        throw new Exception('Access denied', 403);
    }

    return $call;
}

function recordCallHistoryMessage(PDO $pdo, array $call, int $senderId, string $event): ?array
{
    $conversationId = (int) ($call['conversation_id'] ?? 0);
    if ($conversationId <= 0 || $senderId <= 0) {
        return null;
    }

    $label = strtolower((string) ($call['call_type'] ?? 'audio')) === 'video'
        ? 'Video call'
        : 'Audio call';

    switch ($event) {
        case 'started':
            $content = $label . ' started';
            break;
        case 'accepted':
            $content = $label . ' answered';
            break;
        case 'declined':
            $content = $label . ' declined';
            break;
        case 'ended':
            $content = $label . ' ended';
            $duration = formatCallDuration($call['responded_at'] ?? null, $call['ended_at'] ?? null);
            if ($duration) {
                $content .= ' (' . $duration . ')';
            }
            break;
        case 'missed':
            $content = 'Missed ' . strtolower($label);
            break;
        default:
            $content = $label . ' updated';
            break;
    }

    $stmt = $pdo->prepare("
        INSERT INTO messages (conversation_id, sender_id, content, message_type)
        VALUES (?, ?, ?, 'system')
    ");
    $stmt->execute([$conversationId, $senderId, $content]);
    $messageId = (int) $pdo->lastInsertId();

    $pdo->prepare("UPDATE conversations SET updated_at = NOW() WHERE id = ?")
        ->execute([$conversationId]);

    return fetchCallHistoryMessage($pdo, $messageId);
}

function formatCallDuration(?string $start, ?string $end): ?string
{
    if (!$start || !$end) {
        return null;
    }

    $startTime = strtotime($start);
    $endTime = strtotime($end);
    if ($startTime === false || $endTime === false || $endTime <= $startTime) {
        return null;
    }

    $totalSeconds = $endTime - $startTime;
    $hours = (int) floor($totalSeconds / 3600);
    $minutes = (int) floor(($totalSeconds % 3600) / 60);
    $seconds = (int) ($totalSeconds % 60);

    if ($hours > 0) {
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }

    return sprintf('%02d:%02d', $minutes, $seconds);
}

function fetchCallHistoryMessage(PDO $pdo, int $messageId): ?array
{
    if ($messageId <= 0) {
        return null;
    }

    $stmt = $pdo->prepare("
        SELECT
            m.*,
            u.name AS sender_name,
            u.profile_image AS sender_image
        FROM messages m
        INNER JOIN users u ON u.id = m.sender_id
        WHERE m.id = ?
        LIMIT 1
    ");
    $stmt->execute([$messageId]);
    $message = $stmt->fetch(PDO::FETCH_ASSOC);

    return $message ?: null;
}
