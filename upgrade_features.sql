USE sunson_solar;

-- Run this once when upgrading a database created before service settings,
-- customer notification preferences and notification types were added.
CREATE TABLE IF NOT EXISTS service_catalog (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(80) NOT NULL UNIQUE,
    base_price DECIMAL(12, 2) NOT NULL DEFAULT 0,
    available_days VARCHAR(120) NOT NULL DEFAULT 'Monday,Tuesday,Wednesday,Thursday,Friday',
    start_time TIME NULL,
    end_time TIME NULL,
    active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

SET @receive_reminders_exists = (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'receive_reminders'
);
SET @add_receive_reminders = IF(
    @receive_reminders_exists = 0,
    'ALTER TABLE users ADD COLUMN receive_reminders TINYINT(1) NOT NULL DEFAULT 1',
    'SELECT 1'
);
PREPARE receive_reminders_statement FROM @add_receive_reminders;
EXECUTE receive_reminders_statement;
DEALLOCATE PREPARE receive_reminders_statement;

SET @receive_promotions_exists = (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'receive_promotions'
);
SET @add_receive_promotions = IF(
    @receive_promotions_exists = 0,
    'ALTER TABLE users ADD COLUMN receive_promotions TINYINT(1) NOT NULL DEFAULT 1',
    'SELECT 1'
);
PREPARE receive_promotions_statement FROM @add_receive_promotions;
EXECUTE receive_promotions_statement;
DEALLOCATE PREPARE receive_promotions_statement;

SET @notification_type_exists = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'notifications'
      AND COLUMN_NAME = 'type'
);

SET @add_notification_type = IF(
    @notification_type_exists = 0,
    'ALTER TABLE notifications ADD COLUMN type VARCHAR(40) NOT NULL DEFAULT ''Announcement''',
    'SELECT 1'
);

PREPARE notification_type_statement FROM @add_notification_type;
EXECUTE notification_type_statement;
DEALLOCATE PREPARE notification_type_statement;

INSERT INTO service_catalog (name)
SELECT 'Consultation' WHERE NOT EXISTS (SELECT 1 FROM service_catalog WHERE name = 'Consultation');
INSERT INTO service_catalog (name)
SELECT 'Solar System Design' WHERE NOT EXISTS (SELECT 1 FROM service_catalog WHERE name = 'Solar System Design');
INSERT INTO service_catalog (name)
SELECT 'Permitting' WHERE NOT EXISTS (SELECT 1 FROM service_catalog WHERE name = 'Permitting');
INSERT INTO service_catalog (name)
SELECT 'Installation' WHERE NOT EXISTS (SELECT 1 FROM service_catalog WHERE name = 'Installation');
INSERT INTO service_catalog (name)
SELECT 'Maintenance' WHERE NOT EXISTS (SELECT 1 FROM service_catalog WHERE name = 'Maintenance');
INSERT INTO service_catalog (name)
SELECT 'Repair' WHERE NOT EXISTS (SELECT 1 FROM service_catalog WHERE name = 'Repair');
INSERT INTO service_catalog (name)
SELECT 'Monitoring' WHERE NOT EXISTS (SELECT 1 FROM service_catalog WHERE name = 'Monitoring');
