<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

@ini_set('zlib.output_compression', 'Off');
@ini_set('output_buffering', 'Off');
@ini_set('output_handler', '');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Content-Type: application/octet-stream');
header('Content-Encoding: identity');

$sizeMB = isset($_GET['ckSize']) ? intval($_GET['ckSize']) : 0; 
// If 0 → unlimited mode

$block = random_bytes(1024 * 1024); // 1MB block
$i = 0;

if ($sizeMB > 0) {
    // Fixed size mode (old behavior)
    for ($i = 0; $i < $sizeMB; $i++) {
        echo $block;
        flush();
        if (function_exists("ob_flush")) ob_flush();
    }
} else {
    // Unlimited stream mode
    while (true) {
        echo $block;
        flush();
        if (function_exists("ob_flush")) ob_flush();
    }
}
