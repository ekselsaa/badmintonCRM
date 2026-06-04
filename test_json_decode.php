<?php
$failedSteps = [472];
$transcriptPath = 'C:\\Users\\Lenovo\\.gemini\\antigravity-ide\\brain\\d95c3c85-a03d-4cc3-a4f9-f3f0293865b4\\.system_generated\\logs\\transcript.jsonl';
$handle = fopen($transcriptPath, 'r');
while (($line = fgets($handle)) !== false) {
    $data = json_decode($line, true);
    if (isset($data['step_index']) && $data['step_index'] == 472) {
        $tc = $data['tool_calls'][0];
        $replacement = $tc['args']['ReplacementContent'];
        echo "Raw type: " . gettype($replacement) . "\n";
        echo "Raw length: " . strlen($replacement) . "\n";
        echo "First 10 chars: " . bin2hex(substr($replacement, 0, 10)) . " (" . substr($replacement, 0, 10) . ")\n";
        echo "Last 10 chars: " . bin2hex(substr($replacement, -10)) . " (" . substr($replacement, -10) . ")\n";
        
        // Let's check json_decode
        $decoded = json_decode($replacement);
        if ($decoded === null) {
            echo "Direct json_decode failed: " . json_last_error_msg() . "\n";
            // Let's try wrapping it in double quotes or decapsulating it
            // Wait, does it start with "\""?
        } else {
            echo "Direct json_decode succeeded! Decoded length: " . strlen($decoded) . "\n";
        }
        break;
    }
}
fclose($handle);
