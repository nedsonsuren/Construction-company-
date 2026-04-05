<?php
echo json_encode([
    'php_working' => true,
    'php_version' => phpversion(),
    'server' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
    'time' => date('Y-m-d H:i:s')
]);
?>
