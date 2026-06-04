<?php
$transcriptPath = 'C:\\Users\\Lenovo\\.gemini\\antigravity-ide\\brain\\d95c3c85-a03d-4cc3-a4f9-f3f0293865b4\\.system_generated\\logs\\transcript.jsonl';
$handle = fopen($transcriptPath, 'r');
if (!$handle) {
    die("Failed to open transcript\n");
}

$edits = [];

while (($line = fgets($handle)) !== false) {
    $data = json_decode($line, true);
    if (!$data) {
        continue;
    }
    
    $step = $data['step_index'] ?? '';
    
    if (isset($data['tool_calls'])) {
        foreach ($data['tool_calls'] as $tc) {
            $name = $tc['name'] ?? '';
            $args = $tc['args'] ?? [];
            
            if ($name === 'replace_file_content') {
                $targetFile = trim($args['TargetFile'] ?? '', '"\'');
                $targetContent = $args['TargetContent'] ?? null;
                $replacementContent = $args['ReplacementContent'] ?? null;
                
                // Strip quotes if they are in content as well
                if ($targetContent !== null && is_string($targetContent)) {
                    // sometimes contents also get double-escaped or quoted
                    if (strpos($targetContent, '"') === 0 && substr($targetContent, -1) === '"') {
                        $targetContent = json_decode($targetContent);
                    }
                }
                if ($replacementContent !== null && is_string($replacementContent)) {
                    if (strpos($replacementContent, '"') === 0 && substr($replacementContent, -1) === '"') {
                        $replacementContent = json_decode($replacementContent);
                    }
                }
                
                if ($targetFile && $targetContent !== null && $replacementContent !== null) {
                    $edits[] = [
                        'step' => $step,
                        'type' => 'single',
                        'file' => strtolower(realpath($targetFile)),
                        'replacements' => [
                            [
                                'target' => $targetContent,
                                'replacement' => $replacementContent
                            ]
                        ]
                    ];
                }
            } else if ($name === 'multi_replace_file_content') {
                $targetFile = trim($args['TargetFile'] ?? '', '"\'');
                $chunks = $args['ReplacementChunks'] ?? [];
                
                if (is_string($chunks)) {
                    $chunks = json_decode($chunks, true);
                }
                
                if ($targetFile && !empty($chunks)) {
                    $reps = [];
                    foreach ($chunks as $chunk) {
                        $targetContent = $chunk['TargetContent'] ?? null;
                        $replacementContent = $chunk['ReplacementContent'] ?? null;
                        
                        if ($targetContent !== null && is_string($targetContent)) {
                            if (strpos($targetContent, '"') === 0 && substr($targetContent, -1) === '"') {
                                $targetContent = json_decode($targetContent);
                            }
                        }
                        if ($replacementContent !== null && is_string($replacementContent)) {
                            if (strpos($replacementContent, '"') === 0 && substr($replacementContent, -1) === '"') {
                                $replacementContent = json_decode($replacementContent);
                            }
                        }
                        
                        if ($targetContent !== null && $replacementContent !== null) {
                            $reps[] = [
                                'target' => $targetContent,
                                'replacement' => $replacementContent
                            ];
                        }
                    }
                    if (!empty($reps)) {
                        $edits[] = [
                            'step' => $step,
                            'type' => 'multi',
                            'file' => strtolower(realpath($targetFile)),
                            'replacements' => $reps
                        ];
                    }
                }
            }
        }
    }
}
fclose($handle);

// Sort edits by step descending (latest first)
usort($edits, function($a, $b) {
    return $b['step'] - $a['step'];
});

echo "Found " . count($edits) . " edit operations in transcript.\n";

// We will track the content of each file in memory while patching, to avoid writing repeatedly.
// Also keep backup of original files before we start patching!
$fileContents = [];

foreach ($edits as $edit) {
    $file = $edit['file'];
    if (!$file || !file_exists($file)) {
        echo "File does not exist: " . ($edit['file'] ?: 'unknown') . " (Step {$edit['step']})\n";
        continue;
    }
    
    if (!isset($fileContents[$file])) {
        $fileContents[$file] = file_get_contents($file);
        // Create a backup file in recovered_files/backups/
        $relative = str_replace(strtolower(realpath(__DIR__)) . DIRECTORY_SEPARATOR, "", $file);
        $backupDest = __DIR__ . '/recovered_files/backups/' . $relative;
        @mkdir(dirname($backupDest), 0777, true);
        file_put_contents($backupDest, $fileContents[$file]);
    }
    
    $content = $fileContents[$file];
    $success = true;
    
    foreach ($edit['replacements'] as $rep) {
        $search = $rep['replacement']; // We want to search for the ReplacementContent
        $replace = $rep['target'];      // And replace it with the original TargetContent
        
        // Let's normalize newlines to avoid mismatch due to CRLF / LF
        $contentNorm = str_replace("\r\n", "\n", $content);
        $searchNorm = str_replace("\r\n", "\n", $search);
        $replaceNorm = str_replace("\r\n", "\n", $replace);
        
        $pos = strpos($contentNorm, $searchNorm);
        if ($pos !== false) {
            $contentNorm = str_replace($searchNorm, $replaceNorm, $contentNorm);
            $content = $contentNorm;
            echo "  [Step {$edit['step']}] Successfully reversed edit on " . basename($file) . "\n";
        } else {
            // If search string not found, check if it's already in the target state
            if (strpos($contentNorm, $replaceNorm) !== false) {
                echo "  [Step {$edit['step']}] target already present in " . basename($file) . "\n";
            } else {
                echo "  [Step {$edit['step']}] WARNING: Could not find replacement content in " . basename($file) . "\n";
                // Let's print out what we were looking for and what we found
                echo "    Searching for: " . substr($searchNorm, 0, 50) . "...\n";
                $success = false;
            }
        }
    }
    
    $fileContents[$file] = $content;
}

// Now save the updated file contents back to disk!
foreach ($fileContents as $file => $content) {
    file_put_contents($file, $content);
    echo "Saved reverted file: $file\n";
}
