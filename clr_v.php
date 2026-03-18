<?php
$d='/usr/www/users/smartucbmh/application/storage/framework/views/';$c=0;
if(is_dir($d)){foreach(glob($d.'*.php') as $f){unlink($f);$c++;}}
echo "Cleared $c views.";
