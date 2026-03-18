<?php
$d = __DIR__ . '/application/storage/framework/views/';
$c = 0; foreach (glob($d . '*.php') as $f) { unlink($f); $c++; }
echo "Cleared {$c} views.";
