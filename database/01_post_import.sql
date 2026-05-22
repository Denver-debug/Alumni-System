-- =====================================================
-- POST-IMPORT SCRIPT FOR HOSTINGER
-- Run this AFTER importing schema.sql
-- =====================================================

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- Verify tables were created
SELECT COUNT(*) as total_tables 
FROM information_schema.tables 
WHERE table_schema = 'u263745868_alumni_system';

-- Show all tables
SHOW TABLES;

-- Verify foreign keys are working
SELECT 
    TABLE_NAME,
    CONSTRAINT_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM
    information_schema.KEY_COLUMN_USAGE
WHERE
    REFERENCED_TABLE_SCHEMA = 'u263745868_alumni_system'
    AND TABLE_NAME = 'users'
ORDER BY TABLE_NAME, CONSTRAINT_NAME;
