<?php
$failedSteps = [329, 335, 339, 472];
foreach ($failedSteps as $step) {
    $path = __DIR__ . "/recovered_files/failed_details/step_{$step}_index.blade.php.json";
    if (file_exists($path)) {
        $data = json_decode(file_get_contents($path), true);
        echo "=== STEP $step ===\n";
        echo "Instruction: " . ($data['args']['Instruction'] ?? '') . "\n";
        echo "TargetContent:\n" . ($data['args']['TargetContent'] ?? '') . "\n";
        echo "ReplacementContent:\n" . ($data['args']['ReplacementContent'] ?? '') . "\n";
    }
}
