<?php

// ✅ CORS headers: ఎప్పుడూ పైనే ఉండాలి
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Encoding, Content-Type');

// ✅ Compression ni disable cheyyadam
@ini_set('zlib.output_compression', 'Off');
@ini_set('output_buffering', 'Off');
@ini_set('output_handler', '');

/**
 * @return int
 */


function testDownloadSpeed($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $start = microtime(true);
    $data = curl_exec($ch);
    $end = microtime(true);

    if (curl_errno($ch)) {
        curl_close($ch);
        return 0;
    }
    curl_close($ch);

    $size = strlen($data) / (1024 * 1024); // MB
    $time = $end - $start;
    if ($time <= 0) return 0;

    return round(($size * 8) / $time, 2); // Mbps
}

function getChunkCount()
{
    if (
        !array_key_exists('ckSize', $_GET)
        || !ctype_digit($_GET['ckSize'])
        || (int) $_GET['ckSize'] <= 0
    ) {
        return 4;
    }

    if ((int) $_GET['ckSize'] > 1024) {
        return 1024;
    }

    return (int) $_GET['ckSize'];
}

/**
 * @return void
 */
function sendHeaders()
{
    header('HTTP/1.1 200 OK');

    // ✅ Ikkada `CORS` header need ledu - top lo already icham

    // File transfer headers
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=random.dat');
    header('Content-Transfer-Encoding: binary');

    // Cache settings: never cache this request
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0, s-maxage=0');
    header('Cache-Control: post-check=0, pre-check=0', false);
    header('Pragma: no-cache');
}

// Determine how much data we should send
$chunks = getChunkCount();

// Generate data
$data = openssl_random_pseudo_bytes(1048576);

// Deliver chunks of 1048576 bytes
sendHeaders();
for ($i = 0; $i < $chunks; $i++) {
    echo $data;
    flush();
}
?>
