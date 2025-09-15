<?php
header("Content-Type: application/json");

// Params
$from = $_GET['from'] ?? '';
$to   = $_GET['to'] ?? '';

if (!$from || !$to) {
    echo json_encode(["status" => "error", "message" => "Missing from or to"]);
    exit;
}

// ---------- PING ----------
$pingResult = shell_exec("ping -c 4 -q " . escapeshellarg($to));
preg_match('/min\/avg\/max\/mdev = ([\d\.]+)\/([\d\.]+)\/([\d\.]+)\/([\d\.]+)/', $pingResult, $matches);

$ping   = isset($matches[2]) ? (float)$matches[2] : 0; // avg
$jitter = isset($matches[4]) ? (float)$matches[4] : 0; // mdev

// ---------- DOWNLOAD TEST ----------
$downloadStart = microtime(true);
$downloadData  = @file_get_contents("http://$to/testfile_10MB.bin"); // 👈 you need a test file
$downloadEnd   = microtime(true);

$downloadMbps = 0;
if ($downloadData !== false) {
    $sizeBytes   = strlen($downloadData);
    $timeTaken   = $downloadEnd - $downloadStart;
    if ($timeTaken > 0) {
        $downloadMbps = round(($sizeBytes * 8) / ($timeTaken * 1024 * 1024), 2);
    }
}

// ---------- UPLOAD TEST ----------
$uploadMbps = 0;
$uploadStart = microtime(true);

$context = stream_context_create([
    'http' => [
        'method'  => 'POST',
        'header'  => "Content-Type: application/octet-stream\r\n",
        'content' => str_repeat("A", 5 * 1024 * 1024) // 5MB upload test
    ]
]);

$uploadResponse = @file_get_contents("http://$to/upload_test.php", false, $context);
$uploadEnd = microtime(true);

if ($uploadResponse !== false) {
    $timeTaken = $uploadEnd - $uploadStart;
    if ($timeTaken > 0) {
        $uploadMbps = round((5 * 1024 * 1024 * 8) / ($timeTaken * 1024 * 1024), 2);
    }
}

// ---------- RESPONSE ----------
echo json_encode([
    "status" => "ok",
    "from" => $from,
    "target" => $to,
    "ping_ms" => $ping,
    "jitter_ms" => $jitter,
    "download_mbps" => $downloadMbps,
    "upload_mbps" => $uploadMbps
]);
