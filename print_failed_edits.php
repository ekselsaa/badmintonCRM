<?php
$transcriptPath = 'C:\\Users\\Lenovo\\.gemini\\antigravity-ide\\brain\\d95c3c85-a03d-4cc3-a4f9-f3f0293865b4\\.system_generated\\logs\\transcript.jsonl';
$handle = fopen($transcriptPath, 'r');
if (!$handle) {
    die("Failed to open transcript\n");
}

$failedSteps = [489, 472, 440, 422, 401, 343, 339, 335, 329, 313, 311, 260, 256, 157, 79];

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
                    $details[] = [
                        'step' => $step,
                        'name' => $name,
                        'file' => $tc['args']['TargetFile'] ?? '',
                        'args' => $tc['args']
                    ];
                }
            }
        }
    }
}
fclose($handle);

@mkdir(__DIR__ . '/recovered_files/failed_details', 0777, true);
foreach ($details as $det) {
    $filename = "step_{$det['step']}_" . basename(trim($det['file'], '"\'')) . ".json";
    file_put_contents(__DIR__ . '/recovered_files/failed_details/' . $filename, json_encode($det, JSON_PRETTY_PRINT));
    echo "Saved details for step {$det['step']} to $filename\n";
}
