<?php
// ═════════════════════════════════════════════════════════════
// FLIDOH CONSTRUCTION - GET MESSAGES API
// Retrieves all submitted messages from MySQL
// ═════════════════════════════════════════════════════════════

// Include database configuration
require_once 'db_config.php';

// Set headers
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    // Initialize database if needed
    if (!initializeDatabase()) {
        throw new Exception('Database initialization failed');
    }

    // Get database connection
    $pdo = getDBConnection();
    if (!$pdo) {
        throw new Exception('Database connection failed');
    }

    // Get messages from database
    $stmt = $pdo->prepare("
        SELECT id, first_name, last_name, email, service, message, timestamp, ip_address, status
        FROM messages
        ORDER BY timestamp DESC
    ");

    $stmt->execute();
    $messages = $stmt->fetchAll();

    // Transform data to match the expected format
    $transformedMessages = [];
    foreach ($messages as $msg) {
        $transformedMessages[] = [
            'id' => (int)$msg['id'],
            'firstName' => $msg['first_name'],
            'lastName' => $msg['last_name'],
            'email' => $msg['email'],
            'service' => $msg['service'],
            'message' => $msg['message'],
            'timestamp' => $msg['timestamp'],
            'ip' => $msg['ip_address'],
            'status' => $msg['status']
        ];
    }

    $total = count($transformedMessages);

    // Count messages from today
    $today_start = strtotime('today');
    $today_end = strtotime('tomorrow') - 1;
    $today = 0;

    foreach ($transformedMessages as $msg) {
        $msg_time = strtotime($msg['timestamp']);
        if ($msg_time >= $today_start && $msg_time <= $today_end) {
            $today++;
        }
    }

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'messages' => $transformedMessages,
        'total' => $total,
        'today' => $today
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
