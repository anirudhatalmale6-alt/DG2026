<?php
$count = 0;

// Clear compiled Blade views
$dirs = [
    __DIR__ . '/storage/framework/views',
    __DIR__ . '/../storage/framework/views',
];
foreach ($dirs as $dir) {
    if (is_dir($dir)) {
        $files = glob($dir . '/*');
        foreach ($files as $f) {
            if (is_file($f) && basename($f) !== '.gitignore') {
                @unlink($f);
                $count++;
            }
        }
    }
}

// Clear OPcache
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "OPcache reset. ";
} else {
    echo "OPcache not available. ";
}

// Clear Laravel cache files
$cacheDirs = [
    __DIR__ . '/storage/framework/cache/data',
    __DIR__ . '/bootstrap/cache',
];
foreach ($cacheDirs as $dir) {
    if (is_dir($dir)) {
        $iter = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($iter as $f) {
            if ($f->isFile() && $f->getFilename() !== '.gitignore') {
                @unlink($f->getPathname());
                $count++;
            }
        }
    }
}

echo "Cleared $count files total.";
unlink(__FILE__);
