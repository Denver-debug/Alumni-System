-- ============================================
-- Login Security System Migration
-- ============================================

-- System settings table
CREATE TABLE IF NOT EXISTS system_settings (
  id INT PRIMARY KEY AUTO_INCREMENT,
  setting_key VARCHAR(100) UNIQUE NOT NULL,
  setting_value TEXT NOT NULL,
  setting_type ENUM('string', 'number', 'boolean', 'json') DEFAULT 'string',
  description TEXT,
  updated_by INT,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_setting_key (setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default security settings
INSERT INTO system_settings (setting_key, setting_value, setting_type, description) VALUES
('max_login_attempts', '5', 'number', 'Maximum failed login attempts before lockout'),
('lockout_duration_minutes', '30', 'number', 'Account lockout duration in minutes'),
('enable_login_lockout', 'true', 'boolean', 'Enable/disable login lockout feature'),
('session_timeout_minutes', '120', 'number', 'Session timeout in minutes'),
('require_email_verification', 'true', 'boolean', 'Require email verification for new accounts')
ON DUPLICATE KEY UPDATE setting_key = setting_key;

-- Login attempts tracking
CREATE TABLE IF NOT EXISTS login_attempts (
  id INT PRIMARY KEY AUTO_INCREMENT,
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

-- Procedure to clean up old login attempts (older than 30 days)
DELIMITER //
CREATE PROCEDURE IF NOT EXISTS cleanup_old_login_attempts()
BEGIN
  DELETE FROM login_attempts
  WHERE attempt_time < DATE_SUB(NOW(), INTERVAL 30 DAY);
END//
DELIMITER ;

-- Event to run cleanup daily
CREATE EVENT IF NOT EXISTS daily_login_cleanup
ON SCHEDULE EVERY 1 DAY
STARTS CURRENT_TIMESTAMP
DO CALL cleanup_old_login_attempts();
