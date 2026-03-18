<?php
$viewsDir = __DIR__ . '/application/storage/framework/views/';
$count = 0;
foreach (glob($viewsDir . '*.php') as $f) { unlink($f); $count++; }
echo "Cleared {$count} compiled blade views.";
