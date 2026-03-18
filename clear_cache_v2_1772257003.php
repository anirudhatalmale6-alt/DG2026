<?php
$paths = [
    '/usr/www/users/smartucbmh/application/storage/framework/views/',
    '/usr/www/users/smartucbmh/storage/framework/views/',
    '/usr/www/users/smartucbmh/public_html/application/storage/framework/views/',
];
$total = 0;
foreach ($paths as $p) {
    if (is_dir($p)) {
        $files = glob($p . '*.php');
        echo "Found " . count($files) . " files in $p\n";
        foreach ($files as $f) { if (unlink($f)) $total++; }
    } else {
        echo "Not a dir: $p\n";
    }
}
echo "Cleared $total compiled views total.";
