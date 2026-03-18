<?php
$dir = __DIR__ . '/application/storage/framework/views/';
$files = glob($dir . '*.php');
$count = 0;
foreach ($files as $f) { unlink($f); $count++; }
echo "Cleared {$count} compiled views.";
