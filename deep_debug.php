<?php
// Find EVERY place formatDateValue is declared as a PHP function (not JS)
echo "=== Deep search: ALL formatDateValue declarations ===\n\n";

$baseDir = __DIR__;
$iter = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($baseDir, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::LEAVES_ONLY
);

foreach ($iter as $file) {
    if (!$file->isFile()) continue;
    $ext = $file->getExtension();
    if (!in_array($ext, ['php', 'blade'])) continue;
    if (strpos($file->getPathname(), '/vendor/') !== false) continue;
    if (strpos($file->getPathname(), '/storage/framework/') !== false) continue;
    
    $content = @file_get_contents($file->getPathname());
    if ($content === false) continue;
    
    // Match PHP function declarations (not JS)
    if (preg_match('/function\s+formatDateValue\s*\(/', $content)) {
        $path = str_replace($baseDir . '/', '', $file->getPathname());
        $lines = explode("\n", $content);
        echo "FILE: $path\n";
        foreach ($lines as $i => $line) {
            if (preg_match('/function\s+formatDateValue/', $line) || 
                strpos($line, 'function_exists') !== false && strpos($line, 'formatDateValue') !== false) {
                echo "  Line " . ($i+1) . ": " . trim($line) . "\n";
            }
        }
        echo "\n";
    }
}

// Check how ClientMasterNew helpers.php is loaded
echo "=== How is ClientMasterNew helpers.php autoloaded? ===\n";
$cmn_composer = $baseDir . '/Modules/ClientMasterNew/composer.json';
if (file_exists($cmn_composer)) {
    echo "ClientMasterNew composer.json:\n" . file_get_contents($cmn_composer) . "\n\n";
}

// Check ClientMasterNew service provider for helpers loading
$cmn_providers = $baseDir . '/Modules/ClientMasterNew/Providers';
if (is_dir($cmn_providers)) {
    foreach (scandir($cmn_providers) as $f) {
        if ($f === '.' || $f === '..') continue;
        $content = file_get_contents($cmn_providers . '/' . $f);
        if (strpos($content, 'helpers') !== false || strpos($content, 'Helpers') !== false || strpos($content, 'require') !== false || strpos($content, 'include') !== false) {
            echo "Provider $f references helpers:\n";
            $lines = explode("\n", $content);
            foreach ($lines as $i => $line) {
                if (stripos($line, 'helper') !== false || stripos($line, 'require') !== false || stripos($line, 'include') !== false) {
                    echo "  Line " . ($i+1) . ": " . trim($line) . "\n";
                }
            }
            echo "\n";
        }
    }
}

// Check module.json for autoload
$cmn_module = $baseDir . '/Modules/ClientMasterNew/module.json';
if (file_exists($cmn_module)) {
    $mj = json_decode(file_get_contents($cmn_module), true);
    if (isset($mj['files'])) {
        echo "ClientMasterNew module.json files: " . json_encode($mj['files']) . "\n";
    }
}

// Also check if the function is in the cached/compiled autoload
echo "\n=== Checking vendor/composer autoload files ===\n";
$autoloadFiles = $baseDir . '/vendor/composer/autoload_files.php';
if (file_exists($autoloadFiles)) {
    $files = include($autoloadFiles);
    foreach ($files as $hash => $path) {
        if (stripos($path, 'helper') !== false || stripos($path, 'Helper') !== false) {
            echo "  $path\n";
        }
    }
}

unlink(__FILE__);
