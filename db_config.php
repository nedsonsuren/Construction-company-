<?php
// ═════════════════════════════════════════════════════════════
// FLIDOH CONSTRUCTION - DATABASE CONNECTION
// PDO database connection for MySQL
// ═════════════════════════════════════════════════════════════

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'flidoh_construction');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Create PDO connection
function getDBConnection() {
    static $pdo = null;

    if ($pdo === null) {
        try {
            // First try to connect to the database
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // If database doesn't exist, try to create it
            if ($e->getCode() == 1049) { // Unknown database
                if (createDatabaseIfNotExists()) {
                    // Now try connecting again
                    try {
                        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
                    } catch (PDOException $e2) {
                        error_log("Database connection failed after creation: " . $e2->getMessage());
                        $pdo = null;
                    }
                } else {
                    error_log("Failed to create database: " . $e->getMessage());
                    $pdo = null;
                }
            } else {
                error_log("Database connection failed: " . $e->getMessage());
                $pdo = null;
            }
        }
    }

    return $pdo;
}

// Create database if it doesn't exist
function createDatabaseIfNotExists() {
    try {
        // Connect without specifying database
        $dsn = "mysql:host=" . DB_HOST . ";charset=" . DB_CHARSET;
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        // Create database if it doesn't exist
        $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        return true;
    } catch (PDOException $e) {
        error_log("Database creation failed: " . $e->getMessage());
        return false;
    }
}

// Test database connection
function testDBConnection() {
    $pdo = getDBConnection();
    if ($pdo) {
        try {
            $stmt = $pdo->query("SELECT 1");
            return $stmt->fetch() ? true : false;
        } catch (PDOException $e) {
            return false;
        }
    }
    return false;
}

// Initialize database tables if they don't exist
function initializeDatabase() {
    $pdo = getDBConnection();
    if (!$pdo) return false;

    try {
        // Create messages table
        $pdo->exec("
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
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // Create indexes
        $pdo->exec("CREATE INDEX IF NOT EXISTS idx_timestamp ON messages(timestamp)");
        $pdo->exec("CREATE INDEX IF NOT EXISTS idx_status ON messages(status)");
        $pdo->exec("CREATE INDEX IF NOT EXISTS idx_email ON messages(email)");

        return true;
    } catch (PDOException $e) {
        error_log("Database initialization failed: " . $e->getMessage());
        return false;
    }
}
?>