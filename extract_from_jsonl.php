<?php
$transcriptPath = 'C:\\Users\\Lenovo\\.gemini\\antigravity-ide\\brain\\d95c3c85-a03d-4cc3-a4f9-f3f0293865b4\\.system_generated\\logs\\transcript.jsonl';
$handle = fopen($transcriptPath, 'r');
if (!$handle) {
    die("Failed to open transcript\n");
}

$filesData = [];

while (($line = fopen($transcriptPath, 'r') ? fgets($handle) : false) !== false) {
    $data = json_decode($line, true);
    if (!$data) {
        continue;
    }
    
    if (($data['type'] ?? '') === 'VIEW_FILE') {
        $content = $data['content'] ?? '';
        if (!$content) continue;
        
        // Find File Path
        if (preg_match('/File Path: `file:\/\/\/(.*?)`/', $content, $matches)) {
            $filePath = str_replace('/', DIRECTORY_SEPARATOR, $matches[1]);
            // Under Windows, let's normalize drive letter: c: vs C:
            $filePath = lowercase_drive($filePath);
            
            if (!isset($filesData[$filePath])) {
                $filesData[$filePath] = [
                    'step' => $data['step_index'] ?? '',
                    'raw_content' => $content
                ];
            }
        }
    }
}
fclose($handle);

function lowercase_drive($path) {
    if (preg_match('/^([a-zA-Z]):(.*)$/', $path, $m)) {
        return strtolower($m[1]) . ':' . $m[2];
    }
    return $path;
}

echo "Found original viewed contents for " . count($filesData) . " files:\n";
foreach ($filesData as $path => $info) {
    echo "- $path (Step {$info['step']})\n";
}

// Clean and write
@mkdir(__DIR__ . '/recovered_files', 0777, true);
foreach ($filesData as $path => $info) {
    $lines = explode("\n", $info['raw_content']);
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
        
        // We are in the code section
        // A line should start with "<expectedLineNo>: " or "<expectedLineNo>:"
        $prefix1 = $expectedLineNo . ": ";
        $prefix2 = $expectedLineNo . ":";
        
        if (strpos($line, $prefix1) === 0) {
            $codeLines[] = substr($line, strlen($prefix1));
            $expectedLineNo++;
        } else if (strpos($line, $prefix2) === 0) {
            $codeLines[] = substr($line, strlen($prefix2));
            $expectedLineNo++;
        } else {
            // It might be a line that got wrapped or some other text (e.g. "Showing lines ...")
            // Or the file reading ended.
            if ($expectedLineNo > 1) {
                // We reached the end of the file view
                break;
            }
        }
    }
    
    $cleanContent = implode("\n", $codeLines);
    // Write to recovered_files directory preserving directory structure under badmintonCRM
    // The path is like c:\xampp\htdocs\badmintonCRM\app\Models\User.php
    // We want to write to c:\xampp\htdocs\badmintonCRM\recovered_files\app\Models\User.php
    $relative = str_replace(lowercase_drive(__DIR__ . DIRECTORY_SEPARATOR), "", lowercase_drive($path));
    $dest = __DIR__ . DIRECTORY_SEPARATOR . 'recovered_files' . DIRECTORY_SEPARATOR . $relative;
    @mkdir(dirname($dest), 0777, true);
    file_put_contents($dest, $cleanContent);
    echo "Recovered and saved: $relative\n";
}
