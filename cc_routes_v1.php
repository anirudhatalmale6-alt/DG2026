<?php
// Clear route cache
$appPath = '/usr/www/users/smartucbmh/application/';
$cacheFiles = glob($appPath . 'bootstrap/cache/routes*.php');
$count = 0;
foreach ($cacheFiles as $f) { unlink($f); $count++; }
echo "Cleared $count route cache files.\n";

// Also clear module caches
$modCaches = glob($appPath . 'bootstrap/cache/*_email*.php');
foreach ($modCaches as $f) { unlink($f); $count++; }
echo "Total cleared: $count cache files.\n";
