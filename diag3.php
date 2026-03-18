<?php
header('Content-Type: text/plain');

// Check filesystem config
$configFile = '/usr/www/users/smartucbmh/public_html/application/config/filesystems.php';
if (file_exists($configFile)) {
    $config = include($configFile);
    echo "PUBLIC DISK CONFIG:\n";
    print_r($config['disks']['public'] ?? 'NOT FOUND');
    echo "\n\n";
}

// Check .env for FILESYSTEM_DISK
$envFile = '/usr/www/users/smartucbmh/public_html/application/.env';
if (file_exists($envFile)) {
    $env = file_get_contents($envFile);
    preg_match('/FILESYSTEM_DISK=(.*)/', $env, $m);
    echo "FILESYSTEM_DISK=" . ($m[1] ?? 'not set') . "\n";
    preg_match('/FILESYSTEM_DRIVER=(.*)/', $env, $m);
    echo "FILESYSTEM_DRIVER=" . ($m[1] ?? 'not set') . "\n\n";
}

// Check if file is in a different location
echo "SEARCHING FOR BANKING FILE:\n";
$searchDirs = [
    '/usr/www/users/smartucbmh/public_html/application/storage/',
    '/usr/www/users/smartucbmh/public_html/storage/',
];
$target = 'ATP100 BANKING';
foreach ($searchDirs as $dir) {
    if (!is_dir($dir)) { echo "$dir: DIR NOT FOUND\n"; continue; }
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS));
    foreach ($it as $file) {
        if (strpos($file->getFilename(), $target) !== false) {
            echo "FOUND: " . $file->getPathname() . " (" . $file->getSize() . " bytes)\n";
        }
    }
}

// List files in ATP100 directory under web root storage
echo "\nFILES IN /public_html/storage/client_docs/ATP100/:\n";
$atp = '/usr/www/users/smartucbmh/public_html/storage/client_docs/ATP100/';
if (is_dir($atp)) {
    foreach (scandir($atp) as $f) {
        if ($f === '.' || $f === '..') continue;
        echo "  $f\n";
    }
}
