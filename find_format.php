<?php
echo "=== Searching entire app for formatDa ===\n";
$dirs = [__DIR__, __DIR__ . '/../'];
foreach ($dirs as $baseDir) {
    $iter = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($baseDir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::LEAVES_ONLY
    );
    foreach ($iter as $file) {
        if (!$file->isFile() || $file->getExtension() !== 'php') continue;
        if (strpos($file->getPathname(), '/vendor/') !== false) continue;
        if (strpos($file->getPathname(), '/storage/') !== false) continue;
        $content = @file_get_contents($file->getPathname());
        if ($content === false) continue;
        if (preg_match('/function\s+formatDa/', $content)) {
            $path = str_replace('/usr/www/users/smartucbmh/', '', $file->getPathname());
            echo "\n--- $path ---\n";
            $lines = explode("\n", $content);
            foreach ($lines as $i => $line) {
                if (preg_match('/formatDa/', $line)) {
                    echo "  Line " . ($i+1) . ": " . trim($line) . "\n";
                }
            }
        }
    }
}

// Also check composer.json autoload for helpers
echo "\n=== composer.json autoload files ===\n";
$composer = json_decode(file_get_contents(__DIR__ . '/composer.json'), true);
if (isset($composer['autoload']['files'])) {
    foreach ($composer['autoload']['files'] as $f) {
        echo "  $f\n";
        if (file_exists(__DIR__ . '/' . $f)) {
            $content = file_get_contents(__DIR__ . '/' . $f);
            if (preg_match('/formatDa/', $content)) {
                echo "    ^^^ CONTAINS formatDa\n";
            }
        }
    }
}

// Check module composer.json files for autoloaded helpers
echo "\n=== Module composer.json autoload files ===\n";
$modDir = __DIR__ . '/Modules';
foreach (scandir($modDir) as $mod) {
    if ($mod === '.' || $mod === '..') continue;
    $cj = $modDir . '/' . $mod . '/composer.json';
    if (file_exists($cj)) {
        $c = json_decode(file_get_contents($cj), true);
        if (isset($c['autoload']['files'])) {
            echo "  $mod: " . implode(', ', $c['autoload']['files']) . "\n";
        }
    }
}
unlink(__FILE__);
