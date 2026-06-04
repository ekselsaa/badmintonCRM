<?php
$transcriptPath = 'C:\\Users\\Lenovo\\.gemini\\antigravity-ide\\brain\\d95c3c85-a03d-4cc3-a4f9-f3f0293865b4\\.system_generated\\logs\\transcript.jsonl';
$handle = fopen($transcriptPath, 'r');
while (($line = fgets($handle)) !== false) {
    $data = json_decode($line, true);
    if (isset($data['step_index']) && $data['step_index'] == 444) {
        echo "=== STEP 444 ===\n";
        echo "Length: " . strlen($data['content']) . "\n";
        echo "Prefix: " . substr($data['content'], 0, 300) . "\n";
        echo "Suffix: " . substr($data['content'], -300) . "\n";
        break;
    }
}
fclose($handle);
