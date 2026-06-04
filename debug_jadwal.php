<?php
$transcriptPath = 'C:\\Users\\Lenovo\\.gemini\\antigravity-ide\\brain\\d95c3c85-a03d-4cc3-a4f9-f3f0293865b4\\.system_generated\\logs\\transcript.jsonl';
$handle = fopen($transcriptPath, 'r');
if (!$handle) {
    die("Failed to open transcript\n");
}

while (($line = fgets($handle)) !== false) {
    $data = json_decode($line, true);
    if (!$data) continue;
    
    if (isset($data['tool_calls'])) {
        foreach ($data['tool_calls'] as $tc) {
            if ($tc['name'] === 'view_file' && strpos($tc['args']['AbsolutePath'] ?? '', 'jadwal') !== false) {
                echo "=== Step " . $data['step_index'] . " ===\n";
                echo "File: " . $tc['args']['AbsolutePath'] . "\n";
                if (isset($data['content'])) {
                    echo "Content length: " . strlen($data['content']) . "\n";
                    echo "First 100 chars: " . substr($data['content'], 0, 100) . "\n";
                    echo "Last 100 chars: " . substr($data['content'], -100) . "\n";
                }
                echo "\n";
            }
        }
    }
}
fclose($handle);
