<?php
// 🟡 Enable PHP errors for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, X-Requested-With");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// ✅ Validate required parameters
if (!isset($_GET['target']) || !isset($_GET['path'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Missing target or path"]);
    exit;
}

$target = $_GET['target'];
$path   = $_GET['path'];

// Rebuild query string without target & path
parse_str($_SERVER['QUERY_STRING'], $params);
unset($params['target'], $params['path']);
$qs = http_build_query($params);

// Build URL
$url = "http://{$target}/{$path}" . ($qs ? "?$qs" : "");

// ✅ Debug: optionally uncomment to see the URL
// echo json_encode([ "status" => "debug", "url" => $url ]); exit;

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 20);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents("php://input"));
}

$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($response === false) {
    $error = curl_error($ch);
    curl_close($ch);
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Proxy curl error: $error"]);
    exit;
}

curl_close($ch);

// Forward the response and HTTP code
http_response_code($httpcode);
echo $response;
