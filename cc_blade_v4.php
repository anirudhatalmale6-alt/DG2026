<?php
$dir = '/usr/www/users/smartucbmh/application/storage/framework/views/';
$count = 0;
foreach (glob($dir . '*.php') as $file) { unlink($file); $count++; }
echo "Cleared {$count} blade views. DONE\n";
