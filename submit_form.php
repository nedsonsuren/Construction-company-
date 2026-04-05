<?php
// ═════════════════════════════════════════════════════════════
// FLIDOH CONSTRUCTION - CONTACT FORM HANDLER
// Handles form submissions and stores messages in MySQL
// ═════════════════════════════════════════════════════════════

// Include database configuration
require_once 'db_config.php';

// Set JSON header first
header('Content-Type: application/json; charset=utf-8');

// Handle CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Check if POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
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

    // Get form data
    $firstName = isset($_POST['firstName']) ? trim($_POST['firstName']) : '';
    $lastName = isset($_POST['lastName']) ? trim($_POST['lastName']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $service = isset($_POST['service']) ? trim($_POST['service']) : '';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';

    // Validation
    $errors = [];
    if (empty($firstName)) $errors[] = 'First name is required';
    if (empty($lastName)) $errors[] = 'Last name is required';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required';
    if (empty($message)) $errors[] = 'Message is required';

    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Validation failed', 'errors' => $errors]);
        exit;
    }

    // Insert message into database
    $stmt = $pdo->prepare("
        INSERT INTO messages (first_name, last_name, email, service, message, ip_address)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $ipAddress = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'unknown';

    $result = $stmt->execute([
        $firstName,
        $lastName,
        $email,
        $service,
        $message,
        $ipAddress
    ]);

    if (!$result) {
        throw new Exception('Failed to save message to database');
    }

    $messageId = $pdo->lastInsertId();

    // Try to send email (optional - don't fail if it doesn't work)
    $mailSent = false;
    try {
        $to = 'seannedson@gmail.com';
        $subject = 'New Quote Request from ' . $firstName . ' ' . $lastName;
        $emailBody = "Name: $firstName $lastName\r\n";
        $emailBody .= "Email: $email\r\n";
        $emailBody .= "Service: " . ($service ? $service : 'Not specified') . "\r\n";
        $emailBody .= "Message: $message\r\n";
        $emailBody .= "Submitted: " . date('Y-m-d H:i:s') . "\r\n";

        $headers = "From: noreply@flidohconstruction.com\r\n";
        $headers .= "Reply-To: " . $email . "\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        if (function_exists('mail')) {
            $mailSent = @mail($to, $subject, $emailBody, $headers);
        }
    } catch (Exception $e) {
        // Silently fail on mail - message is already saved
        $mailSent = false;
    }

    // Return success response
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Message received! We will contact you shortly.',
        'id' => $messageId,
        'email_sent' => $mailSent
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>