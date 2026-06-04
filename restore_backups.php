<?php
$backupDir = __DIR__ . '/recovered_files/backups';
$workspaceDir = __DIR__;

function scan($dir, &$results = array()) {
    $files = scandir($dir);
    foreach ($files as $key => $value) {
        $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
        if (!is_dir($path)) {
            $results[] = str_replace(__DIR__ . DIRECTORY_SEPARATOR . 'recovered_files' . DIRECTORY_SEPARATOR . 'backups' . DIRECTORY_SEPARATOR, "", $path);
        } else if ($value != "." && $value != "..") {
            scan($path, $results);
        }
    }
    return $results;
}

$files = scan($backupDir);
foreach ($files as $file) {
    $src = $backupDir . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $file);
    $dest = $workspaceDir . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $file);
    copy($src, $dest);
    echo "Restored from backup: $file\n";
}
