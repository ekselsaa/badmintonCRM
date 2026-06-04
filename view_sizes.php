<?php
function scan($dir) {
    $results = [];
    $files = scandir($dir);
    foreach ($files as $value) {
        if ($value === '.' || $value === '..') continue;
        $path = $dir . DIRECTORY_SEPARATOR . $value;
        if (!is_dir($path)) {
            $results[] = [
                'file' => $path,
                'size' => filesize($path)
            ];
        } else {
            $results = array_merge($results, scan($path));
        }
    }
    return $results;
}

$results = scan(__DIR__ . '/recovered_files');
foreach ($results as $res) {
    echo $res['size'] . " - " . str_replace(__DIR__ . DIRECTORY_SEPARATOR, "", $res['file']) . "\n";
}
