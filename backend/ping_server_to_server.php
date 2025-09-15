<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

$to = $_GET['to'] ?? '';
if (!$to) {
    echo json_encode(["status" => "error", "message" => "No server specified"]);
    exit;
}

// ----------------------
// 🟢 PING
// ----------------------
exec("ping -c 5 " . escapeshellarg($to), $output, $retval);
$pings = [];
foreach ($output as $line) {
    if (preg_match('/time=([\d\.]+)/', $line, $matches)) {
        $pings[] = floatval($matches[1]);
    }
}
$ping = $jitter = 0;
if (count($pings) > 0) {
    $ping = array_sum($pings) / count($pings);
    $jitter = max($pings) - min($pings);
}

// ----------------------
// 🟢 DOWNLOAD TEST
// ----------------------
$downloadUrl = "http://{$to}/streamserver/backend/garbage.php?ckSize=5000000"; // ~5MB
$start = microtime(true);

$ch = curl_init($downloadUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
$data = curl_exec($ch);
curl_close($ch);

$end = microtime(true);
$downloadSpeed = 0;
if ($data !== false) {
    $size = strlen($data) / (1024 * 1024); // MB
    $time = $end - $start;
    if ($time > 0) {
        $downloadSpeed = round(($size / $time) * 8, 2); // Mbps
    }
}

// ----------------------
// 🟢 UPLOAD TEST
// ----------------------
$uploadUrl = "http://{$to}/streamserver/backend/empty.php";
$postData = str_repeat("A", 5 * 1024 * 1024); // 5MB dummy

$start = microtime(true);
$ch = curl_init($uploadUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/octet-stream"]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
$result = curl_exec($ch);
curl_close($ch);

$end = microtime(true);
$uploadSpeed = 0;
if ($result !== false) {
    $size = strlen($postData) / (1024 * 1024); // MB
    $time = $end - $start;
    if ($time > 0) {
        $uploadSpeed = round(($size / $time) * 8, 2); // Mbps
    }
}

// ----------------------
// 🟢 OUTPUT
// ----------------------
echo json_encode([
    "status" => "ok",
    "target" => $to,
    "ping_ms" => round($ping, 2),
    "jitter_ms" => round($jitter, 2),
    "download_mbps" => $downloadSpeed,
    "upload_mbps"  => $uploadSpeed
]);

?>
