<?php
/**
 * Standalone System Cleanup Utility Script
 *
 * Digunakan untuk membersihkan log lama, cache framework, session kedaluwarsa, 
 * dan phpunit cache. 
 *
 * CARA PENGGUNAAN:
 *   Mode Simulasi (Aman): php cleanup_system.php --dry-run (atau tanpa argumen)
 *   Mode Eksekusi Riil : php cleanup_system.php --force
 */

$options = getopt('', ['dry-run', 'force']);
$isDryRun = isset($options['dry-run']) || !isset($options['force']);

echo "==================================================" . PHP_EOL;
echo "   GOR Anbiyaa Sport — System Cleanup Utility     " . PHP_EOL;
echo "==================================================" . PHP_EOL;
echo "Mode: " . ($isDryRun ? "DRY-RUN (Simulasi)" : "FORCE (Eksekusi Riil)") . PHP_EOL;
echo "Waktu: " . date('Y-m-d H:i:s') . PHP_EOL;
echo "--------------------------------------------------" . PHP_EOL;

$totalSize = 0;
$filesDeleted = 0;
$filesSkipped = 0;

// Daftar direktori & file target pembersihan
$targets = [
    'logs' => [
        'path' => __DIR__ . '/storage/logs',
        'pattern' => '/^laravel-.*\.log$/', // Hapus log harian lama
        'truncate' => ['laravel.log'],      // Kosongkan log utama
    ],
    'sessions' => [
        'path' => __DIR__ . '/storage/framework/sessions',
        'pattern' => '/^[a-zA-Z0-9]{40}$/', // Pola file session Laravel standar
    ],
    'views' => [
        'path' => __DIR__ . '/storage/framework/views',
        'pattern' => '/^[a-f0-9]{40}\.php$/', // Pola file blade terkompilasi
    ],
    'phpunit_cache' => [
        'path' => __DIR__,
        'files' => ['.phpunit.result.cache'],
    ]
];

foreach ($targets as $category => $config) {
    echo "Memeriksa kategori: [" . strtoupper($category) . "]..." . PHP_EOL;
    $path = $config['path'];

    if (!is_dir($path)) {
        echo "  [SKIP] Direktori tidak ditemukan: {$path}" . PHP_EOL;
        continue;
    }

    // Pembersihan berbasis file terdaftar
    if (isset($config['files'])) {
        foreach ($config['files'] as $file) {
            $fullPath = $path . '/' . $file;
            if (file_exists($fullPath)) {
                $size = filesize($fullPath);
                $totalSize += $size;
                $sizeKb = round($size / 1024, 2);
                if ($isDryRun) {
                    echo "  [Akan Dihapus] File: {$file} ({$sizeKb} KB)" . PHP_EOL;
                    $filesSkipped++;
                } else {
                    if (unlink($fullPath)) {
                        echo "  [DELETED] File: {$file} ({$sizeKb} KB)" . PHP_EOL;
                        $filesDeleted++;
                    } else {
                        echo "  [GAGAL] File: {$file}" . PHP_EOL;
                    }
                }
            }
        }
    }

    // Pembersihan berbasis pattern di dalam direktori
    if (isset($config['pattern'])) {
        $dir = opendir($path);
        if ($dir) {
            while (($file = readdir($dir)) !== false) {
                if ($file === '.' || $file === '..' || $file === '.gitignore') {
                    continue;
                }
                if (preg_match($config['pattern'], $file)) {
                    $fullPath = $path . '/' . $file;
                    if (is_file($fullPath)) {
                        $size = filesize($fullPath);
                        $totalSize += $size;
                        $sizeKb = round($size / 1024, 2);
                        if ($isDryRun) {
                            echo "  [Akan Dihapus] {$file} ({$sizeKb} KB)" . PHP_EOL;
                            $filesSkipped++;
                        } else {
                            if (unlink($fullPath)) {
                                echo "  [DELETED] {$file} ({$sizeKb} KB)" . PHP_EOL;
                                $filesDeleted++;
                            } else {
                                echo "  [GAGAL] {$file}" . PHP_EOL;
                            }
                        }
                    }
                }
            }
            closedir($dir);
        }
    }

    // Pengosongan/Truncate file log utama
    if (isset($config['truncate'])) {
        foreach ($config['truncate'] as $file) {
            $fullPath = $path . '/' . $file;
            if (file_exists($fullPath) && filesize($fullPath) > 0) {
                $size = filesize($fullPath);
                $totalSize += $size;
                $sizeKb = round($size / 1024, 2);
                if ($isDryRun) {
                    echo "  [Akan Dikosongkan] Log: {$file} ({$sizeKb} KB)" . PHP_EOL;
                    $filesSkipped++;
                } else {
                    $fh = fopen($fullPath, 'w');
                    if ($fh) {
                        fclose($fh);
                        echo "  [TRUNCATED] Log: {$file} ({$sizeKb} KB)" . PHP_EOL;
                        $filesDeleted++;
                    } else {
                        echo "  [GAGAL KOSONGKAN] Log: {$file}" . PHP_EOL;
                    }
                }
            }
        }
    }
}

$totalSizeMb = round($totalSize / (1024 * 1024), 3);
echo "--------------------------------------------------" . PHP_EOL;
if ($isDryRun) {
    echo "SELESAI (Simulasi): Ditemukan {$filesSkipped} file yang dapat dibersihkan." . PHP_EOL;
    echo "Total ruang yang akan dibebaskan: {$totalSizeMb} MB." . PHP_EOL;
    echo "Jalankan perintah berikut untuk mengeksekusi pembersihan nyata:" . PHP_EOL;
    echo "  php cleanup_system.php --force" . PHP_EOL;
} else {
    echo "SELESAI (Eksekusi): Berhasil menghapus/mengosongkan {$filesDeleted} file." . PHP_EOL;
    echo "Total ruang dibebaskan: {$totalSizeMb} MB." . PHP_EOL;
}
echo "==================================================" . PHP_EOL;
