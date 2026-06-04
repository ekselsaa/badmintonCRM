<?php
$transcriptPath = 'C:\\Users\\Lenovo\\.gemini\\antigravity-ide\\brain\\d95c3c85-a03d-4cc3-a4f9-f3f0293865b4\\.system_generated\\logs\\transcript.jsonl';
$handle = fopen($transcriptPath, 'r');
while (($line = fgets($handle)) !== false) {
    if (strpos($line, 'AdminController.php') !== false) {
        $data = json_decode($line, true);
        if (!$data) continue;
        if (isset($data['tool_calls'])) {
            foreach ($data['tool_calls'] as $tc) {
                if ($tc['name'] === 'view_file') {
                    $args = $tc['args'];
                    echo "Step " . $data['step_index'] . ": StartLine=" . ($args['StartLine'] ?? 'none') . " EndLine=" . ($args['EndLine'] ?? 'none') . "\n";
                }
            }
        }
    }
}
fclose($handle);
