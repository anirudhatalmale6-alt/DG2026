<?php
$cachePath = __DIR__ . '/application/bootstrap/cache/';
$viewPath = __DIR__ . '/application/storage/framework/views/';
$cleared = 0;
foreach ([$cachePath, $viewPath] as $dir) {
    if (is_dir($dir)) {
        foreach (glob($dir . '*') as $file) {
            if (is_file($file)) { unlink($file); $cleared++; }
        }
    }
}
echo "Cleared $cleared cached files";
