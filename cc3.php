<?php
$basePath = dirname(__FILE__);
$cacheDir = $basePath . '/bootstrap/cache';
if (is_dir($cacheDir)) {
    foreach (glob($cacheDir . '/*') as $file) {
        if (is_file($file)) @unlink($file);
    }
}
$viewDir = $basePath . '/storage/framework/views';
if (is_dir($viewDir)) {
    foreach (glob($viewDir . '/*.php') as $file) @unlink($file);
}
echo "Cache cleared";
