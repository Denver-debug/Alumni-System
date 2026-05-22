-- Add optional campus admin assignment to campuses
-- Date: 2026-05-20

ALTER TABLE campuses
ADD COLUMN assigned_admin_id INT NULL AFTER status,
ADD INDEX idx_assigned_admin_id (assigned_admin_id),
ADD FOREIGN KEY (assigned_admin_id) REFERENCES users(id) ON DELETE SET NULL;
