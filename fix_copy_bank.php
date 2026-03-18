<?php
header('Content-Type: text/plain');

// Copy existing bank documents that are missing from root storage
$src_base = '/usr/www/users/smartucbmh/application/storage/app/public/';
$dst_base = '/usr/www/users/smartucbmh/storage/';

$files = [
    'client_docs/ATP100/ATP100 BANKING - Confirmation of Banking - Uploaded Tue 24 Feb 2026 @ 08-21-52.pdf',
    'client_docs/ATP100/ATP100 BANKING - Confirmation of Banking - Uploaded Wed 25 Feb 2026 @ 04-54-58.pdf',
];

foreach ($files as $fp) {
    $src = $src_base . $fp;
    $dst = $dst_base . $fp;
    $dstDir = dirname($dst);

    if (!file_exists($src)) { echo "SOURCE MISSING: $fp\n"; continue; }
    if (file_exists($dst)) { echo "ALREADY EXISTS: $fp\n"; continue; }

    if (!is_dir($dstDir)) mkdir($dstDir, 0755, true);

    if (copy($src, $dst)) {
        echo "COPIED: $fp (" . filesize($dst) . "b)\n";
    } else {
        echo "FAILED: $fp\n";
    }
}
