<?php
$count = 0;
foreach (glob('/usr/www/users/smartucbmh/application/storage/framework/views/*.php') as $f) {
    unlink($f);
    $count++;
}
echo "Cleared {$count} compiled views. Done.\n";
