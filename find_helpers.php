<?php
echo "=== All helpers.php files in Modules ===\n";
$modulesDir = __DIR__ . '/Modules';
$iter = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($modulesDir));
foreach ($iter as $file) {
    if ($file->isFile() && $file->getFilename() === 'helpers.php') {
        $path = str_replace(__DIR__ . '/', '', $file->getPathname());
        $content = file_get_contents($file->getPathname());
        // Check if formatDataValue is declared
        if (strpos($content, 'formatDataValue') !== false) {
            $hasCheck = strpos($content, 'function_exists') !== false;
            echo "\n$path — CONTAINS formatDataValue" . ($hasCheck ? " (has function_exists check)" : " (NO function_exists check)") . "\n";
            // Show relevant lines
            $lines = explode("\n", $content);
            foreach ($lines as $i => $line) {
                if (strpos($line, 'formatDataValue') !== false || strpos($line, 'function_exists') !== false) {
                    echo "  Line " . ($i+1) . ": " . trim($line) . "\n";
                }
            }
        }
    }
}
unlink(__FILE__);
