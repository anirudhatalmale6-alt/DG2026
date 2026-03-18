<?php
$dir = '/usr/www/users/smartucbmh/application/storage/framework/views/';

// List all files
$all = scandir($dir);
echo "Files in views dir: " . count($all) . "\n";
foreach ($all as $f) {
    if ($f === '.' || $f === '..') continue;
    $full = $dir . $f;
    echo "  $f (is_file=" . (is_file($full) ? 'Y' : 'N') . ", is_dir=" . (is_dir($full) ? 'Y' : 'N') . ")\n";
    if (is_file($full) && pathinfo($f, PATHINFO_EXTENSION) === 'php') {
        unlink($full);
        echo "    DELETED\n";
    }
}
echo "\nDone.\n";
