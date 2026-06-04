<?php
$file = 'c:\\xampp\\htdocs\\badmintonCRM\\resources\\views\\pelanggan\\membership\\index.blade.php';
$content = file_get_contents($file);
$lines = explode("\n", $content);
foreach ($lines as $i => $line) {
    if (strpos($line, 'lapangan_id') !== false || strpos($line, 'select-hari') !== false || strpos($line, 'select-sesi') !== false) {
        echo "Line " . ($i + 1) . ": " . trim($line) . "\n";
    }
}
