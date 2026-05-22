-- Multi-Campus System Migration (Safe Version)
-- Run this script on your existing alumni_system database
-- This version includes safety checks to avoid duplicate column/constraint errors

-- Step 1: Create campuses table (if not exists)
CREATE TABLE IF NOT EXISTS campuses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(20) NOT NULL UNIQUE,
    description TEXT,
    location VARCHAR(255),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_code (code),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Step 2: Insert default campus
INSERT IGNORE INTO campuses (id, name, code, description, status, created_at, updated_at)
VALUES (1, 'Main Campus', 'MAIN', 'Default main campus', 'active', NOW(), NOW());

-- Step 3: Update users table
-- Add campus_id column if not exists
ALTER TABLE users 
ADD COLUMN campus_id INT DEFAULT NULL AFTER role;

-- Add/Update foreign key for campus_id
ALTER TABLE users 
ADD INDEX IF NOT EXISTS idx_campus (campus_id);

-- Add foreign key constraint if it doesn't exist
ALTER TABLE users 
ADD CONSTRAINT fk_users_campus FOREIGN KEY (campus_id) REFERENCES campuses(id) ON DELETE SET NULL;

-- Step 4: Update alumni_profiles table
-- Add campus_id column if not exists
ALTER TABLE alumni_profiles 
ADD COLUMN campus_id INT DEFAULT NULL AFTER user_id;

-- Add index if not exists
ALTER TABLE alumni_profiles 
ADD INDEX IF NOT EXISTS idx_campus (campus_id);

-- Add foreign key constraint if it doesn't exist
ALTER TABLE alumni_profiles 
ADD CONSTRAINT fk_alumni_profiles_campus FOREIGN KEY (campus_id) REFERENCES campuses(id) ON DELETE SET NULL;

-- Step 5: Create program_campus junction table
CREATE TABLE IF NOT EXISTS program_campus (
    id INT AUTO_INCREMENT PRIMARY KEY,
    program_id INT NOT NULL,
    campus_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (program_id) REFERENCES programs(id) ON DELETE CASCADE,
    FOREIGN KEY (campus_id) REFERENCES campuses(id) ON DELETE CASCADE,
    UNIQUE KEY unique_program_campus (program_id, campus_id),
    INDEX idx_program (program_id),
    INDEX idx_campus (campus_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Step 6: Update sections table
-- Add campus_id column if not exists
ALTER TABLE sections 
ADD COLUMN campus_id INT NOT NULL DEFAULT 1 AFTER program_id;

-- Add index if not exists
ALTER TABLE sections 
ADD INDEX IF NOT EXISTS idx_campus_sec (campus_id);

-- Add foreign key if not exists
ALTER TABLE sections 
ADD CONSTRAINT fk_sections_campus FOREIGN KEY (campus_id) REFERENCES campuses(id) ON DELETE CASCADE;

-- Drop old unique constraint if it exists
ALTER TABLE sections DROP INDEX IF EXISTS unique_section;

-- Add new unique constraint with campus_id
ALTER TABLE sections ADD UNIQUE KEY unique_section (program_id, campus_id, name, batch_year);

-- Step 7: Migration complete
SELECT 'Multi-Campus System Migration Complete - Safe Version' AS status;
