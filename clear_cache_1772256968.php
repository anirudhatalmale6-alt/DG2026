<?php
$viewsPath = '/usr/www/users/smartucbmh/application/storage/framework/views/';
$files = glob($viewsPath . '*.php');
$count = 0;
foreach ($files as $file) {
    if (unlink($file)) $count++;
}
echo "Cleared $count compiled views.";
