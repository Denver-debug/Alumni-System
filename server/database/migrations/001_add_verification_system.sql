-- ============================================
-- Alumni Verification System Migration
-- ============================================

-- Add verification columns to users table
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS verification_status ENUM('pending', 'verified', 'rejected') DEFAULT 'pending' AFTER status,
ADD COLUMN IF NOT EXISTS verified_by INT NULL AFTER verification_status,
ADD COLUMN IF NOT EXISTS verified_at TIMESTAMP NULL AFTER verified_by,
ADD COLUMN IF NOT EXISTS rejection_reason TEXT NULL AFTER verified_at,
ADD COLUMN IF NOT EXISTS verification_notes TEXT NULL AFTER rejection_reason;

-- Add indexes for better performance
ALTER TABLE users
ADD INDEX IF NOT EXISTS idx_verification_status (verification_status);

-- Add foreign key for verified_by
ALTER TABLE users
ADD CONSTRAINT fk_users_verified_by 
FOREIGN KEY (verified_by) REFERENCES users(id) ON DELETE SET NULL;

-- Update existing users to verified status (backward compatibility)
UPDATE users 
SET verification_status = 'verified', 
  verified_at = created_at 
WHERE verification_status IS NULL OR verification_status = 'pending';

-- Create verification notifications table
CREATE TABLE IF NOT EXISTS verification_notifications (
  id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT NOT NULL,
  status ENUM('pending', 'verified', 'rejected') NOT NULL,
  message TEXT,
  sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  read_at TIMESTAMP NULL,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX idx_user_read (user_id, read_at),
  INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert initial notification for existing users
INSERT INTO verification_notifications (user_id, status, message, read_at)
SELECT id, 'verified', 'Your account has been automatically verified.', NOW()
FROM users
WHERE verification_status = 'verified'
ON DUPLICATE KEY UPDATE user_id = user_id;
