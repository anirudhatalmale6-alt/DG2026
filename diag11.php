<?php
header('Content-Type: text/plain');

// The DOCUMENT_ROOT is /usr/www/users/smartucbmh
// Apache resolves /storage/... to DOCUMENT_ROOT/storage/...
// Which is /usr/www/users/smartucbmh/storage/...

$docRoot = '/usr/www/users/smartucbmh/';

echo "DOC ROOT STORAGE:\n";
$dir = $docRoot . 'storage/client_docs/ATP100/';
echo "dir exists: " . (is_dir($dir) ? "YES" : "NO") . "\n";
if (is_dir($dir)) {
    foreach (scandir($dir) as $f) {
        if ($f === '.' || $f === '..') continue;
        echo "  $f | " . filesize($dir.$f) . "b\n";
    }
}

echo "\nPUBLIC_HTML STORAGE:\n";
$dir2 = $docRoot . 'public_html/storage/client_docs/ATP100/';
echo "dir exists: " . (is_dir($dir2) ? "YES" : "NO") . "\n";
if (is_dir($dir2)) {
    foreach (scandir($dir2) as $f) {
        if ($f === '.' || $f === '..') continue;
        echo "  $f | " . filesize($dir2.$f) . "b\n";
    }
}

// Check: the old file exists in docroot/storage but not the new one
$oldFile = $docRoot . 'storage/client_docs/ATP100/ATP100 CIPC - COR 14.3 Registration Certificate - Uploaded Mon 23 Feb 2026 @ 21-23-57.pdf';
$newFile = $docRoot . 'storage/client_docs/ATP100/ATP100 CIPC - COR 14.3 Registration Certificate - Uploaded Wed 25 Feb 2026 @ 05-07-20.pdf';
echo "\nOLD FILE in docroot/storage: " . (file_exists($oldFile) ? "EXISTS" : "MISSING") . "\n";
echo "NEW FILE in docroot/storage: " . (file_exists($newFile) ? "EXISTS" : "MISSING") . "\n";
