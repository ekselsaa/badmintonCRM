<?php
$transcriptPath = 'C:\\Users\\Lenovo\\.gemini\\antigravity-ide\\brain\\d95c3c85-a03d-4cc3-a4f9-f3f0293865b4\\.system_generated\\logs\\transcript.jsonl';
$handle = fopen($transcriptPath, 'r');
if (!$handle) {
    die("Failed to open transcript\n");
}

$viewedFiles = [];

// We will track the first time we see a VIEW_FILE response for each file path.
// When we view a file, the model sends a view_file tool_call. The next step is a VIEW_FILE step.
// Let's parse all lines.
$lastViewedPath = null;

while (($line = fgets($handle)) !== false) {
    $data = json_decode($line, true);
    if (!$data) {
        continue;
    }
    
    // Check if this step is a model response calling view_file
    if (isset($data['tool_calls'])) {
        foreach ($data['tool_calls'] as $tc) {
            if ($tc['name'] === 'view_file') {
                $path = $tc['args']['AbsolutePath'] ?? '';
                if ($path) {
                    $lastViewedPath = strtolower(realpath($path));
                }
            }
        }
    }
    
    // Check if this step is a VIEW_FILE type response
    if (($data['type'] ?? '') === 'VIEW_FILE' && $lastViewedPath) {
        if (!isset($viewedFiles[$lastViewedPath])) {
            $viewedFiles[$lastViewedPath] = [
                'step' => $data['step_index'] ?? '',
                'content' => $data['content'] ?? ''
            ];
            echo "Captured first VIEW_FILE content for $lastViewedPath at step " . ($data['step_index'] ?? '') . "\n";
        }
        $lastViewedPath = null;
    }
}
fclose($handle);

// Now let's save these contents to a temporary folder or print them
@mkdir(__DIR__ . '/recovered_files', 0777, true);
foreach ($viewedFiles as $path => $info) {
    $filename = basename($path);
    // The content has a header: "Created At: ... \nCompleted At: ... \nFile Path: ... \nTotal Lines: ... \nTotal Bytes: ... \nShowing lines ...\nThe following code has been modified to include a line number before every line, in the format: <line_number>: <original_line>..."
    // Let's write the raw content to a file, and we will clean it up later if it has line numbers.
    file_put_contents(__DIR__ . '/recovered_files/' . $filename . '.raw', $info['content']);
    echo "Saved raw file: $filename.raw\n";
}
