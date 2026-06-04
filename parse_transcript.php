<?php
$transcriptPath = 'C:\\Users\\Lenovo\\.gemini\\antigravity-ide\\brain\\d95c3c85-a03d-4cc3-a4f9-f3f0293865b4\\.system_generated\\logs\\transcript.jsonl';
if (!file_exists($transcriptPath)) {
    die("File not found\n");
}

$handle = fopen($transcriptPath, 'r');
if (!$handle) {
    die("Could not open file\n");
}

$lineNumber = 0;
while (($line = fgets($handle)) !== false) {
    $lineNumber++;
    $data = json_decode($line, true);
    if (!$data) {
        continue;
    }
    
    $step = $data['step_index'] ?? '';
    $type = $data['type'] ?? '';
    $source = $data['source'] ?? '';
    
    // Check if it's a tool call
    if (isset($data['tool_calls'])) {
        foreach ($data['tool_calls'] as $tc) {
            $name = $tc['name'] ?? '';
            $args = $tc['args'] ?? [];
            if ($name === 'view_file' || $name === 'replace_file_content' || $name === 'multi_replace_file_content' || $name === 'write_to_file') {
                $targetFile = $args['AbsolutePath'] ?? $args['TargetFile'] ?? '';
                if ($targetFile) {
                    echo "Step $step ($type by $source): $name -> $targetFile\n";
                }
            }
        }
    }
}
fclose($handle);
