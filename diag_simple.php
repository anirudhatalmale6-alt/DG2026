<?php
header('Content-Type: text/plain');

// Read .env directly to check APP_URL
$env = file_get_contents('/usr/www/users/smartucbmh/application/.env');
preg_match('/APP_URL=(.*)/', $env, $m);
echo "APP_URL=" . trim($m[1] ?? 'not set') . "\n";

// Check if there's a public prefix
preg_match('/ASSET_URL=(.*)/', $env, $m2);
echo "ASSET_URL=" . trim($m2[1] ?? 'not set') . "\n";
