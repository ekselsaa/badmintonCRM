<?php
$indexFile = __DIR__ . '/resources/views/pelanggan/membership/index.blade.php';
if (!file_exists($indexFile)) {
    die("File not found\n");
}

$content = file_get_contents($indexFile);

// Revert step 472
$target472 = file_get_contents(__DIR__ . '/recovered_files/failed_details/step_472_target.txt');
$replacement472 = file_get_contents(__DIR__ . '/recovered_files/failed_details/step_472_replacement.txt');

// Revert step 339
$target339 = file_get_contents(__DIR__ . '/recovered_files/failed_details/step_339_target.txt');
$replacement339 = file_get_contents(__DIR__ . '/recovered_files/failed_details/step_339_replacement.txt');

// Revert step 335
$target335 = file_get_contents(__DIR__ . '/recovered_files/failed_details/step_335_target.txt');
$replacement335 = file_get_contents(__DIR__ . '/recovered_files/failed_details/step_335_replacement.txt');

// Revert step 329
$target329 = file_get_contents(__DIR__ . '/recovered_files/failed_details/step_329_target.txt');
$replacement329 = file_get_contents(__DIR__ . '/recovered_files/failed_details/step_329_replacement.txt');

// Normalize newlines
function normalize($str) {
    return str_replace("\r\n", "\n", trim($str));
}

$contentNorm = str_replace("\r\n", "\n", $content);

// Apply revert 472
$rep472 = normalize($replacement472);
$tar472 = normalize($target472);
if (strpos($contentNorm, $rep472) !== false) {
    $contentNorm = str_replace($rep472, $tar472, $contentNorm);
    echo "Reverted step 472 successfully!\n";
} else {
    echo "Revert step 472 skipped or already done.\n";
}

// Apply revert 339
$rep339 = normalize($replacement339);
$tar339 = normalize($target339);
if (strpos($contentNorm, $rep339) !== false) {
    $contentNorm = str_replace($rep339, $tar339, $contentNorm);
    echo "Reverted step 339 successfully!\n";
} else {
    echo "Revert step 339 skipped or already done.\n";
}

// Apply revert 335
$rep335 = normalize($replacement335);
$tar335 = normalize($target335);
if (strpos($contentNorm, $rep335) !== false) {
    $contentNorm = str_replace($rep335, $tar335, $contentNorm);
    echo "Reverted step 335 successfully!\n";
} else {
    echo "Revert step 335 skipped or already done.\n";
}

// Apply revert 329
$rep329 = normalize($replacement329);
$tar329 = normalize($target329);
if (strpos($contentNorm, $rep329) !== false) {
    $contentNorm = str_replace($rep329, $tar329, $contentNorm);
    echo "Reverted step 329 successfully!\n";
} else {
    echo "Revert step 329 skipped or already done.\n";
}

file_put_contents($indexFile, $contentNorm);
echo "Saved reverted index.blade.php\n";
