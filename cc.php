<?php
$viewCacheDir = dirname(__FILE__) . '/application/storage/framework/views';
$count = 0;
if (is_dir($viewCacheDir)) {
    foreach (glob($viewCacheDir . '/*.php') as $file) {
        unlink($file);
        $count++;
    }
}
echo "Deleted $count cached views.\nDONE";
