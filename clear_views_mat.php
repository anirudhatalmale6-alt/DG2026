<?php
$dir = __DIR__ . '/application/storage/framework/views/';
$count = 0;
if (is_dir($dir)) {
    foreach (glob($dir . '*.php') as $file) {
        unlink($file);
        $count++;
    }
}
echo "Cleared $count compiled views.";
