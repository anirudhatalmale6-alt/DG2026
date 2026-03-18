<?php
header('Content-Type: text/plain');

$dir = '/usr/www/users/smartucbmh/public_html/storage/client_docs/ATP100/';
echo "FILES IN WEB ROOT ATP100:\n";
foreach (scandir($dir) as $f) {
    if ($f === '.' || $f === '..') continue;
    $full = $dir . $f;
    echo "  " . $f . " | size=" . filesize($full) . " | perms=" . decoct(fileperms($full) & 0777) . " | mod=" . date('Y-m-d H:i:s', filemtime($full)) . "\n";
}

// Check the exact new file
$newFile = $dir . 'ATP100 CIPC - COR 14.3 Registration Certificate - Uploaded Wed 25 Feb 2026 @ 05-07-20.pdf';
echo "\nNEW FILE CHECK:\n";
echo "exists: " . (file_exists($newFile) ? "YES" : "NO") . "\n";
echo "readable: " . (is_readable($newFile) ? "YES" : "NO") . "\n";
if (file_exists($newFile)) {
    echo "size: " . filesize($newFile) . "\n";
    echo "perms: " . decoct(fileperms($newFile) & 0777) . "\n";
    echo "owner: " . posix_getpwuid(fileowner($newFile))['name'] . "\n";
}

// Check .htaccess in storage dir
$htaccess = '/usr/www/users/smartucbmh/public_html/storage/.htaccess';
echo "\n.htaccess in storage: " . (file_exists($htaccess) ? "EXISTS" : "MISSING") . "\n";
if (file_exists($htaccess)) {
    echo file_get_contents($htaccess) . "\n";
}

// Check .htaccess in public_html
$htaccess2 = '/usr/www/users/smartucbmh/public_html/.htaccess';
echo "\n.htaccess in public_html: " . (file_exists($htaccess2) ? "EXISTS" : "MISSING") . "\n";
if (file_exists($htaccess2)) {
    echo file_get_contents($htaccess2) . "\n";
}
