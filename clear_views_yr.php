<?php
$dir = '/usr/www/users/smartucbmh/application/storage/framework/views/';
$count = 0;
if (is_dir($dir)) {
    foreach (glob($dir . '*.php') as $file) { unlink($file); $count++; }
}
echo "Cleared $count compiled views.";
