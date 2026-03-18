<?php
echo "=== CIMSCore Providers ===\n";
$provDir = __DIR__ . '/Modules/CIMSCore/Providers';
if (is_dir($provDir)) {
    foreach (scandir($provDir) as $f) {
        if ($f === '.' || $f === '..') continue;
        echo "\n--- $f ---\n";
        echo file_get_contents($provDir . '/' . $f);
    }
} else {
    echo "Providers dir not found\n";
}

echo "\n\n=== CIMSCore module.json ===\n";
$mj = __DIR__ . '/Modules/CIMSCore/module.json';
if (file_exists($mj)) echo file_get_contents($mj);

echo "\n\n=== CIMSCore partials ===\n";
$partialsDir = __DIR__ . '/Modules/CIMSCore/Resources/views/partials';
if (is_dir($partialsDir)) {
    foreach (scandir($partialsDir) as $f) {
        if ($f === '.' || $f === '..') continue;
        echo "  $f\n";
    }
} else {
    echo "  Partials dir not found at: $partialsDir\n";
    // Check alternative locations
    $altDir = __DIR__ . '/Modules/CIMSCore/Resources/views';
    if (is_dir($altDir)) {
        echo "  Views dir contents:\n";
        $iter = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($altDir));
        foreach ($iter as $file) {
            if ($file->isFile()) echo "    " . str_replace($altDir . '/', '', $file->getPathname()) . "\n";
        }
    }
}
unlink(__FILE__);
