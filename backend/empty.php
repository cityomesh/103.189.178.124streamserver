<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$bytesReceived = 0;
$start = microtime(true);

// Read input in chunks
while (!feof(STDIN)) {
    $data = fread(STDIN, 1024*1024);
    if ($data === false) break;
    $bytesReceived += strlen($data);
}

$elapsed = microtime(true) - $start;
$uploadMbps = $elapsed > 0 ? ($bytesReceived * 8) / ($elapsed * 1024 * 1024) : 0;

echo json_encode([
    'status' => 'ok',
    'received_mb' => round($bytesReceived/1024/1024, 2),
    'upload_mbps' => round($uploadMbps, 2)
]);
exit;
