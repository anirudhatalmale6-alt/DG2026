<?php
// Find the views directory
$possible = [
    __DIR__ . '/../application/storage/framework/views/',
    __DIR__ . '/application/storage/framework/views/',
    '/usr/www/users/smartucbmh/application/storage/framework/views/',
];

echo "Script dir: " . __DIR__ . "\n";

foreach ($possible as $dir) {
    echo "Checking: $dir => " . (is_dir($dir) ? "EXISTS" : "NOT FOUND") . "\n";
    if (is_dir($dir)) {
        $files = glob($dir . '*.php');
        echo "  Found " . count($files) . " files\n";
        $count = 0;
        foreach ($files as $file) {
            unlink($file);
            $count++;
        }
        echo "  Cleared $count compiled views.\n";
    }
}
