<?php
$basePath = realpath(__DIR__);
$appPath = $basePath . '/application';
// Clear blade compiled views from application directory
$viewPath = $appPath . '/storage/framework/views';
if (is_dir($viewPath)) {
    $files = glob($viewPath . '/*');
    foreach ($files as $file) {
        if (is_file($file)) unlink($file);
    }
}
// Also check root level (legacy)
$viewPath2 = $basePath . '/storage/framework/views';
if (is_dir($viewPath2)) {
    $files = glob($viewPath2 . '/*');
    foreach ($files as $file) {
        if (is_file($file)) unlink($file);
    }
}
// Clear bootstrap cache
foreach (['routes-v7.php','config.php','packages.php','services.php'] as $f) {
    $p = $appPath . '/bootstrap/cache/' . $f;
    if (file_exists($p)) unlink($p);
    $p2 = $basePath . '/bootstrap/cache/' . $f;
    if (file_exists($p2)) unlink($p2);
}
echo "All caches cleared successfully.";
