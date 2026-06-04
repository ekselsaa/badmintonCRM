<?php
$transcriptPath = 'C:\\Users\\Lenovo\\.gemini\\antigravity-ide\\brain\\d95c3c85-a03d-4cc3-a4f9-f3f0293865b4\\.system_generated\\logs\\transcript.jsonl';
$handle = fopen($transcriptPath, 'r');
while (($line = fgets($handle)) !== false) {
    $data = json_decode($line, true);
    if (isset($data['step_index']) && ($data['step_index'] == 454 || $data['step_index'] == 458)) {
        echo "=== STEP " . $data['step_index'] . " ===\n";
        echo json_encode($data, JSON_PRETTY_PRINT) . "\n";
    }
}
fclose($handle);
