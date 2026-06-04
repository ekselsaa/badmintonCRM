<?php
$transcriptPath = 'C:\\Users\\Lenovo\\.gemini\\antigravity-ide\\brain\\d95c3c85-a03d-4cc3-a4f9-f3f0293865b4\\.system_generated\\logs\\transcript.jsonl';
$handle = fopen($transcriptPath, 'r');
while (($line = fgets($handle)) !== false) {
    $data = json_decode($line, true);
    if (isset($data['tool_calls'])) {
        foreach ($data['tool_calls'] as $tc) {
            if ($tc['name'] === 'view_file' && strpos($tc['args']['AbsolutePath'] ?? '', 'DatabaseSeeder.php') !== false) {
                // Let's print this step and the next step (which contains the tool response)
                echo "Found view_file call in step " . $data['step_index'] . "\n";
                // Let's print the next few lines from the log to see where the response is.
                for ($i = 0; $i < 5; $i++) {
                    $nextLine = fgets($handle);
                    if ($nextLine) {
                        $nextData = json_decode($nextLine, true);
                        echo "Next step: " . ($nextData['step_index'] ?? 'no step') . " type: " . ($nextData['type'] ?? '') . " status: " . ($nextData['status'] ?? '') . "\n";
                        if (isset($nextData['content'])) {
                            echo "Content length: " . strlen($nextData['content']) . "\n";
                            echo "Content prefix: " . substr($nextData['content'], 0, 100) . "\n";
                        }
                        if (isset($nextData['output'])) {
                            echo "Output length: " . strlen($nextData['output']) . "\n";
                            echo "Output prefix: " . substr($nextData['output'], 0, 100) . "\n";
                        }
                    }
                }
                break 2;
            }
        }
    }
}
fclose($handle);
