<?php
// Clear compiled Blade views
$viewsPath = '/usr/www/users/smartucbmh/application/storage/framework/views/';
$files = glob($viewsPath . '*.php');
$count = 0;
if ($files) {
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
            $count++;
        }
    }
}
echo "Blade cache cleared. Deleted {$count} compiled view files.";
