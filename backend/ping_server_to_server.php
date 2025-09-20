<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

set_time_limit(0);

$to = $_GET['to'] ?? '';
$duration = 20; // 20 seconds fixed

if (!$to) {
    echo json_encode(["status" => "error", "message" => "No target server given"]);
    exit;
}

// Result array
$result = [
    "status" => "ok",
    "ping_ms" => null,
    "jitter_ms" => null,
    "download_mbps" => null,
    "upload_mbps" => null
];

// ------------------- PING + JITTER -------------------
$pingCmd = sprintf("ping -c 5 -q %s", escapeshellarg($to));
exec($pingCmd, $out, $ret);
if ($ret === 0 && !empty($out)) {
    $line = implode(" ", $out);
    if (preg_match('/rtt min\/avg\/max\/mdev = ([0-9.]+)\/([0-9.]+)\/([0-9.]+)\/([0-9.]+)/', $line, $m)) {
        $result["ping_ms"] = floatval($m[2]);
        $result["jitter_ms"] = floatval($m[4]);
    }
}

// ------------------- Iperf3 Test -------------------
$iperfCmd = sprintf(
    "iperf3 -c %s -t %d -J",
    escapeshellarg($to),
    $duration
);
exec($iperfCmd, $output, $ret);

if ($ret === 0 && !empty($output)) {
    $json = implode("", $output);
    $data = json_decode($json, true);

    if ($data) {
        // Download = server → client
        $result["download_mbps"] = isset($data["end"]["sum_received"]["bits_per_second"]) ?
            round($data["end"]["sum_received"]["bits_per_second"] / 1024 / 1024, 2) : null;

        // Upload = client → server
        $result["upload_mbps"] = isset($data["end"]["sum_sent"]["bits_per_second"]) ?
            round($data["end"]["sum_sent"]["bits_per_second"] / 1024 / 1024, 2) : null;
    }
}

// ------------------- OUTPUT -------------------
echo json_encode($result);
exit;
