-- =====================================================
-- Migration 005: Messaging System Enhancements
-- Adds is_active columns, typing_indicators table,
-- and conversation-related views
-- =====================================================

-- Add is_active to conversations if not present
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'conversations' AND COLUMN_NAME = 'is_active');
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE conversations ADD COLUMN is_active BOOLEAN DEFAULT TRUE',
    'SELECT "is_active column already exists in conversations table" AS status');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add is_active to conversation_participants if not present
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'conversation_participants' AND COLUMN_NAME = 'is_active');
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE conversation_participants ADD COLUMN is_active BOOLEAN DEFAULT TRUE',
    'SELECT "is_active column already exists in conversation_participants table" AS status');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- typing_indicators table
CREATE TABLE IF NOT EXISTS typing_indicators (
    id INT AUTO_INCREMENT PRIMARY KEY,
    conversation_id INT NOT NULL,
    user_id INT NOT NULL,
    started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (conversation_id) REFERENCES conversations(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_typing (conversation_id, user_id),
    INDEX idx_conversation (conversation_id),
    INDEX idx_started (started_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- v_conversation_list: efficient conversation listing with last message
CREATE OR REPLACE VIEW v_conversation_list AS
    SELECT
        c.id,
        c.type,
        c.name,
        c.avatar_url,
        c.created_at,
        c.updated_at,
        c.last_message_at,
        (
            SELECT COUNT(*)
            FROM conversation_participants cp2
            WHERE cp2.conversation_id = c.id AND cp2.is_active = TRUE
        ) AS participant_count,
        (
            SELECT m.content
            FROM messages m
            WHERE m.conversation_id = c.id AND m.is_deleted = FALSE
            ORDER BY m.created_at DESC LIMIT 1
        ) AS last_message,
        (
            SELECT m.created_at
            FROM messages m
            WHERE m.conversation_id = c.id AND m.is_deleted = FALSE
            ORDER BY m.created_at DESC LIMIT 1
        ) AS last_message_time,
        (
            SELECT m.sender_id
            FROM messages m
            WHERE m.conversation_id = c.id AND m.is_deleted = FALSE
            ORDER BY m.created_at DESC LIMIT 1
        ) AS last_sender_id
    FROM conversations c
    WHERE c.is_active = TRUE;

-- v_unread_counts: per-user unread message counts per conversation
CREATE OR REPLACE VIEW v_unread_counts AS
    SELECT
        cp.user_id,
        cp.conversation_id,
        COUNT(m.id) AS unread_count
    FROM conversation_participants cp
    LEFT JOIN messages m
        ON  m.conversation_id = cp.conversation_id
        AND m.created_at > COALESCE(cp.last_read_at, '1970-01-01 00:00:01')
        AND m.sender_id  != cp.user_id
        AND m.is_deleted  = FALSE
    WHERE cp.is_active = TRUE
    GROUP BY cp.user_id, cp.conversation_id;

SELECT 'Migration 005: Messaging Enhancements - COMPLETE' AS status;
