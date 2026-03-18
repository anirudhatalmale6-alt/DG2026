<?php
$versionFile = '/usr/www/users/smartucbmh/application/storage/cache_version.txt';
file_put_contents($versionFile, time());
echo "Cache version file created: " . file_get_contents($versionFile) . "\n";
echo "Path: $versionFile\n";
echo "Exists: " . (file_exists($versionFile) ? 'YES' : 'NO') . "\n";
