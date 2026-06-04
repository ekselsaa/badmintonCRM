<?php
function scan($dir, &$results = array()) {
    $files = scandir($dir);
    foreach ($files as $key => $value) {
        $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
        if (!is_dir($path)) {
            $mtime = filemtime($path);
            if (time() - $mtime < 2 * 24 * 3600) { // last 2 days
                $results[] = [
                    'file' => $path,
                    'mtime' => date('Y-m-d H:i:s', $mtime)
                ];
            }
        } else if ($value != "." && $value != "..") {
            if (strpos($path, 'vendor') === false && 
                strpos($path, 'node_modules') === false && 
                strpos($path, 'storage') === false && 
                strpos($path, '.git') === false && 
                strpos($path, '.gemini') === false &&
                strpos($path, 'bootstrap/cache') === false) {
                scan($path, $results);
            }
        }
    }
    return $results;
}

$results = scan(__DIR__);
usort($results, function($a, $b) {
    return strcmp($b['mtime'], $a['mtime']);
});

foreach ($results as $res) {
    echo $res['mtime'] . " - " . str_replace(__DIR__ . DIRECTORY_SEPARATOR, "", $res['file']) . "\n";
}
