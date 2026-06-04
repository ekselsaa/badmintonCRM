<?php
$dir = __DIR__ . '/recovered_files/complete';
function scan($dir, &$results = array()) {
    $files = scandir($dir);
    foreach ($files as $key => $value) {
        $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
        if (!is_dir($path)) {
            $results[] = str_replace(__DIR__ . DIRECTORY_SEPARATOR . 'recovered_files' . DIRECTORY_SEPARATOR . 'complete' . DIRECTORY_SEPARATOR, "", $path);
        } else if ($value != "." && $value != "..") {
            scan($path, $results);
        }
    }
    return $results;
}
$files = scan($dir);
foreach ($files as $file) {
    echo "- $file\n";
}
