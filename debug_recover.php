<?php
$transcriptPath = 'C:\\Users\\Lenovo\\.gemini\\antigravity-ide\\brain\\d95c3c85-a03d-4cc3-a4f9-f3f0293865b4\\.system_generated\\logs\\transcript.jsonl';
$handle = fopen($transcriptPath, 'r');
if (!$handle) {
    die("Failed to open transcript\n");
}

for ($i = 0; $i < 1000; $i++) {
    $line = fgets($handle);
    if ($line === false) break;
    $data = json_decode($line, true);
    if (!$data) continue;
    
    $type = $data['type'] ?? '';
    if (strpos($type, 'VIEW') !== false || strpos($type, 'view') !== false) {
        echo "Line $i: step " . ($data['step_index'] ?? '') . " type: " . $type . "\n";
        if (isset($data['tool_calls'])) {
            echo "  tool_calls count: " . count($data['tool_calls']) . "\n";
        }
        // print keys
        echo "  keys: " . implode(', ', array_keys($data)) . "\n";
        if (isset($data['content'])) {
            echo "  content prefix: " . substr($data['content'], 0, 50) . "\n";
        }
    }
}
fclose($handle);
