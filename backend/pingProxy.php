<?php
// Debugging & execution time
ini_set('display_errors', 0); // set to 1 only while debugging
ini_set('max_execution_time', 300);
error_reporting(E_ALL);

// CORS headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, X-Requested-With");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Validate required params
if (empty($_GET['target']) || empty($_GET['path'])) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "message" => "Missing required query params: target or path"
    ]);
    exit;
}

$target = $_GET['target']; // e.g. 192.168.12.103
$path   = $_GET['path'];   // e.g. MainCdnServer/backend/ping_server_to_server.php

// Remove target & path from query string, keep rest
parse_str($_SERVER['QUERY_STRING'], $params);
unset($params['target'], $params['path']);
$qs = http_build_query($params);

// Encode path safely
$encodedPath = implode("/", array_map('rawurlencode', explode("/", $path)));

// Final target URL
$url = "http://{$target}/{$encodedPath}" . ($qs ? "?$qs" : "");

// Init cURL
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 40);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

// Forward POST body if needed
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents("php://input"));
}

// Execute
$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($response === false) {
    $error = curl_error($ch);
    curl_close($ch);

    http_response_code(502); // Bad Gateway
    echo json_encode([
        "status" => "error",
        "message" => "Cannot reach target server ($target). cURL error: $error. Check LAN/VPN/firewall."
    ]);
    exit;
}

curl_close($ch);

// Pass through response from target
http_response_code($httpcode);
echo $response;
