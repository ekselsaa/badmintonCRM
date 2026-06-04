<?php
$transcriptPath = 'C:\\Users\\Lenovo\\.gemini\\antigravity-ide\\brain\\d95c3c85-a03d-4cc3-a4f9-f3f0293865b4\\.system_generated\\logs\\transcript.jsonl';
$handle = fopen($transcriptPath, 'r');
if (!$handle) {
    die("Failed to open transcript\n");
}

$completeFiles = [];

while (($line = fgets($handle)) !== false) {
    $data = json_decode($line, true);
    if (!$data) continue;
    
    if (($data['type'] ?? '') === 'VIEW_FILE') {
        $content = $data['content'] ?? '';
        if (strpos($content, 'The above content shows the entire, complete file contents of the requested file.') !== false) {
            if (preg_match('/File Path: `file:\/\/\/(.*?)`/', $content, $matches)) {
                $filePath = str_replace('/', DIRECTORY_SEPARATOR, $matches[1]);
                
                // We keep the first complete view of this file in the conversation
                $filePathKey = strtolower($filePath);
                if (!isset($completeFiles[$filePathKey])) {
                    $completeFiles[$filePathKey] = [
                        'path' => $filePath,
                        'step' => $data['step_index'],
                        'content' => $content
                    ];
                }
            }
        }
    }
}
fclose($handle);

echo "Found " . count($completeFiles) . " completely viewed files.\n";

$workspaceDir = strtolower(realpath(__DIR__)) . DIRECTORY_SEPARATOR;

// Write clean versions to recovered_files/complete/
@mkdir(__DIR__ . '/recovered_files/complete', 0777, true);
foreach ($completeFiles as $key => $info) {
    $path = $info['path'];
    $lines = explode("\n", $info['content']);
    $codeLines = [];
    $inCode = false;
    $expectedLineNo = 1;
    
    foreach ($lines as $line) {
        if (!$inCode) {
            if (strpos($line, 'The following code has been modified to include a line number') !== false) {
                $inCode = true;
            }
            continue;
        }
        
        $prefix1 = $expectedLineNo . ": ";
        $prefix2 = $expectedLineNo . ":";
        
        if (strpos($line, $prefix1) === 0) {
            $codeLines[] = substr($line, strlen($prefix1));
            $expectedLineNo++;
        } else if (strpos($line, $prefix2) === 0) {
            $codeLines[] = substr($line, strlen($prefix2));
            $expectedLineNo++;
        } else {
            if ($expectedLineNo > 1) {
                break;
            }
        }
    }
    
    $cleanContent = implode("\n", $codeLines);
    
    $pathLower = strtolower($path);
    if (strpos($pathLower, $workspaceDir) === 0) {
        $relative = substr($path, strlen($workspaceDir));
    } else {
        // Skip files outside the workspace
        continue;
    }
    
    $dest = __DIR__ . '/recovered_files/complete/' . $relative;
    @mkdir(dirname($dest), 0777, true);
    file_put_contents($dest, $cleanContent);
    echo "Saved clean complete file: $relative (Step {$info['step']})\n";
}
