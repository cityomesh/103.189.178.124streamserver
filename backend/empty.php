<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Read full POST payload
$input = file_get_contents('php://input');

// Return received MB
echo json_encode([
    'status' => 'ok',
    'received_mb' => strlen($input)/(1024*1024)
]);
exit;
