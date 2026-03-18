<?php
$files = glob("/usr/www/users/smartucbmh/application/storage/framework/views/*.php");
$count = count($files);
array_map("unlink", $files);
echo "Cleared $count compiled blade views.";
