-- =====================================================
-- Migration 002: Verification System
-- Ensures verification columns exist on USERS table
-- and creates verification_notifications with user_id
-- =====================================================

-- Add verification columns to users table if not present
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'verification_status');
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE users ADD COLUMN verification_status ENUM(''pending'', ''verified'', ''rejected'') DEFAULT ''pending''',
    'SELECT "verification_status column already exists in users table" AS status');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'verified_by');
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE users ADD COLUMN verified_by INT NULL',
    'SELECT "verified_by column already exists in users table" AS status');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'verified_at');
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE users ADD COLUMN verified_at DATETIME NULL',
    'SELECT "verified_at column already exists in users table" AS status');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'rejection_reason');
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE users ADD COLUMN rejection_reason TEXT NULL',
    'SELECT "rejection_reason column already exists in users table" AS status');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'verification_notes');
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE users ADD COLUMN verification_notes TEXT NULL',
    'SELECT "verification_notes column already exists in users table" AS status');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add index if not exists
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND INDEX_NAME = 'idx_verification_status');
SET @sql = IF(@idx_exists = 0,
    'ALTER TABLE users ADD INDEX idx_verification_status (verification_status)',
    'SELECT "idx_verification_status index already exists on users table" AS status');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add foreign key for verified_by (may already exist via schema)
-- Use IF NOT EXISTS pattern via a workaround
SET @fk_exists = (
    SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
    WHERE CONSTRAINT_SCHEMA = DATABASE()
    AND TABLE_NAME = 'users'
    AND CONSTRAINT_NAME = 'fk_users_verified_by'
    AND CONSTRAINT_TYPE = 'FOREIGN KEY'
);
-- Only add FK if it doesn't exist
SET @sql = IF(@fk_exists = 0,
    'ALTER TABLE users ADD CONSTRAINT fk_users_verified_by FOREIGN KEY (verified_by) REFERENCES users(id) ON DELETE SET NULL',
    'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Create verification_notifications table (uses user_id, matches schema.sql)
CREATE TABLE IF NOT EXISTS verification_notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    status ENUM('pending', 'verified', 'rejected') NOT NULL,
    message TEXT,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    read_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_read (user_id, read_at),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Set existing alumni to 'verified' if they have no status set
-- (only if this is a fresh migration on an existing system)
UPDATE users
SET verification_status = 'verified',
    verified_at = created_at
WHERE role = 'alumni'
  AND (verification_status IS NULL OR verification_status = '');

SELECT 'Migration 002: Verification System - COMPLETE' AS status;
