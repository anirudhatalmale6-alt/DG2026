<?php
// Clear OPcache
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "OPcache: RESET\n";
} else {
    echo "OPcache: Not available\n";
}

// Clear view cache
$basePath = realpath(__DIR__);
$viewPath = $basePath . '/application/storage/framework/views';
if (is_dir($viewPath)) {
    $files = glob($viewPath . '/*');
    $count = 0;
    foreach ($files as $file) {
        if (is_file($file) && @unlink($file)) $count++;
    }
    echo "Views: Cleared $count files\n";
}

// Clear bootstrap cache
foreach (['routes-v7.php','config.php','packages.php','services.php'] as $f) {
    $p = $basePath . '/application/bootstrap/cache/' . $f;
    if (file_exists($p) && @unlink($p)) echo "Bootstrap: Deleted $f\n";
}

echo "\nAll caches cleared. Please hard-refresh (Ctrl+Shift+R) to see changes.\n";
