<?php
header('Content-Type: text/plain');

// Search for .htaccess files
echo "HTACCESS FILES:\n";
exec("find /usr/www/users/smartucbmh/ -maxdepth 3 -name '.htaccess' 2>/dev/null", $output);
foreach ($output as $line) {
    echo "  $line\n";
    echo "  ---\n";
    echo file_get_contents($line);
    echo "\n  ===\n\n";
}

// Check if the issue is filename encoding
echo "\nTEST: can PHP read both files?\n";
$old = '/usr/www/users/smartucbmh/public_html/storage/client_docs/ATP100/ATP100 CIPC - COR 14.3 Registration Certificate - Uploaded Mon 23 Feb 2026 @ 21-23-57.pdf';
$new = '/usr/www/users/smartucbmh/public_html/storage/client_docs/ATP100/ATP100 CIPC - COR 14.3 Registration Certificate - Uploaded Wed 25 Feb 2026 @ 05-07-20.pdf';
echo "old: " . (file_exists($old) ? filesize($old) . "b" : "MISSING") . "\n";
echo "new: " . (file_exists($new) ? filesize($new) . "b" : "MISSING") . "\n";

// Check for any apache config
echo "\nAPACHE VHOST/CONFIG:\n";
exec("find /etc/apache2/ -name '*.conf' 2>/dev/null | head -20", $output2);
foreach ($output2 as $line) echo "  $line\n";
