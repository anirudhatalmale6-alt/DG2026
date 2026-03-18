<?php
$viewPath = __DIR__ . '/application/storage/framework/views';
$files = glob($viewPath . '/*.php');
$count = count($files);
foreach ($files as $file) { unlink($file); }
echo "Deleted {$count} compiled views. Cache cleared.";
