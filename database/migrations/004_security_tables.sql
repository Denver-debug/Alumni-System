-- =====================================================
-- Migration 004: Security Tables
-- Creates account_lockouts, login_attempts,
-- and inserts default system_settings
-- =====================================================

-- system_settings (may already exist from schema.sql - use IF NOT EXISTS)
CREATE TABLE IF NOT EXISTS system_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT NOT NULL,
    setting_type ENUM('string', 'number', 'boolean', 'json') DEFAULT 'string',
    description TEXT,
    updated_by INT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_setting_key (setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default security settings (INSERT IGNORE so existing rows are not overwritten)
INSERT IGNORE INTO system_settings (setting_key, setting_value, setting_type, description) VALUES
    ('max_login_attempts',       '5',    'number',  'Maximum failed login attempts before lockout'),
    ('lockout_duration_minutes', '30',   'number',  'Account lockout duration in minutes'),
    ('enable_login_lockout',     'true', 'boolean', 'Enable/disable login lockout feature'),
    ('session_timeout_minutes',  '120',  'number',  'Session timeout in minutes'),
    ('require_email_verification','true','boolean', 'Require email verification for new accounts');

-- login_attempts (may already exist)
CREATE TABLE IF NOT EXISTS login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    attempt_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    success BOOLEAN DEFAULT FALSE,
    failure_reason VARCHAR(255),
    INDEX idx_email_time (email, attempt_time),
    INDEX idx_ip_time (ip_address, attempt_time),
    INDEX idx_success (success, attempt_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- account_lockouts (used by security-settings.php locked-accounts endpoint)
CREATE TABLE IF NOT EXISTS account_lockouts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    locked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    unlock_at TIMESTAMP NOT NULL,
    reason VARCHAR(255) DEFAULT 'Too many failed login attempts',
    unlocked_by INT NULL,
    unlocked_at TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (unlocked_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user_unlock (user_id, unlock_at),
    INDEX idx_active (is_active, unlock_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- security_logs (may already exist from schema.sql)
CREATE TABLE IF NOT EXISTS security_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_type VARCHAR(100) NOT NULL,
    description TEXT,
    user_id INT,
    email VARCHAR(255),
    ip_address VARCHAR(45),
    user_agent VARCHAR(500),
    metadata JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_event (event_type),
    INDEX idx_user (user_id),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SELECT 'Migration 004: Security Tables - COMPLETE' AS status;
