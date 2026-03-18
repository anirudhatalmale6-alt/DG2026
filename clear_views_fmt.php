<?php
$dir = '/usr/www/users/smartucbmh/application/storage/framework/views/';
$count = 0;
if (is_dir($dir)) { foreach (glob($dir . '*.php') as $f) { unlink($f); $count++; } }
echo "Cleared $count views.";
