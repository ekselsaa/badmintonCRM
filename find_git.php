<?php
$possiblePaths = [
    'C:\\Program Files\\Git\\cmd\\git.exe',
    'C:\\Program Files\\Git\\bin\\git.exe',
    'C:\\Program Files (x86)\\Git\\cmd\\git.exe',
    'C:\\Program Files (x86)\\Git\\bin\\git.exe',
    'C:\\Users\\Lenovo\\AppData\\Local\\Programs\Git\\cmd\\git.exe',
    'C:\\Users\\Lenovo\\AppData\\Local\\Programs\\Git\\bin\\git.exe',
];

foreach ($possiblePaths as $path) {
    if (file_exists($path)) {
        echo "Found Git at: $path\n";
    }
}

// Also scan C:\Program Files and AppData for git.exe
function find_file($dir, $filename, $maxDepth = 4, $currentDepth = 0) {
    if ($currentDepth > $maxDepth) return [];
    if (!is_dir($dir)) return [];
    
    $results = [];
    $files = @scandir($dir);
    if (!$files) return [];
    
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        $path = $dir . DIRECTORY_SEPARATOR . $file;
        if (is_dir($path)) {
            $results = array_merge($results, find_file($path, $filename, $maxDepth, $currentDepth + 1));
        } else {
            if (strtolower($file) === strtolower($filename)) {
                $results[] = $path;
            }
        }
    }
    return $results;
}

echo "Searching Program Files...\n";
$progFiles = find_file('C:\\Program Files', 'git.exe', 4);
foreach ($progFiles as $p) echo "Found: $p\n";

echo "Searching Users...\n";
$userFiles = find_file('C:\\Users\\Lenovo\\AppData', 'git.exe', 5);
foreach ($userFiles as $p) echo "Found: $p\n";
