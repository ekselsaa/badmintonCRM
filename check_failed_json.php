<?php
foreach(glob('recovered_files/failed_details/*.json') as $f) {
    $d = json_decode(file_get_contents($f), true);
    $targetFile = $d['args']['TargetFile'] ?? ($d['args']['AbsolutePath'] ?? '');
    echo basename($f) . ' -> ' . $targetFile . PHP_EOL;
}
