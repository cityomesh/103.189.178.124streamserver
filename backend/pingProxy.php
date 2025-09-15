<?php
// 🟢 Debugging enable chesamu
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 🟢 CORS headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, X-Requested-With");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

// OPTIONS request ki 200 return cheyyadam
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// ✅ Required parameters check
if (!isset($_GET['target']) || !isset($_GET['path'])) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "message" => "Missing target or path"
    ]);
    exit;
}

$target = $_GET['target']; // Example: 192.168.12.103
$path   = $_GET['path'];   // Example: MainCdnServer/backend/ping_server_to_server.php

// 🔹 Remaining query parameters extract cheyyadam
parse_str($_SERVER['QUERY_STRING'], $params);
unset($params['target'], $params['path']);
$qs = http_build_query($params);

// 🔹 Path ni correct ga encode cheyyadam
$encodedPath = implode("/", array_map('rawurlencode', explode("/", $path)));

// 🔹 Final URL
$url = "http://{$target}/{$encodedPath}" . ($qs ? "?$qs" : "");

// 🔹 Debugging: final URL chudachu (temporary)
// echo json_encode(["debug_url" => $url]); exit;

// 🟢 CURL initialize
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 20);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

// POST request handle cheyyadam
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents("php://input"));
}

// 🟢 Execute curl
$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// 🔹 Curl error check
if ($response === false) {
    $error = curl_error($ch);
    curl_close($ch);
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Proxy curl error: $error"
    ]);
    exit;
}

// 🔹 Close curl
curl_close($ch);

// 🟢 Forward the response and HTTP status code
http_response_code($httpcode);
echo $response;
?>
