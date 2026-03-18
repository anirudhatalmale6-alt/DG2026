<?php
$paths = [
    __DIR__ . '/application/storage/framework/views/',
    '/usr/www/users/smartucbmh/application/storage/framework/views/',
];
foreach ($paths as $dir) {
    if (is_dir($dir)) {
        $count = 0;
        foreach (glob($dir . '*.php') as $file) { unlink($file); $count++; }
        echo "Cleared $count views from $dir\n";
    }
}
echo "Done.";
