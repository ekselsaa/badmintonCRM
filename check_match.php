<?php
$indexFile = __DIR__ . '/resources/views/pelanggan/membership/index.blade.php';
$content = file_get_contents($indexFile);

$replacement472 = file_get_contents(__DIR__ . '/recovered_files/failed_details/step_472_replacement.txt');

function normalize($str) {
    return str_replace("\r\n", "\n", trim($str));
}

$contentNorm = normalize($content);
$rep472 = normalize($replacement472);

echo "Content length: " . strlen($contentNorm) . "\n";
echo "Replacement length: " . strlen($rep472) . "\n";

$pos = strpos($contentNorm, $rep472);
if ($pos === false) {
    echo "Match failed.\n";
    // Let's print the first 100 characters of each to see differences
    echo "Content start: " . bin2hex(substr($contentNorm, 0, 100)) . "\n";
    echo "Replacement start: " . bin2hex(substr($rep472, 0, 100)) . "\n";
    
    // Let's search for a smaller substring
    $sub = "function selectPackage(element)";
    echo "Sub position: " . strpos($contentNorm, $sub) . "\n";
} else {
    echo "Match succeeded at pos: $pos\n";
}
