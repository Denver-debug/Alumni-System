-- Reset Admin User Script
-- Run this in MySQL to reset the admin account

USE alumni_system;

-- First, delete any existing admin user (in case it has wrong data)
DELETE FROM users WHERE email = 'admin@minsu.edu.ph' OR email = 'admin@alumni.edu' OR role IN ('admin', 'system_admin');

-- Insert fresh admin user
-- Password: password (change immediately after first login!)
-- The hash below is the standard bcrypt hash for 'password'
INSERT INTO users (email, password, name, role, auth_provider, email_verified, status) VALUES
('admin@minsu.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'system_admin', 'email', TRUE, 'active');

-- Verify the user was created
SELECT id, email, name, role, status FROM users WHERE role IN ('admin', 'system_admin');
