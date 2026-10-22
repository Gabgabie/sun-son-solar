CREATE DATABASE IF NOT EXISTS sunson_solar
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE sunson_solar;

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(80) NOT NULL,
    last_name VARCHAR(80) NOT NULL,
    middle_name VARCHAR(80) NULL,
    birthdate DATE NULL,
    gender VARCHAR(30) NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    phone VARCHAR(30) NULL,
    address VARCHAR(255) NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role VARCHAR(30) NOT NULL DEFAULT 'customer',
    department VARCHAR(80) NULL,
    receive_reminders TINYINT(1) NOT NULL DEFAULT 1,
    receive_promotions TINYINT(1) NOT NULL DEFAULT 1,
    active TINYINT(1) NOT NULL DEFAULT 1,
    must_change_password TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS service_requests (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_id INT UNSIGNED NOT NULL,
    service_type VARCHAR(80) NOT NULL,
    preferred_date DATE NOT NULL,
    preferred_time TIME NULL,
    notes TEXT NULL,
    status ENUM('Pending', 'Approved', 'Declined', 'Completed', 'Cancelled') NOT NULL DEFAULT 'Pending',
    assigned_technician_id INT UNSIGNED NULL,
    admin_notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES users(id),
    FOREIGN KEY (assigned_technician_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS checkins (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id INT UNSIGNED NOT NULL,
    service_request_id INT UNSIGNED NULL,
    latitude DECIMAL(10, 7) NULL,
    longitude DECIMAL(10, 7) NULL,
    checked_in_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES users(id),
    FOREIGN KEY (service_request_id) REFERENCES service_requests(id)
);

CREATE TABLE IF NOT EXISTS products (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    category VARCHAR(80) NOT NULL,
    description TEXT NULL,
    price DECIMAL(12, 2) NOT NULL DEFAULT 0,
    stock INT NOT NULL DEFAULT 0,
    active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

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

CREATE TABLE IF NOT EXISTS notifications (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(160) NOT NULL,
    message TEXT NOT NULL,
    audience ENUM('all', 'customers', 'employees') NOT NULL DEFAULT 'all',
    type VARCHAR(40) NOT NULL DEFAULT 'Announcement',
    created_by INT UNSIGNED NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS user_notifications (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    notification_id INT UNSIGNED NOT NULL,
    read_at DATETIME NULL,
    UNIQUE KEY uq_user_notification (user_id, notification_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (notification_id) REFERENCES notifications(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS service_reports (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    service_request_id INT UNSIGNED NULL,
    employee_id INT UNSIGNED NOT NULL,
    issue_type VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    status ENUM('Open', 'In Review', 'Resolved') NOT NULL DEFAULT 'Open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (service_request_id) REFERENCES service_requests(id),
    FOREIGN KEY (employee_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS support_tickets (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_id INT UNSIGNED NOT NULL,
    subject VARCHAR(160) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('Open', 'In Progress', 'Closed') NOT NULL DEFAULT 'Open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS payments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    service_request_id INT UNSIGNED NOT NULL,
    amount DECIMAL(12, 2) NOT NULL,
    payment_method VARCHAR(80) NULL,
    status ENUM('Pending', 'Paid', 'Refunded') NOT NULL DEFAULT 'Pending',
    receipt_number VARCHAR(80) NULL,
    paid_at DATETIME NULL,
    FOREIGN KEY (service_request_id) REFERENCES service_requests(id)
);

INSERT INTO products (name, category, description, price, stock)
SELECT 'Solar Panels', 'Panels', 'Photovoltaic panels for residential and commercial systems.', 0, 0
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Solar Panels');

INSERT INTO products (name, category, description, price, stock)
SELECT 'Solar Inverters', 'Inverters', 'Convert solar DC power for compatible electrical systems.', 0, 0
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Solar Inverters');

INSERT INTO products (name, category, description, price, stock)
SELECT 'Solar Batteries', 'Batteries', 'Energy storage options for suitable solar installations.', 0, 0
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Solar Batteries');

INSERT INTO products (name, category, description, price, stock)
SELECT 'Racking & Mounting', 'Mounting', 'Mounting hardware for different installation types.', 0, 0
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Racking & Mounting');

INSERT INTO products (name, category, description, price, stock)
SELECT 'Solar Wires', 'Wires', 'Wiring and accessories for solar installations.', 0, 0
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Solar Wires');

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
