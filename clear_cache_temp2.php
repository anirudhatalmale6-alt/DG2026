<?php
header('Content-Type: application/json');
$viewsPath = __DIR__ . '/application/storage/framework/views/';
$files = glob($viewsPath . '*.php');
$deleted = 0;
foreach ($files as $file) { unlink($file); $deleted++; }
echo json_encode(['views_cleared' => $deleted]);
