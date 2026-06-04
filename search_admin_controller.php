<?php
$transcriptPath = 'C:\\Users\\Lenovo\\.gemini\\antigravity-ide\\brain\\d95c3c85-a03d-4cc3-a4f9-f3f0293865b4\\.system_generated\\logs\\transcript.jsonl';
$handle = fopen($transcriptPath, 'r');
while (($line = fgets($handle)) !== false) {
    if (strpos($line, 'AdminController.php') !== false) {
        $data = json_decode($line, true);
        if (!$data) continue;
        echo "Step: " . ($data['step_index'] ?? 'no step') . " | type: " . ($data['type'] ?? '') . " | source: " . ($data['source'] ?? '') . "\n";
        if (isset($data['content'])) {
            echo "  content: " . substr($data['content'], 0, 100) . "\n";
        }
        if (isset($data['tool_calls'])) {
            foreach ($data['tool_calls'] as $tc) {
                echo "  tool_call: " . $tc['name'] . " with path " . ($tc['args']['AbsolutePath'] ?? $tc['args']['TargetFile'] ?? '') . "\n";
            }
        }
    }
}
fclose($handle);
