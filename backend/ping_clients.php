<?php
header("Content-Type: application/json");

// --- Clients list (name + ip) ---
$clients = [
  [ "name" => "CDN DSN3", "ip" => "192.168.24.5" ],
  [ "name" => "SHARPLINK INDORE CDN", "ip" => "192.168.25.5" ],
  [ "name" => "SPIDERLINK_NOIDA", "ip" => "192.168.27.5" ]
];

$results = [];

foreach ($clients as $client) {
    $ip = escapeshellarg($client['ip']);
    $pingCmd = "ping -c 3 -W 2 $ip"; // 3 packets, timeout 2s
    $output = [];
    $returnVar = 0;

    exec($pingCmd, $output, $returnVar);

    $latency = null;
    $status = "down";

    if ($returnVar === 0) {
        $status = "ok";
        foreach ($output as $line) {
            if (strpos($line, "avg") !== false) {
                preg_match('/= (.*)\/(.*)\/(.*)\/(.*) ms/', $line, $matches);
                if (isset($matches[2])) {
                    $latency = round(floatval($matches[2]), 2);
                }
            }
        }
    }

    $results[] = [
        "name" => $client['name'],
        "ip" => $client['ip'],
        "status" => $status,
        "latency_ms" => $latency,
        "raw" => $output
    ];
}

echo json_encode([
    "server" => getHostByName(getHostName()), // current server IP
    "results" => $results
], JSON_PRETTY_PRINT);
