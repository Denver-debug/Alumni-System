-- Alumni Management System Database Schema
-- MySQL 8.x Compatible

-- Use Hostinger database
USE u263745868_alumni_system;

-- =====================================================
-- CORE USER TABLES
-- =====================================================

-- Users table (authentication)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    alumni_id VARCHAR(30) UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255),
    name VARCHAR(255) NOT NULL,
    role ENUM('alumni', 'admin', 'campus_admin', 'staff', 'system_admin') DEFAULT 'alumni',
    campus_id INT,
    google_id VARCHAR(255) UNIQUE,
    auth_provider ENUM('email', 'google') DEFAULT 'email',
    profile_image VARCHAR(500),
    email_verified BOOLEAN DEFAULT FALSE,
    verification_code VARCHAR(10),
    verification_expires DATETIME,
    reset_code VARCHAR(10),
    reset_expires DATETIME,
    status ENUM('active', 'inactive', 'blocked') DEFAULT 'active',
    verification_status ENUM('pending', 'verified', 'rejected') DEFAULT 'pending',
    verified_by INT NULL,
    verified_at DATETIME,
    rejection_reason TEXT,
    verification_notes TEXT,
    login_attempts INT DEFAULT 0,
    locked_until DATETIME,
    last_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_alumni_id (alumni_id),
    INDEX idx_google_id (google_id),
    INDEX idx_campus (campus_id),
    INDEX idx_status (status),
    INDEX idx_verification_status (verification_status)
) ENGINE=InnoDB;

-- Pending registrations (before email verification)
CREATE TABLE pending_registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    verification_code VARCHAR(10) NOT NULL,
    verification_expires DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_code (verification_code)
) ENGINE=InnoDB;

-- Verification notifications
CREATE TABLE verification_notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    status ENUM('pending', 'verified', 'rejected') NOT NULL,
    message TEXT,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    read_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_read (user_id, read_at),
    INDEX idx_status (status)
) ENGINE=InnoDB;

-- =====================================================
-- ORGANIZATION TABLES
-- =====================================================

-- Campuses (new - multi-campus support)
CREATE TABLE campuses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(20) NOT NULL UNIQUE,
    description TEXT,
    location VARCHAR(255),
    status ENUM('active', 'inactive') DEFAULT 'active',
    assigned_admin_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_code (code),
    INDEX idx_status (status),
    FOREIGN KEY (assigned_admin_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Colleges
CREATE TABLE colleges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(20) NOT NULL UNIQUE,
    description TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_code (code),
    INDEX idx_status (status)
) ENGINE=InnoDB;

-- Programs (linked to colleges)
CREATE TABLE programs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    college_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(20) NOT NULL UNIQUE,
    description TEXT,
    degree_type VARCHAR(100),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (college_id) REFERENCES colleges(id) ON DELETE CASCADE,
    INDEX idx_college (college_id),
    INDEX idx_code (code),
    INDEX idx_status (status)
) ENGINE=InnoDB;

-- Program Campus (many-to-many: programs available at multiple campuses)
CREATE TABLE program_campus (
    id INT AUTO_INCREMENT PRIMARY KEY,
    program_id INT NOT NULL,
    campus_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (program_id) REFERENCES programs(id) ON DELETE CASCADE,
    FOREIGN KEY (campus_id) REFERENCES campuses(id) ON DELETE CASCADE,
    UNIQUE KEY unique_program_campus (program_id, campus_id),
    INDEX idx_program (program_id),
    INDEX idx_campus (campus_id)
) ENGINE=InnoDB;

-- Sections (linked to programs)
CREATE TABLE sections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    program_id INT NOT NULL,
    campus_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    batch_year INT NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (program_id) REFERENCES programs(id) ON DELETE CASCADE,
    FOREIGN KEY (campus_id) REFERENCES campuses(id) ON DELETE CASCADE,
    INDEX idx_program (program_id),
    INDEX idx_campus (campus_id),
    INDEX idx_batch (batch_year),
    UNIQUE KEY unique_section (program_id, campus_id, name, batch_year)
) ENGINE=InnoDB;

-- =====================================================
-- ALUMNI PROFILE TABLES
-- =====================================================

-- Alumni profiles (extended user info)
CREATE TABLE alumni_profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    campus_id INT,
    college_id INT,
    program_id INT,
    section_id INT,
    batch_year INT,
    graduation_year INT,
    student_id VARCHAR(50),
    
    -- Personal Info
    first_name VARCHAR(100),
    middle_name VARCHAR(100),
    last_name VARCHAR(100),
    suffix VARCHAR(20),
    nickname VARCHAR(50),
    gender ENUM('male', 'female', 'other', 'prefer_not_to_say'),
    birthdate DATE,
    civil_status ENUM('single', 'married', 'widowed', 'separated', 'divorced'),
    nationality VARCHAR(100),
    religion VARCHAR(100),
    
    -- Contact Info
    phone VARCHAR(20),
    mobile VARCHAR(20),
    address_street VARCHAR(255),
    address_barangay VARCHAR(100),
    address_city VARCHAR(100),
    address_province VARCHAR(100),
    address_region VARCHAR(100),
    address_zip VARCHAR(10),
    address_country VARCHAR(100) DEFAULT 'Philippines',
    
    -- Employment Info
    employment_status ENUM('employed', 'self_employed', 'unemployed', 'student', 'retired'),
    current_employer VARCHAR(255),
    job_title VARCHAR(255),
    company_address VARCHAR(500),
    industry VARCHAR(100),
    monthly_salary_range VARCHAR(50),
    
    -- Social Media
    linkedin_url VARCHAR(255),
    facebook_url VARCHAR(255),
    twitter_url VARCHAR(255),
    instagram_url VARCHAR(255),
    
    -- Gamification
    total_points INT DEFAULT 0,
    badge_level ENUM('bronze', 'silver', 'gold', 'platinum', 'diamond') DEFAULT 'bronze',
    
    -- Profile completeness
    profile_completed BOOLEAN DEFAULT FALSE,
    profile_completed_at DATETIME,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (campus_id) REFERENCES campuses(id) ON DELETE SET NULL,
    FOREIGN KEY (college_id) REFERENCES colleges(id) ON DELETE SET NULL,
    FOREIGN KEY (program_id) REFERENCES programs(id) ON DELETE SET NULL,
    FOREIGN KEY (section_id) REFERENCES sections(id) ON DELETE SET NULL,
    
    INDEX idx_user (user_id),
    INDEX idx_campus (campus_id),
    INDEX idx_college (college_id),
    INDEX idx_program (program_id),
    INDEX idx_section (section_id),
    INDEX idx_batch (batch_year),
    INDEX idx_points (total_points)
) ENGINE=InnoDB;

-- =====================================================
-- EVENTS & GAMIFICATION TABLES
-- =====================================================

-- Events
CREATE TABLE events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    event_date DATE NOT NULL,
    event_time TIME,
    end_date DATE,
    end_time TIME,
    location VARCHAR(500),
    venue_type ENUM('physical', 'online', 'hybrid') DEFAULT 'physical',
    meeting_link VARCHAR(500),
    event_type ENUM('seminar', 'reunion', 'workshop', 'webinar', 'networking', 'career_fair', 'sports', 'cultural', 'community_service', 'other') DEFAULT 'other',
    
    -- Target audience
    target_type ENUM('all', 'college', 'program', 'section', 'batch_year') DEFAULT 'all',
    target_id INT,
    target_batch_year INT,
    
    -- Attendance settings
    max_attendees INT,
    registration_deadline DATETIME,
    attendance_code VARCHAR(20),
    qr_code_data VARCHAR(500),
    
    -- Points
    points_reward INT DEFAULT 0,
    
    -- Images
    cover_image VARCHAR(500),
    
    -- Status
    status ENUM('draft', 'upcoming', 'ongoing', 'completed', 'cancelled') DEFAULT 'draft',
    
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_date (event_date),
    INDEX idx_status (status),
    INDEX idx_type (event_type),
    INDEX idx_target (target_type, target_id)
) ENGINE=InnoDB;

-- Event RSVPs
CREATE TABLE event_rsvps (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    user_id INT NOT NULL,
    status ENUM('going', 'maybe', 'not_going') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_rsvp (event_id, user_id),
    INDEX idx_event (event_id),
    INDEX idx_user (user_id),
    INDEX idx_status (status)
) ENGINE=InnoDB;

-- Event attendances (actual check-ins)
CREATE TABLE event_attendances (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    user_id INT NOT NULL,
    check_in_method ENUM('qr_code', 'attendance_code', 'manual') NOT NULL,
    check_in_time DATETIME NOT NULL,
    check_out_time DATETIME,
    points_awarded INT DEFAULT 0,
    verified_by INT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (verified_by) REFERENCES users(id) ON DELETE SET NULL,
    UNIQUE KEY unique_attendance (event_id, user_id),
    INDEX idx_event (event_id),
    INDEX idx_user (user_id)
) ENGINE=InnoDB;

-- Point transactions
CREATE TABLE point_transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    points INT NOT NULL,
    type ENUM('earned', 'redeemed', 'bonus', 'penalty', 'expired') NOT NULL,
    source ENUM('event_attendance', 'profile_completion', 'first_login', 'referral', 'reward_redemption', 'admin_bonus', 'other') NOT NULL,
    reference_id INT,
    reference_type VARCHAR(50),
    description VARCHAR(500),
    balance_after INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_type (type),
    INDEX idx_source (source),
    INDEX idx_created (created_at)
) ENGINE=InnoDB;

-- Rewards catalog
CREATE TABLE rewards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    points_cost INT NOT NULL,
    quantity_available INT,
    quantity_redeemed INT DEFAULT 0,
    reward_type ENUM('physical', 'digital', 'discount', 'access', 'other') DEFAULT 'other',
    image_url VARCHAR(500),
    terms_conditions TEXT,
    valid_from DATE,
    valid_until DATE,
    status ENUM('active', 'inactive', 'out_of_stock') DEFAULT 'active',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_points (points_cost)
) ENGINE=InnoDB;

-- Reward redemptions
CREATE TABLE reward_redemptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    reward_id INT NOT NULL,
    points_spent INT NOT NULL,
    status ENUM('pending', 'approved', 'claimed', 'rejected', 'expired') DEFAULT 'pending',
    claim_code VARCHAR(50),
    approved_by INT,
    approved_at DATETIME,
    claimed_at DATETIME,
    rejection_reason VARCHAR(500),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (reward_id) REFERENCES rewards(id) ON DELETE CASCADE,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user (user_id),
    INDEX idx_reward (reward_id),
    INDEX idx_status (status)
) ENGINE=InnoDB;

-- =====================================================
-- MESSAGING TABLES
-- =====================================================

-- Conversations
CREATE TABLE conversations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('personal', 'group', 'section', 'program', 'college') NOT NULL,
    name VARCHAR(255),
    description TEXT,
    reference_id INT,
    avatar_url VARCHAR(500),
    created_by INT,
    last_message_at DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_type (type),
    INDEX idx_reference (reference_id),
    INDEX idx_last_message (last_message_at)
) ENGINE=InnoDB;

-- Conversation participants
CREATE TABLE conversation_participants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    conversation_id INT NOT NULL,
    user_id INT NOT NULL,
    role ENUM('member', 'admin', 'owner') DEFAULT 'member',
    nickname VARCHAR(100),
    is_muted BOOLEAN DEFAULT FALSE,
    last_read_at DATETIME,
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    left_at DATETIME,
    
    FOREIGN KEY (conversation_id) REFERENCES conversations(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_participant (conversation_id, user_id),
    INDEX idx_conversation (conversation_id),
    INDEX idx_user (user_id)
) ENGINE=InnoDB;

-- Messages
CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    conversation_id INT NOT NULL,
    sender_id INT NOT NULL,
    content TEXT NOT NULL,
    message_type ENUM('text', 'image', 'file', 'system') DEFAULT 'text',
    file_url VARCHAR(500),
    file_name VARCHAR(255),
    file_size INT,
    reply_to_id INT,
    is_edited BOOLEAN DEFAULT FALSE,
    edited_at DATETIME,
    is_deleted BOOLEAN DEFAULT FALSE,
    deleted_at DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (conversation_id) REFERENCES conversations(id) ON DELETE CASCADE,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (reply_to_id) REFERENCES messages(id) ON DELETE SET NULL,
    INDEX idx_conversation (conversation_id),
    INDEX idx_sender (sender_id),
    INDEX idx_created (created_at)
) ENGINE=InnoDB;

-- Message read receipts
CREATE TABLE message_reads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    message_id INT NOT NULL,
    user_id INT NOT NULL,
    read_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (message_id) REFERENCES messages(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_read (message_id, user_id),
    INDEX idx_message (message_id),
    INDEX idx_user (user_id)
) ENGINE=InnoDB;

-- =====================================================
-- ANNOUNCEMENTS TABLES
-- =====================================================

-- Announcements
CREATE TABLE announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    excerpt VARCHAR(500),
    cover_image VARCHAR(500),
    
    -- Targeting
    target_type ENUM('all', 'college', 'program', 'section', 'batch_year') DEFAULT 'all',
    target_id INT,
    target_batch_year INT,
    
    -- Publishing
    priority ENUM('low', 'normal', 'high', 'urgent') DEFAULT 'normal',
    is_pinned BOOLEAN DEFAULT FALSE,
    publish_date DATETIME,
    expire_date DATETIME,
    status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    
    -- Engagement
    views_count INT DEFAULT 0,
    
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_target (target_type, target_id),
    INDEX idx_publish (publish_date),
    INDEX idx_pinned (is_pinned)
) ENGINE=InnoDB;

-- Announcement reads (for tracking who has seen it)
CREATE TABLE announcement_reads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    announcement_id INT NOT NULL,
    user_id INT NOT NULL,
    read_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (announcement_id) REFERENCES announcements(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_read (announcement_id, user_id),
    INDEX idx_announcement (announcement_id)
) ENGINE=InnoDB;

-- =====================================================
-- FORM BUILDER TABLES
-- =====================================================

-- Form fields (dynamic custom fields)
CREATE TABLE form_fields (
    id INT AUTO_INCREMENT PRIMARY KEY,
    field_name VARCHAR(64) NOT NULL UNIQUE,
    field_label VARCHAR(255) NOT NULL,
    field_type ENUM('text', 'textarea', 'email', 'phone', 'number', 'date', 'select', 'multiselect', 'checkbox', 'radio', 'file', 'image', 'address', 'url') NOT NULL,
    field_options JSON,
    placeholder VARCHAR(255),
    help_text VARCHAR(500),
    validation_rules JSON,
    form_section ENUM('personal', 'contact', 'education', 'employment', 'social', 'additional') DEFAULT 'additional',
    field_group VARCHAR(100),
    display_order INT DEFAULT 0,
    column_width ENUM('full', 'half', 'third') DEFAULT 'full',
    is_required BOOLEAN DEFAULT FALSE,
    is_builtin BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    conditional_config JSON,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_section (form_section),
    INDEX idx_order (display_order),
    INDEX idx_active (is_active)
) ENGINE=InnoDB;

-- =====================================================
-- SETTINGS TABLES
-- =====================================================

-- System settings (security and general configuration)
CREATE TABLE system_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT NOT NULL,
    setting_type ENUM('string', 'number', 'boolean', 'json') DEFAULT 'string',
    description TEXT,
    updated_by INT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_setting_key (setting_key)
) ENGINE=InnoDB;

-- Login attempts tracking
CREATE TABLE login_attempts (
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
) ENGINE=InnoDB;

-- Site content (CMS)
CREATE TABLE site_content (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section ENUM('header', 'footer', 'about', 'contact', 'home', 'announcement', 'login', 'register', 'settings', 'other') NOT NULL,
    content_key VARCHAR(100) NOT NULL,
    title VARCHAR(255),
    content_value TEXT,
    media_url VARCHAR(500),
    content_type ENUM('text', 'html', 'image_url', 'video_url', 'link', 'date', 'json') DEFAULT 'text',
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    styles TEXT,
    updated_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL,
    UNIQUE KEY unique_content (section, content_key),
    INDEX idx_section (section),
    INDEX idx_active (is_active)
) ENGINE=InnoDB;

-- Theme settings
CREATE TABLE theme_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    setting_type ENUM('color', 'font', 'size', 'image', 'text', 'boolean', 'json') DEFAULT 'text',
    description VARCHAR(500),
    updated_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_key (setting_key)
) ENGINE=InnoDB;

-- Email settings
CREATE TABLE email_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    setting_type ENUM('text', 'email', 'url', 'html', 'json') DEFAULT 'text',
    description VARCHAR(500),
    updated_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_key (setting_key)
) ENGINE=InnoDB;

-- Email templates
CREATE TABLE email_templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    template_key VARCHAR(100) NOT NULL UNIQUE,
    template_name VARCHAR(255) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    body TEXT NOT NULL,
    available_variables JSON,
    description VARCHAR(500),
    is_active BOOLEAN DEFAULT TRUE,
    updated_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_key (template_key),
    INDEX idx_active (is_active)
) ENGINE=InnoDB;

-- =====================================================
-- ADMIN & AUDIT TABLES
-- =====================================================

-- Admin activity log
CREATE TABLE admin_activities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    activity_type VARCHAR(100) NOT NULL,
    description TEXT,
    target_type VARCHAR(50),
    target_id INT,
    old_values JSON,
    new_values JSON,
    ip_address VARCHAR(45),
    user_agent VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_type (activity_type),
    INDEX idx_created (created_at)
) ENGINE=InnoDB;

-- Security logs
CREATE TABLE security_logs (
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
) ENGINE=InnoDB;

-- Alumni ID sequence tracking
CREATE TABLE alumni_id_sequences (
    id INT AUTO_INCREMENT PRIMARY KEY,
    year INT NOT NULL,
    college_code VARCHAR(20) NOT NULL,
    last_sequence INT DEFAULT 0,
    
    UNIQUE KEY unique_seq (year, college_code),
    INDEX idx_year_college (year, college_code)
) ENGINE=InnoDB;

-- =====================================================
-- DEFAULT DATA
-- =====================================================

-- Insert default admin user (password: Admin@123)
INSERT INTO users (email, password, name, role, email_verified, status) VALUES
('admin@alumni.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'system_admin', TRUE, 'active');

-- Insert default theme settings
INSERT INTO theme_settings (setting_key, setting_value, setting_type, description) VALUES
('primary_color', '#1e40af', 'color', 'Primary brand color'),
('secondary_color', '#64748b', 'color', 'Secondary brand color'),
('accent_color', '#f59e0b', 'color', 'Accent color for highlights'),
('logo_url', '/assets/images/logo.png', 'image', 'Main logo URL'),
('institution_name', 'Alumni Management System', 'text', 'Institution name'),
('institution_short_name', 'AMS', 'text', 'Institution abbreviation'),
('footer_text', '© 2024 Alumni Management System. All rights reserved.', 'text', 'Footer copyright text'),
('font_family', 'Inter, sans-serif', 'font', 'Primary font family');

-- Insert default system settings
INSERT INTO system_settings (setting_key, setting_value, setting_type, description) VALUES
('max_login_attempts', '5', 'number', 'Maximum failed login attempts before lockout'),
('lockout_duration_minutes', '30', 'number', 'Account lockout duration in minutes'),
('enable_login_lockout', 'true', 'boolean', 'Enable/disable login lockout feature'),
('session_timeout_minutes', '120', 'number', 'Session timeout in minutes'),
('require_email_verification', 'true', 'boolean', 'Require email verification for new accounts');

-- Insert default email templates
INSERT INTO email_templates (template_key, template_name, subject, body, available_variables, is_active) VALUES
('verification', 'Email Verification', 'Verify Your Email Address', '<h2>Welcome to Alumni System!</h2><p>Hi {{name}},</p><p>Your verification code is: <strong>{{code}}</strong></p><p>This code expires in {{expiration_minutes}} minutes.</p>', '["name", "code", "expiration_minutes"]', TRUE),
('password_reset', 'Password Reset', 'Reset Your Password', '<h2>Password Reset Request</h2><p>Hi {{name}},</p><p>Your password reset code is: <strong>{{code}}</strong></p><p>This code expires in {{expiration_minutes}} minutes.</p><p>If you did not request this, please ignore this email.</p>', '["name", "code", "expiration_minutes"]', TRUE),
('welcome', 'Welcome Email', 'Welcome to the Alumni Network!', '<h2>Welcome, {{name}}!</h2><p>Thank you for joining our alumni network.</p><p>Your Alumni ID is: <strong>{{alumni_id}}</strong></p><p>Start by completing your profile to earn points!</p>', '["name", "alumni_id"]', TRUE),
('event_reminder', 'Event Reminder', 'Reminder: {{event_title}}', '<h2>Event Reminder</h2><p>Hi {{name}},</p><p>This is a reminder about the upcoming event:</p><p><strong>{{event_title}}</strong></p><p>Date: {{event_date}}</p><p>Location: {{location}}</p>', '["name", "event_title", "event_date", "location"]', TRUE),
('points_earned', 'Points Earned', 'You Earned {{points}} Points!', '<h2>Congratulations!</h2><p>Hi {{name}},</p><p>You have earned <strong>{{points}} points</strong> for {{reason}}.</p><p>Your total points: {{total_points}}</p>', '["name", "points", "reason", "total_points"]', TRUE);

-- Insert default email settings
INSERT INTO email_settings (setting_key, setting_value, setting_type, description) VALUES
('contact_email', 'alumni@university.edu', 'email', 'Main contact email'),
('contact_phone', '+1 234 567 8900', 'text', 'Contact phone number'),
('smtp_host', 'smtp.gmail.com', 'text', 'SMTP server host'),
('smtp_port', '587', 'text', 'SMTP server port'),
('smtp_encryption', 'tls', 'text', 'SMTP encryption type'),
('from_name', 'Alumni Management System', 'text', 'Email sender name'),
('from_email', 'noreply@alumni.edu', 'email', 'Email sender address');

-- Insert default site content
INSERT INTO site_content (section, content_key, title, content_value, content_type, is_active) VALUES
('header', 'main_title', 'Main Title', 'Alumni Management System', 'text', TRUE),
('header', 'tagline', 'Tagline', 'Stay Connected. Stay Engaged.', 'text', TRUE),
('home', 'welcome_message', 'Welcome Message', 'Welcome to our alumni community! Connect with fellow graduates, attend events, and stay updated.', 'text', TRUE),
('footer', 'copyright', 'Copyright', '© 2024 Alumni Management System', 'text', TRUE),
('about', 'description', 'About Description', 'Our alumni management system helps graduates stay connected with their alma mater.', 'html', TRUE);

-- Insert point configuration
INSERT INTO site_content (section, content_key, title, content_value, content_type, is_active) VALUES
('settings', 'alumni_id_prefix', 'Alumni ID Prefix', 'ALM', 'text', TRUE),
('settings', 'points_profile_completion', 'Profile Completion Points', '50', 'text', TRUE),
('settings', 'points_first_login', 'First Login Points', '10', 'text', TRUE),
('settings', 'points_referral', 'Referral Points', '25', 'text', TRUE),
('settings', 'points_profile_update', 'Profile Update Points', '5', 'text', TRUE);

-- Insert badge level thresholds
INSERT INTO site_content (section, content_key, title, content_value, content_type, is_active) VALUES
('settings', 'badge_bronze_min', 'Bronze Badge Min Points', '0', 'text', TRUE),
('settings', 'badge_silver_min', 'Silver Badge Min Points', '100', 'text', TRUE),
('settings', 'badge_gold_min', 'Gold Badge Min Points', '500', 'text', TRUE),
('settings', 'badge_platinum_min', 'Platinum Badge Min Points', '1000', 'text', TRUE),
('settings', 'badge_diamond_min', 'Diamond Badge Min Points', '5000', 'text', TRUE);

-- =====================================================
-- DEFAULT DATA
-- =====================================================

-- Password: password (for testing - change immediately after first login!)
-- Hash is for 'password' using bcrypt
-- Note: Admins do NOT get alumni_id (NULL) - only alumni get IDs
INSERT INTO users (email, password, name, role, auth_provider, email_verified, status) VALUES
('admin@minsu.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'system_admin', 'email', TRUE, 'active');

-- Insert sample campuses
INSERT INTO campuses (name, code, description, location, status) VALUES
('Bongabong Campus', 'BBC', 'Main campus in Bongabong', 'Labasan, Bongabong, Oriental Mindoro', 'active'),
('Calapan Campus', 'CPC', 'Calapan City campus', 'Calapan City, Oriental Mindoro', 'active');

-- Insert a sample college, program, section for testing
INSERT INTO colleges (name, code, description, status) VALUES
('College of Computer Studies', 'CCS', 'Information Technology and Computer Science programs', 'active');

INSERT INTO programs (college_id, name, code, description, degree_type, status) VALUES
(1, 'Bachelor of Science in Information Technology', 'BSIT', 'Information Technology degree program', 'Bachelor', 'active'),
(1, 'Bachelor of Science in Computer Science', 'BSCS', 'Computer Science degree program', 'Bachelor', 'active');

-- Link programs to campuses
INSERT INTO program_campus (program_id, campus_id) VALUES
(1, 1), -- BSIT at Bongabong
(2, 1), -- BSCS at Bongabong
(1, 2), -- BSIT at Calapan
(2, 2); -- BSCS at Calapan

-- Insert sections with campus_id
INSERT INTO sections (program_id, campus_id, name, batch_year, status) VALUES
(1, 1, 'Section A', 2024, 'active'),
(1, 1, 'Section B', 2024, 'active'),
(2, 1, 'Section A', 2024, 'active');
