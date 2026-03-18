<?php
// Try multiple possible paths
$paths = [
    __DIR__ . '/../application/storage/framework/views/',
    __DIR__ . '/application/storage/framework/views/',
    '/usr/www/users/smartucbmh/public_html/application/storage/framework/views/',
];

foreach ($paths as $dir) {
    if (is_dir($dir)) {
        $count = 0;
        foreach (glob($dir . '*.php') as $file) {
            unlink($file);
            $count++;
        }
        echo "Path: $dir - Cleared $count compiled views.\n";
    } else {
        echo "Path: $dir - NOT FOUND\n";
    }
}
echo "Done.";
