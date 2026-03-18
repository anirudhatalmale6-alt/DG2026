<?php
$file = __DIR__ . '/Modules/CIMSDocManager/Resources/views/documents/form.blade.php';
$lines = explode("\n", file_get_contents($file));
echo "Lines 52-67 of form.blade.php:\n";
for ($i = 52; $i <= 67 && $i < count($lines); $i++) {
    echo "  " . ($i+1) . ": " . $lines[$i] . "\n";
}

// Also check if there are ANY Blade cached views remaining
$viewDir = __DIR__ . '/storage/framework/views';
$remaining = is_dir($viewDir) ? count(glob($viewDir . '/*.php')) : 0;
echo "\nCached views remaining: $remaining\n";
unlink(__FILE__);
