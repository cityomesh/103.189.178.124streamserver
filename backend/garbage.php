<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/octet-stream');
set_time_limit(0);

$chunkSize = 1024*1024; // 1 MB
$duration = 20;          // 20 sec
$chunk = str_repeat("A", $chunkSize);
$startTime = microtime(true);

while ((microtime(true) - $startTime) < $duration) {
    echo $chunk;
    if (function_exists('ob_flush')) ob_flush();
    flush();
}
exit;
