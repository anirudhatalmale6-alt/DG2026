<?php
echo "=== CIMSDocManager Views ===\n";
$dir = __DIR__ . '/Modules/CIMSDocManager/Resources/views';
if (is_dir($dir)) {
    $iter = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($iter as $f) {
        if ($f->isFile()) echo "  " . str_replace($dir . '/', '', $f->getPathname()) . "\n";
    }
} else {
    echo "  Directory not found: $dir\n";
}

echo "\n=== CIMSDocManager module.json ===\n";
$mj = __DIR__ . '/Modules/CIMSDocManager/module.json';
if (file_exists($mj)) echo file_get_contents($mj);
else echo "  Not found\n";

echo "\n\n=== CIMSDocManager ServiceProvider ===\n";
$provDir = __DIR__ . '/Modules/CIMSDocManager/Providers';
if (is_dir($provDir)) {
    foreach (scandir($provDir) as $f) {
        if ($f === '.' || $f === '..') continue;
        echo "\n--- $f ---\n";
        echo file_get_contents($provDir . '/' . $f);
    }
}
unlink(__FILE__);
