<?php
echo "=== Searching for formatDataValue in ALL PHP files ===\n";
$dir = __DIR__ . '/Modules';
$iter = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
$count = 0;
foreach ($iter as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        if (strpos($content, 'formatDataValue') !== false) {
            $path = str_replace(__DIR__ . '/', '', $file->getPathname());
            echo "\n--- $path ---\n";
            $lines = explode("\n", $content);
            foreach ($lines as $i => $line) {
                if (strpos($line, 'formatDataValue') !== false) {
                    echo "  Line " . ($i+1) . ": " . trim($line) . "\n";
                }
            }
            $count++;
        }
    }
}
echo "\nTotal files found: $count\n";

// Also check for Helpers directories
echo "\n=== Helpers directories ===\n";
foreach (scandir($dir) as $mod) {
    if ($mod === '.' || $mod === '..') continue;
    $hDir = $dir . '/' . $mod . '/Helpers';
    if (is_dir($hDir)) {
        echo "$mod/Helpers/: ";
        echo implode(', ', array_diff(scandir($hDir), ['.', '..'])) . "\n";
    }
}
unlink(__FILE__);
