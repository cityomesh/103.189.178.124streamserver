<?php
header("Content-Type: application/json");

// --- Clients list (name + ip) ---
$clients = [
  [ "name" => "CDN DSN3", "ip" => "10.6.6.165" ],
  [ "name" => "BLCRDHE EDGECDN1006", "ip" => "10.7.7.252" ],
  [ "name" => "KAMALAMILLSHATHWAY1007", "ip" => "172.31.42.2" ],
  [ "name" => "HYDERABAD EDGECDN1008", "ip" => "172.31.32.2" ],
  [ "name" => "KANPUR EXCITEL", "ip" => "172.29.3.178" ],
  [ "name" => "Testing", "ip" => "192.168.12.53" ]
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
