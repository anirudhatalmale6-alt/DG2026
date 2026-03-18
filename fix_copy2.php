<?php
header('Content-Type: text/plain');

$src = '/usr/www/users/smartucbmh/application/storage/app/public/client_docs/ATP100/ATP100 BANKING - Confirmation of Banking - Uploaded Wed 25 Feb 2026 @ 04-54-58.pdf';
$destDir = '/usr/www/users/smartucbmh/public_html/storage/client_docs/ATP100/';
$dest = $destDir . 'ATP100 BANKING - Confirmation of Banking - Uploaded Wed 25 Feb 2026 @ 04-54-58.pdf';

if (!file_exists($src)) {
    echo "SOURCE NOT FOUND\n";
    exit;
}

if (!is_dir($destDir)) {
    mkdir($destDir, 0755, true);
}

if (copy($src, $dest)) {
    echo "SUCCESS: copied " . filesize($dest) . " bytes\n";
} else {
    echo "FAILED\n";
}
