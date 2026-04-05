<?php
// ═════════════════════════════════════════════════════════════
// FLIDOH CONSTRUCTION - DATABASE SETUP SCRIPT
// Initializes the MySQL database and tables
// ═════════════════════════════════════════════════════════════

// Include database configuration
require_once 'db_config.php';

echo "<h1>Flidoh Construction - Database Setup</h1>";
echo "<pre>";

// Test database connection
echo "Testing database connection...\n";
if (testDBConnection()) {
    echo "✓ Database connection successful!\n\n";
} else {
    echo "✗ Database connection failed!\n";
    echo "Please check your database configuration in db_config.php\n\n";
    exit;
}

// Initialize database tables
echo "Initializing database tables...\n";
if (initializeDatabase()) {
    echo "✓ Database tables created successfully!\n\n";
} else {
    echo "✗ Failed to create database tables!\n\n";
    exit;
}

// Test inserting a sample message
echo "Testing message insertion...\n";
try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("
        INSERT INTO messages (first_name, last_name, email, service, message, ip_address)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $result = $stmt->execute([
        'Setup',
        'Test',
        'setup@test.com',
        'Database Setup',
        'This is a test message created during database setup.',
        '127.0.0.1'
    ]);

    if ($result) {
        echo "✓ Test message inserted successfully!\n";
        echo "Message ID: " . $pdo->lastInsertId() . "\n\n";
    } else {
        echo "✗ Failed to insert test message!\n\n";
    }
} catch (Exception $e) {
    echo "✗ Error inserting test message: " . $e->getMessage() . "\n\n";
}

// Test retrieving messages
echo "Testing message retrieval...\n";
try {
    $pdo = getDBConnection();
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM messages");
    $result = $stmt->fetch();
    echo "✓ Found " . $result['total'] . " messages in database\n\n";
} catch (Exception $e) {
    echo "✗ Error retrieving messages: " . $e->getMessage() . "\n\n";
}

echo "</pre>";
echo "<h2>Setup Complete!</h2>";
echo "<p>Your Flidoh Construction website is now integrated with MySQL database.</p>";
echo "<ul>";
echo "<li><a href='index.html'>View Website</a></li>";
echo "<li><a href='messages.html'>View Messages Dashboard</a></li>";
echo "<li><a href='debug.html'>Test Form Submission</a></li>";
echo "</ul>";
?>