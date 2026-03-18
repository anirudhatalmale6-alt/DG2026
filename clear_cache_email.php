<?php
// Clear compiled blade views
$viewsDir = '/usr/www/users/smartucbmh/application/storage/framework/views/';
$count = 0;
foreach (glob($viewsDir . '*.php') as $f) {
    unlink($f);
    $count++;
}
echo "Cleared {$count} compiled views.\n";

// Clear route cache if exists
$routeCache = '/usr/www/users/smartucbmh/application/bootstrap/cache/routes-v7.php';
if (file_exists($routeCache)) {
    unlink($routeCache);
    echo "Route cache cleared.\n";
} else {
    echo "No route cache file.\n";
}

echo "Done.\n";
