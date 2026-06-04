<?php
$file = 'c:\\xampp\\htdocs\\badmintonCRM\\resources\\views\\pelanggan\\booking\\index.blade.php';
$content = file_get_contents($file);
$lines = explode("\n", $content);
$search = 'occupied';
foreach ($lines as $i => $line) {
    if (stripos($line, $search) !== false) {
        echo "Line " . ($i + 1) . ": " . trim($line) . "\n";
    }
}
