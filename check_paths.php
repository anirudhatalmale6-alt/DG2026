<?php
$docRoot = $_SERVER['DOCUMENT_ROOT'] ?? '/usr/www/users/smartucbmh/public_html';
echo "DOCUMENT_ROOT: " . $docRoot . "\n";
echo "Photo URL path: /storage/contact_photos/\n";
echo "Photo storage at: " . $docRoot . "/storage/contact_photos/\n";
echo "Storage dir exists: " . (is_dir($docRoot . '/storage/contact_photos') ? 'YES' : 'NO') . "\n";
echo "public_html/storage exists: " . (is_dir($docRoot . '/storage') ? 'YES' : 'NO') . "\n";

// Also check the existing document storage path pattern
echo "client_master_docs exists: " . (is_dir($docRoot . '/storage/client_master_docs') ? 'YES' : 'NO') . "\n";
