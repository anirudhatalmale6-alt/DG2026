<?php
// Check PHP extensions and settings that affect demo import
echo "=== PHP Extensions ===\n";
echo "curl: " . (extension_loaded('curl') ? 'YES' : 'NO') . "\n";
echo "zip: " . (extension_loaded('zip') ? 'YES' : 'NO') . "\n";
echo "xml: " . (extension_loaded('xml') ? 'YES' : 'NO') . "\n";
echo "simplexml: " . (extension_loaded('simplexml') ? 'YES' : 'NO') . "\n";
echo "dom: " . (extension_loaded('dom') ? 'YES' : 'NO') . "\n";
echo "gd: " . (extension_loaded('gd') ? 'YES' : 'NO') . "\n";
echo "mbstring: " . (extension_loaded('mbstring') ? 'YES' : 'NO') . "\n";

echo "\n=== URL Access ===\n";
echo "allow_url_fopen: " . ini_get('allow_url_fopen') . "\n";

echo "\n=== cURL Test ===\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://casethemes.net');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_NOBODY, true);
curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);
echo "casethemes.net reachable: " . ($code > 0 ? "YES (HTTP $code)" : "NO - $error") . "\n";

echo "\n=== Debug Log ===\n";
$log = __DIR__ . '/wp-content/debug.log';
if (file_exists($log)) {
    $lines = file($log);
    $last = array_slice($lines, -30);
    echo implode('', $last);
} else {
    echo "No debug.log found\n";
}

// Clean up self
unlink(__FILE__);
