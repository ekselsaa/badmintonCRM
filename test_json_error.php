<?php
$transcriptPath = 'C:\\Users\\Lenovo\\.gemini\\antigravity-ide\\brain\\d95c3c85-a03d-4cc3-a4f9-f3f0293865b4\\.system_generated\\logs\\transcript.jsonl';
$handle = fopen($transcriptPath, 'r');
while (($line = fgets($handle)) !== false) {
    $data = json_decode($line, true);
    if (isset($data['step_index']) && $data['step_index'] == 248) {
        $tc = $data['tool_calls'][0];
        $chunks = $tc['args']['ReplacementChunks'] ?? null;
        if ($chunks === null) {
            echo "chunks is null\n";
        } else {
            echo "chunks type: " . gettype($chunks) . "\n";
            if (is_string($chunks)) {
                echo "chunks length: " . strlen($chunks) . "\n";
                // Let's test json_decode
                $decoded = json_decode($chunks, true);
                if ($decoded === null) {
                    echo "Decode failed: " . json_last_error_msg() . "\n";
                    // Let's print the first 200 chars and last 200 chars of $chunks
                    echo "Prefix: " . substr($chunks, 0, 200) . "\n";
                    echo "Suffix: " . substr($chunks, -200) . "\n";
                } else {
                    echo "Decode successful! Chunks count: " . count($decoded) . "\n";
                }
            }
        }
    }
}
fclose($handle);
