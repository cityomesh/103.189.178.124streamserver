<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

$to = $_GET['to'] ?? $argv[1] ?? '';

if (!$to) {
    echo json_encode(["status" => "error", "message" => "No server specified"]);
    exit;
}

// -----------------------------
// PING TEST
// -----------------------------
exec("ping -c 5 " . escapeshellarg($to), $output, $retval);
$pings = [];
foreach ($output as $line) {
    if (preg_match('/time=([\d\.]+)/', $line, $matches)) {
        $pings[] = floatval($matches[1]);
    }
}

$ping = 0;
$jitter = 0;
if (count($pings) > 0) {
    $ping = array_sum($pings) / count($pings);
    $jitter = max($pings) - min($pings);
}

// -----------------------------
// DOWNLOAD TEST
// -----------------------------
$downloadUrl = "http://$to/MainCdnServer/backend/garbage.php?ckSize=20"; // 20MB
$downloadSpeed = 0;

$ch = curl_init($downloadUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // ✅ get full data in memory
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$start = microtime(true);
$data = curl_exec($ch);
$end = microtime(true);
$info = curl_getinfo($ch);
curl_close($ch);

if ($data !== false && !empty($info['size_download'])) {
    $mb = $info['size_download'] / (1024 * 1024); // MB
    $time = $end - $start;
    if ($time > 0) {
        $downloadSpeed = round(($mb * 8) / $time, 2); // Mbps
    }
}

// -----------------------------
// UPLOAD TEST
// -----------------------------
$uploadUrl = "http://$to/MainCdnServer/backend/empty.php";
$postData = str_repeat("0", 8 * 1024 * 1024); // 8MB dummy data

$ch = curl_init($uploadUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$start = microtime(true);
curl_exec($ch);
$end = microtime(true);
$info = curl_getinfo($ch);
curl_close($ch);

$uploadSpeed = 0;
if (!empty($info['size_upload'])) {
    $mb = $info['size_upload'] / (1024 * 1024); // MB
    $time = $end - $start;
    if ($time > 0) {
        $uploadSpeed = round(($mb * 8) / $time, 2); // Mbps
    }
}

// -----------------------------
// OUTPUT JSON
// -----------------------------
echo json_encode([
    "status" => "ok",
    "target" => $to,
    "ping_ms" => round($ping, 2),
    "jitter_ms" => round($jitter, 2),
    "download_mbps" => $downloadSpeed,
    "upload_mbps"  => $uploadSpeed
]);

