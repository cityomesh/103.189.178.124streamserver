<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
ini_set('max_execution_time', 200); // 40s download + 40s upload + overhead

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$from = $_GET['from'] ?? '';
$to   = $_GET['to']   ?? '';

if (!$to) {
    echo json_encode(["status" => "error", "message" => "No server specified"]);
    exit;
}

if (!$from) {
    $from = gethostbyname(gethostname()); // fallback for localhost
}

$result = [
    "status"        => "ok",
    "from"          => $from,
    "to"            => $to,
    "ping_ms"       => null,
    "jitter_ms"     => null,
    "download_mbps" => null,
    "upload_mbps"   => null,
];

//// ------------------- PING & JITTER ------------------- ////
$output = [];
exec("ping -c 5 " . escapeshellarg($to), $output, $ret);

$pings = [];
foreach ($output as $line) {
    if (preg_match('/time=([\d\.]+)/', $line, $m)) {
        $pings[] = floatval($m[1]);
    }
}
if (count($pings)) {
    $avg    = array_sum($pings) / count($pings);
    $jitter = max($pings) - min($pings);
    $result["ping_ms"]   = round($avg, 2);
    $result["jitter_ms"] = round($jitter, 2);
}

//// ------------------- DOWNLOAD SPEED (Dynamic 40s) ------------------- ////
// $downloadUrl = "http://{$to}/MainCdnServer/backend/garbage.php"; // infinite random data
$downloadUrl = "http://$to/MainCdnServer/backend/garbage.php";

$ch = curl_init($downloadUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 45);

$bytes = 0;
$startTime = null;

curl_setopt($ch, CURLOPT_WRITEFUNCTION, function($ch, $data) use (&$bytes, &$startTime) {
    if ($startTime === null) {
        $startTime = microtime(true);
    }
    $bytes += strlen($data);

    // stop after 40 sec
    if ((microtime(true) - $startTime) >= 40) {
        return 0;
    }
    return strlen($data);
});

curl_exec($ch);
$endTime = microtime(true);
curl_close($ch);

if ($startTime) {
    $duration = $endTime - $startTime;
    $mbps = ($bytes * 8) / ($duration * 1024 * 1024);
    $result["download_mbps"] = round($mbps, 2);
}

//// ------------------- UPLOAD SPEED (Dynamic 40s continuous) ------------------- ////
$uploadUrl = "http://$to/MainCdnServer/backend/empty.php";
$bytesSent = 0;
$startTime = microtime(true);

$ch = curl_init($uploadUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 45); // allow up to 45 sec total

// 1 MB dummy chunk
$chunk = str_repeat("A", 1024 * 1024);

curl_setopt($ch, CURLOPT_READFUNCTION, function($ch, $fd, $length) use (&$bytesSent, $chunk, $startTime) {
    $elapsed = microtime(true) - $startTime;
    if ($elapsed >= 40) {
        return ""; // stop upload
    }
    $bytesSent += strlen($chunk);
    return $chunk;
});

curl_setopt($ch, CURLOPT_INFILESIZE, -1); // unknown size (streaming)
curl_exec($ch);
curl_close($ch);

$elapsed = microtime(true) - $startTime;
if ($bytesSent > 0 && $elapsed > 0) {
    $uploadMbps = ($bytesSent * 8) / ($elapsed * 1024 * 1024);
    $result["upload_mbps"] = round($uploadMbps, 2);
}

//// ------------------- OUTPUT ------------------- ////
echo json_encode($result);
exit;
