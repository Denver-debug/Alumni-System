-- =====================================================
-- PRE-IMPORT SCRIPT FOR HOSTINGER (UPDATED)
-- Run this BEFORE importing schema.sql
-- =====================================================

USE u263745868_alumni_system;

-- Disable foreign key checks
SET FOREIGN_KEY_CHECKS = 0;

-- Drop all existing tables for a clean start
DROP TABLE IF EXISTS alumni_id_sequences;
DROP TABLE IF EXISTS security_logs;
DROP TABLE IF EXISTS admin_activities;
DROP TABLE IF EXISTS email_templates;
DROP TABLE IF EXISTS email_settings;
DROP TABLE IF EXISTS theme_settings;
DROP TABLE IF EXISTS site_content;
DROP TABLE IF EXISTS login_attempts;
DROP TABLE IF EXISTS system_settings;
DROP TABLE IF EXISTS form_fields;
DROP TABLE IF EXISTS announcement_reads;
DROP TABLE IF EXISTS announcements;
DROP TABLE IF EXISTS message_reads;
DROP TABLE IF EXISTS messages;
DROP TABLE IF EXISTS conversation_participants;
DROP TABLE IF EXISTS conversations;
DROP TABLE IF EXISTS reward_redemptions;
DROP TABLE IF EXISTS rewards;
DROP TABLE IF EXISTS point_transactions;
DROP TABLE IF EXISTS event_attendances;
DROP TABLE IF EXISTS event_rsvps;
DROP TABLE IF EXISTS events;
DROP TABLE IF EXISTS alumni_profiles;
DROP TABLE IF EXISTS sections;
DROP TABLE IF EXISTS program_campus;
DROP TABLE IF EXISTS programs;
DROP TABLE IF EXISTS colleges;
DROP TABLE IF EXISTS campuses;
DROP TABLE IF EXISTS verification_notifications;
DROP TABLE IF EXISTS pending_registrations;
DROP TABLE IF EXISTS users;

-- Set SQL mode for compatibility
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';

-- Set timezone
SET time_zone = '+00:00';

-- Verify tables are dropped
SELECT 'All tables dropped. Ready for import.' as status;

-- Now you can import schema.sql through phpMyAdmin Import tab
-- After import completes, run 01_post_import.sql
