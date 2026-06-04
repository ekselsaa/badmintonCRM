<?php
$transcriptPath = 'C:\\Users\\Lenovo\\.gemini\\antigravity-ide\\brain\\d95c3c85-a03d-4cc3-a4f9-f3f0293865b4\\.system_generated\\logs\\transcript.jsonl';
$handle = fopen($transcriptPath, 'r');
while (($line = fgets($handle)) !== false) {
    $data = json_decode($line, true);
    if (isset($data['step_index']) && $data['step_index'] == 311) {
        $tc = $data['tool_calls'][0];
        $target = $tc['args']['TargetContent'];
        echo "=== TARGET CONTENT ===\n";
        echo $target . "\n";
        echo "=== END ===\n";
        break;
    }
}
fclose($handle);
