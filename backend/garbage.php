<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/octet-stream');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

@ini_set('zlib.output_compression', '0');
@ini_set('output_buffering', 'Off');
@ini_set('output_handler', '');
set_time_limit(0);

if (ob_get_level()) ob_end_clean();

$chunkSize = 1024 * 1024; // 1 MB
$duration  = 20;           // 20 seconds for download test
$chunk     = str_repeat("A", $chunkSize);

$startTime = microtime(true);

while ((microtime(true) - $startTime) < $duration) {
    echo $chunk;
    if (function_exists('ob_flush')) ob_flush();
    flush();
}
exit;
