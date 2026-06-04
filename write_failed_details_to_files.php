<?php
$failedSteps = [329, 335, 339, 472];
$transcriptPath = 'C:\\Users\\Lenovo\\.gemini\\antigravity-ide\\brain\\d95c3c85-a03d-4cc3-a4f9-f3f0293865b4\\.system_generated\\logs\\transcript.jsonl';
$handle = fopen($transcriptPath, 'r');
if (!$handle) {
    die("Failed to open transcript\n");
}

$details = [];

while (($line = fgets($handle)) !== false) {
    $data = json_decode($line, true);
    if (!$data) continue;
    
    $step = $data['step_index'] ?? '';
    if (in_array($step, $failedSteps)) {
        if (isset($data['tool_calls'])) {
            foreach ($data['tool_calls'] as $tc) {
                $name = $tc['name'] ?? '';
                if ($name === 'replace_file_content' || $name === 'multi_replace_file_content') {
                    $details[$step] = $tc['args'];
                }
            }
        }
    }
}
fclose($handle);

foreach ($details as $step => $args) {
    $target = $args['TargetContent'] ?? '';
    $replacement = $args['ReplacementContent'] ?? '';
    
    if (is_string($target) && strpos($target, '"') === 0 && substr($target, -1) === '"') {
        $target = json_decode($target);
    }
    if (is_string($replacement) && strpos($replacement, '"') === 0 && substr($replacement, -1) === '"') {
        $replacement = json_decode($replacement);
    }
    
    file_put_contents(__DIR__ . "/recovered_files/failed_details/step_{$step}_target.txt", $target);
    file_put_contents(__DIR__ . "/recovered_files/failed_details/step_{$step}_replacement.txt", $replacement);
    echo "Wrote cleaned step $step target and replacement.\n";
}
