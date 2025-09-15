<?php
// ✅ CORS headers: ఎప్పుడూ పైనే ఉండాలి
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Encoding, Content-Type');

// ✅ Compression disable cheyyadam
@ini_set('zlib.output_compression', 'Off');
@ini_set('output_buffering', 'Off');
@ini_set('output_handler', '');

/**
 * Helper: get chunk count (MB lo)
 */
function getChunkCount()
{
    if (
        !array_key_exists('ckSize', $_GET)
        || !ctype_digit($_GET['ckSize'])
        || (int) $_GET['ckSize'] <= 0
    ) {
        return 1; // default 1 MB
    }

    if ((int) $_GET['ckSize'] > 1024) {
        return 1024; // max 1024 MB
    }

    return (int) $_GET['ckSize'];
}

/**
 * Headers for binary stream
 */
function sendHeaders()
{
    header('HTTP/1.1 200 OK');
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=random.dat');
    header('Content-Transfer-Encoding: binary');

    // Cache settings
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0, s-maxage=0');
    header('Cache-Control: post-check=0, pre-check=0', false);
    header('Pragma: no-cache');
}

// ✅ MBs lo count
$chunks = getChunkCount();

// ✅ Random 1MB chunk generate cheyyadam
$data = openssl_random_pseudo_bytes(1048576);

// ✅ Send headers
sendHeaders();

// ✅ Loop and send chunks
for ($i = 0; $i < $chunks; $i++) {
    echo $data;
    flush();
}

?>
