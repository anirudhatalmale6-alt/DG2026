<?php
// Clear compiled views
$viewsPath = '/usr/www/users/smartucbmh/application/storage/framework/views/';
$count = 0;
if (is_dir($viewsPath)) {
    foreach (glob($viewsPath . '*.php') as $f) { if (unlink($f)) $count++; }
}
echo "Cleared $count compiled views.\n";

// Clear route cache if exists
$routeCache = '/usr/www/users/smartucbmh/application/bootstrap/cache/routes-v7.php';
if (file_exists($routeCache)) { unlink($routeCache); echo "Route cache cleared.\n"; } else { echo "No route cache file.\n"; }

// Clear services cache
$servicesCache = '/usr/www/users/smartucbmh/application/bootstrap/cache/services.php';
if (file_exists($servicesCache)) { unlink($servicesCache); echo "Services cache cleared.\n"; } else { echo "No services cache.\n"; }
