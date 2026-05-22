-- Migration: Add campus support to events and announcements
-- Date: 2026-05-19

-- Add campus_id to events table
ALTER TABLE events 
ADD COLUMN campus_id INT NULL AFTER created_by,
ADD INDEX idx_campus_id (campus_id),
ADD FOREIGN KEY (campus_id) REFERENCES campuses(id) ON DELETE SET NULL;

-- Add campus_id to announcements table
ALTER TABLE announcements 
ADD COLUMN campus_id INT NULL AFTER created_by,
ADD INDEX idx_campus_id (campus_id),
ADD FOREIGN KEY (campus_id) REFERENCES campuses(id) ON DELETE SET NULL;

-- Create event_campuses junction table for multi-campus events
CREATE TABLE IF NOT EXISTS event_campuses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    campus_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (campus_id) REFERENCES campuses(id) ON DELETE CASCADE,
    UNIQUE KEY unique_event_campus (event_id, campus_id),
    INDEX idx_event (event_id),
    INDEX idx_campus (campus_id)
) ENGINE=InnoDB;

-- Create announcement_campuses junction table for multi-campus announcements
CREATE TABLE IF NOT EXISTS announcement_campuses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    announcement_id INT NOT NULL,
    campus_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (announcement_id) REFERENCES announcements(id) ON DELETE CASCADE,
    FOREIGN KEY (campus_id) REFERENCES campuses(id) ON DELETE CASCADE,
    UNIQUE KEY unique_announcement_campus (announcement_id, campus_id),
    INDEX idx_announcement (announcement_id),
    INDEX idx_campus (campus_id)
) ENGINE=InnoDB;

-- Update existing events to use campus_id from creator's campus
UPDATE events e
INNER JOIN users u ON e.created_by = u.id
SET e.campus_id = u.campus_id
WHERE u.campus_id IS NOT NULL;

-- Update existing announcements to use campus_id from creator's campus
UPDATE announcements a
INNER JOIN users u ON a.created_by = u.id
SET a.campus_id = u.campus_id
WHERE u.campus_id IS NOT NULL;

-- Populate event_campuses from existing campus_id
INSERT INTO event_campuses (event_id, campus_id)
SELECT id, campus_id FROM events WHERE campus_id IS NOT NULL
ON DUPLICATE KEY UPDATE event_id = event_id;

-- Populate announcement_campuses from existing campus_id
INSERT INTO announcement_campuses (announcement_id, campus_id)
SELECT id, campus_id FROM announcements WHERE campus_id IS NOT NULL
ON DUPLICATE KEY UPDATE announcement_id = announcement_id;
