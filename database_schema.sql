-- ═════════════════════════════════════════════════════════════
-- FLIDOH CONSTRUCTION - DATABASE SCHEMA
-- MySQL database for storing contact form messages
-- ═════════════════════════════════════════════════════════════

-- Create database
CREATE DATABASE IF NOT EXISTS flidoh_construction;
USE flidoh_construction;

-- Create messages table
CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    service VARCHAR(255),
    message TEXT NOT NULL,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),
    status ENUM('unread', 'read', 'responded') DEFAULT 'unread',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create index for better performance
CREATE INDEX idx_timestamp ON messages(timestamp);
CREATE INDEX idx_status ON messages(status);
CREATE INDEX idx_email ON messages(email);

-- Insert sample data (optional)
INSERT INTO messages (first_name, last_name, email, service, message, ip_address) VALUES
('John', 'Doe', 'john.doe@example.com', 'Commercial Construction', 'I am interested in your commercial construction services for a new office building.', '127.0.0.1'),
('Jane', 'Smith', 'jane.smith@example.com', 'Residential Projects', 'Looking for a custom home design and construction quote.', '127.0.0.1');

-- Create admin users table (optional, for future admin panel)
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    role ENUM('admin', 'moderator') DEFAULT 'admin',
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default admin user (password: admin123)
INSERT INTO admin_users (username, password_hash, email) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@flidohconstruction.com');
