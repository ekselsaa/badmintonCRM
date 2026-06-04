<?php
$transcriptPath = 'C:\\Users\\Lenovo\\.gemini\\antigravity-ide\\brain\\d95c3c85-a03d-4cc3-a4f9-f3f0293865b4\\.system_generated\\logs\\transcript.jsonl';
$handle = fopen($transcriptPath, 'r');
while (($line = fgets($handle)) !== false) {
    $data = json_decode($line, true);
    if (isset($data['step_index']) && $data['step_index'] == 248) {
        $tc = $data['tool_calls'][0];
        $targetFile = trim($tc['args']['TargetFile'] ?? '', '"\'');
        echo "targetFile from JSON: " . $tc['args']['TargetFile'] . "\n";
        echo "Trimmed: $targetFile\n";
        echo "Realpath: " . realpath($targetFile) . "\n";
        echo "Realpath lower: " . strtolower(realpath($targetFile)) . "\n";
        
        $chunks = $tc['args']['ReplacementChunks'] ?? [];
        echo "Chunks type: " . gettype($chunks) . "\n";
        if (is_string($chunks)) {
            $decoded = json_decode($chunks, true);
            echo "Decoded chunks count: " . count($decoded) . "\n";
        }
    }
}
fclose($handle);
