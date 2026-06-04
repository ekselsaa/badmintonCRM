<?php
$recoveredDir = realpath(__DIR__ . '/recovered_files');
$currentDir = realpath(__DIR__);

$allRecovered = [];
function scan_recovered($dir, &$results = array()) {
    $files = scandir($dir);
    foreach ($files as $key => $value) {
        $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
        if (!is_dir($path)) {
            $results[] = $path;
        } else if ($value != "." && $value != "..") {
            scan_recovered($path, $results);
        }
    }
    return $results;
}

$allRecovered = scan_recovered($recoveredDir);

foreach ($allRecovered as $recFile) {
    // Normalize path slashes and case
    $recFileNorm = strtolower(realpath($recFile));
    $recDirNorm = strtolower($recoveredDir);
    
    // Get relative path
    $relative = str_replace($recDirNorm . DIRECTORY_SEPARATOR, "", $recFileNorm);
    $currentFile = realpath($currentDir . DIRECTORY_SEPARATOR . $relative);
    
    if (!$currentFile || !file_exists($currentFile)) {
        echo "File does not exist in current: $relative (Only in recovered)\n";
        continue;
    }
    
    $recContent = file_get_contents($recFile);
    $curContent = file_get_contents($currentFile);
    
    // Normalise newlines
    $recContentNorm = str_replace("\r\n", "\n", trim($recContent));
    $curContentNorm = str_replace("\r\n", "\n", trim($curContent));
    
    if ($recContentNorm !== $curContentNorm) {
        echo "DIFFERENCE: $relative\n";
        // Let's print line count difference
        $recLines = explode("\n", $recContentNorm);
        $curLines = explode("\n", $curContentNorm);
        echo "  Recovered lines: " . count($recLines) . ", Current lines: " . count($curLines) . "\n";
    } else {
        echo "IDENTICAL: $relative\n";
    }
}
