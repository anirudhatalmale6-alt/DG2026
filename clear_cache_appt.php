<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<pre>\n";
echo "=== Clear Caches & Cleanup ===\n\n";

$appBase = __DIR__ . '/application';

// 1. Clear compiled views
echo "--- Compiled Views ---\n";
$viewFiles = glob($appBase . '/storage/framework/views/*.php');
$viewCount = count($viewFiles);
foreach ($viewFiles as $file) {
    unlink($file);
}
echo "Cleared $viewCount compiled views\n\n";

// 2. Clear bootstrap cache
echo "--- Bootstrap Cache ---\n";
$cacheFiles = ['packages.php', 'services.php', 'config.php', 'routes-v7.php', 'routes.php'];
foreach ($cacheFiles as $cf) {
    $path = $appBase . '/bootstrap/cache/' . $cf;
    if (file_exists($path)) {
        unlink($path);
        echo "Deleted: $cf\n";
    }
}
// Also check for module-specific cache files
$moduleCaches = glob($appBase . '/bootstrap/cache/*appointment*');
foreach ($moduleCaches as $mc) {
    unlink($mc);
    echo "Deleted: " . basename($mc) . "\n";
}
echo "\n";

// 3. Delete deploy scripts
echo "--- Cleanup Deploy Scripts ---\n";
$scripts = ['deploy_appointments.php', 'deploy_appointments2.php', 'deploy_appointments3.php', 'deploy_sql.php', 'clear_cache_appt.php'];
foreach ($scripts as $script) {
    $path = __DIR__ . '/' . $script;
    if (file_exists($path) && $script !== 'clear_cache_appt.php') {
        unlink($path);
        echo "Deleted: $script\n";
    }
}
echo "\n";

echo "=== Done - Now deleting this script ===\n";
echo "</pre>";

// Self-delete
unlink(__FILE__);
