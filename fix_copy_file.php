<?php
header('Content-Type: text/plain');

// Copy the missing ATP100 banking file to web-accessible location
$src = '/usr/www/users/smartucbmh/application/storage/app/public/client_docs/ATP100/ATP100 BANKING - Confirmation of Banking - Uploaded Tue 24 Feb 2026 @ 08-21-52.pdf';
$destDir = '/usr/www/users/smartucbmh/public_html/storage/client_docs/ATP100/';
$dest = $destDir . 'ATP100 BANKING - Confirmation of Banking - Uploaded Tue 24 Feb 2026 @ 08-21-52.pdf';

if (!file_exists($src)) {
    echo "SOURCE NOT FOUND: $src\n";
    exit;
}

if (!is_dir($destDir)) {
    mkdir($destDir, 0755, true);
    echo "Created dir: $destDir\n";
}

if (copy($src, $dest)) {
    echo "SUCCESS: File copied\n";
    echo "Size: " . filesize($dest) . " bytes\n";
} else {
    echo "FAILED to copy\n";
}

// Verify
echo "Source exists: " . (file_exists($src) ? "YES" : "NO") . "\n";
echo "Dest exists: " . (file_exists($dest) ? "YES" : "NO") . "\n";
