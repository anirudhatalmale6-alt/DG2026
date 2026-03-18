<?php
/**
 * Simple cache clearer — deletes cached files directly without bootstrapping Laravel.
 * Access: https://smartweigh.co.za/clear_cache_payroll.php
 * DELETE AFTER USE.
 */

echo "<pre>\n";
echo "=== Clearing Caches for CIMS_PAYROLL ===\n\n";

$basePath = __DIR__ . '/application';

// Clear bootstrap cache files
$bootstrapCache = $basePath . '/bootstrap/cache';
$files = ['config.php', 'routes-v7.php', 'services.php', 'packages.php'];
foreach ($files as $f) {
    $path = $bootstrapCache . '/' . $f;
    if (file_exists($path)) {
        unlink($path);
        echo "[OK] Deleted: bootstrap/cache/{$f}\n";
    } else {
        echo "[SKIP] Not found: bootstrap/cache/{$f}\n";
    }
}

// Clear compiled views
$viewsPath = $basePath . '/storage/framework/views';
if (is_dir($viewsPath)) {
    $count = 0;
    foreach (glob($viewsPath . '/*.php') as $file) {
        unlink($file);
        $count++;
    }
    echo "[OK] Cleared {$count} compiled views\n";
} else {
    echo "[SKIP] Views directory not found\n";
}

// Clear cache store
$cachePath = $basePath . '/storage/framework/cache/data';
if (is_dir($cachePath)) {
    $ri = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($cachePath, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    $count = 0;
    foreach ($ri as $file) {
        if ($file->isFile() && $file->getFilename() !== '.gitignore') {
            unlink($file->getPathname());
            $count++;
        }
    }
    echo "[OK] Cleared {$count} cache files\n";
} else {
    echo "[SKIP] Cache directory not found\n";
}

// OPcache
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "[OK] OPcache reset\n";
}

echo "\n[DONE] All caches cleared.\n";
echo "[NEXT] Now run: https://smartweigh.co.za/migrate_payroll.php\n";
echo "\n*** DELETE THIS FILE AFTER USE ***\n";
echo "</pre>";
