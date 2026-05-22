-- ============================================
-- Messaging System Migration
-- ============================================

-- Conversations table
CREATE TABLE IF NOT EXISTS conversations (
  id INT PRIMARY KEY AUTO_INCREMENT,
  type ENUM('direct', 'group') DEFAULT 'direct',
  name VARCHAR(255) NULL COMMENT 'Group name (null for direct messages)',
  description TEXT NULL,
  avatar_url VARCHAR(500) NULL,
  created_by INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  is_active BOOLEAN DEFAULT TRUE,
  FOREIGN KEY (created_by) REFERENCES alumni(id) ON DELETE SET NULL,
  INDEX idx_type (type),
  INDEX idx_updated (updated_at DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Conversation participants
CREATE TABLE IF NOT EXISTS conversation_participants (
  id INT PRIMARY KEY AUTO_INCREMENT,
  conversation_id INT NOT NULL,
  alumni_id INT NOT NULL,
  joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  last_read_at TIMESTAMP NULL,
  is_admin BOOLEAN DEFAULT FALSE COMMENT 'For group chats',
  is_active BOOLEAN DEFAULT TRUE,
  left_at TIMESTAMP NULL,
  FOREIGN KEY (conversation_id) REFERENCES conversations(id) ON DELETE CASCADE,
  FOREIGN KEY (alumni_id) REFERENCES alumni(id) ON DELETE CASCADE,
  UNIQUE KEY unique_participant (conversation_id, alumni_id),
  INDEX idx_alumni (alumni_id),
  INDEX idx_conversation (conversation_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Messages table
CREATE TABLE IF NOT EXISTS messages (
  id INT PRIMARY KEY AUTO_INCREMENT,
  conversation_id INT NOT NULL,
  sender_id INT NOT NULL,
  message TEXT NOT NULL,
  message_type ENUM('text', 'image', 'file', 'system') DEFAULT 'text',
  attachment_url VARCHAR(500) NULL,
  attachment_name VARCHAR(255) NULL,
  attachment_size INT NULL COMMENT 'File size in bytes',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  is_deleted BOOLEAN DEFAULT FALSE,
  is_edited BOOLEAN DEFAULT FALSE,
  FOREIGN KEY (conversation_id) REFERENCES conversations(id) ON DELETE CASCADE,
  FOREIGN KEY (sender_id) REFERENCES alumni(id) ON DELETE CASCADE,
  INDEX idx_conversation_time (conversation_id, created_at DESC),
  INDEX idx_sender (sender_id),
  FULLTEXT INDEX idx_message_search (message)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Message read receipts
CREATE TABLE IF NOT EXISTS message_reads (
  id INT PRIMARY KEY AUTO_INCREMENT,
  message_id INT NOT NULL,
  alumni_id INT NOT NULL,
  read_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (message_id) REFERENCES messages(id) ON DELETE CASCADE,
  FOREIGN KEY (alumni_id) REFERENCES alumni(id) ON DELETE CASCADE,
  UNIQUE KEY unique_read (message_id, alumni_id),
  INDEX idx_message (message_id),
  INDEX idx_alumni (alumni_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Typing indicators (temporary, can be in-memory or Redis in production)
CREATE TABLE IF NOT EXISTS typing_indicators (
  id INT PRIMARY KEY AUTO_INCREMENT,
  conversation_id INT NOT NULL,
  alumni_id INT NOT NULL,
  started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (conversation_id) REFERENCES conversations(id) ON DELETE CASCADE,
  FOREIGN KEY (alumni_id) REFERENCES alumni(id) ON DELETE CASCADE,
  UNIQUE KEY unique_typing (conversation_id, alumni_id),
  INDEX idx_conversation (conversation_id),
  INDEX idx_started (started_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- View for conversation list with last message
CREATE OR REPLACE VIEW v_conversation_list AS
SELECT 
  c.id,
  c.type,
  c.name,
  c.avatar_url,
  c.created_at,
  c.updated_at,
  (SELECT COUNT(*) FROM conversation_participants WHERE conversation_id = c.id AND is_active = TRUE) as participant_count,
  (SELECT message FROM messages WHERE conversation_id = c.id AND is_deleted = FALSE ORDER BY created_at DESC LIMIT 1) as last_message,
  (SELECT created_at FROM messages WHERE conversation_id = c.id AND is_deleted = FALSE ORDER BY created_at DESC LIMIT 1) as last_message_time,
  (SELECT sender_id FROM messages WHERE conversation_id = c.id AND is_deleted = FALSE ORDER BY created_at DESC LIMIT 1) as last_sender_id
FROM conversations c
WHERE c.is_active = TRUE;

-- View for unread message counts
CREATE OR REPLACE VIEW v_unread_counts AS
SELECT 
  cp.alumni_id,
  cp.conversation_id,
  COUNT(m.id) as unread_count
FROM conversation_participants cp
LEFT JOIN messages m ON m.conversation_id = cp.conversation_id 
  AND m.created_at > COALESCE(cp.last_read_at, '1970-01-01')
  AND m.sender_id != cp.alumni_id
  AND m.is_deleted = FALSE
WHERE cp.is_active = TRUE
GROUP BY cp.alumni_id, cp.conversation_id;

-- Procedure to create or get direct conversation
DELIMITER //
CREATE PROCEDURE IF NOT EXISTS get_or_create_direct_conversation(
  IN p_alumni_id_1 INT,
  IN p_alumni_id_2 INT,
  OUT p_conversation_id INT
)
BEGIN
  -- Check if conversation already exists
  SELECT c.id INTO p_conversation_id
  FROM conversations c
  INNER JOIN conversation_participants cp1 ON c.id = cp1.conversation_id AND cp1.alumni_id = p_alumni_id_1
  INNER JOIN conversation_participants cp2 ON c.id = cp2.conversation_id AND cp2.alumni_id = p_alumni_id_2
  WHERE c.type = 'direct'
  AND c.is_active = TRUE
  LIMIT 1;
  
  -- If not exists, create new conversation
  IF p_conversation_id IS NULL THEN
    INSERT INTO conversations (type, created_by) VALUES ('direct', p_alumni_id_1);
    SET p_conversation_id = LAST_INSERT_ID();
    
    -- Add both participants
    INSERT INTO conversation_participants (conversation_id, alumni_id) 
    VALUES (p_conversation_id, p_alumni_id_1), (p_conversation_id, p_alumni_id_2);
  END IF;
END//
DELIMITER ;

-- Event to clean up old typing indicators (older than 10 seconds)
CREATE EVENT IF NOT EXISTS cleanup_typing_indicators
ON SCHEDULE EVERY 10 SECOND
STARTS CURRENT_TIMESTAMP
DO DELETE FROM typing_indicators WHERE started_at < DATE_SUB(NOW(), INTERVAL 10 SECOND);
