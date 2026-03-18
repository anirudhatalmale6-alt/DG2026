<?php
header('Content-Type: application/json');

// Clear compiled views
$viewsPath = __DIR__ . '/application/storage/framework/views/';
$files = glob($viewsPath . '*.php');
$deleted = 0;
foreach ($files as $file) {
    unlink($file);
    $deleted++;
}

// Also clear route cache if exists
$routeCache = __DIR__ . '/application/bootstrap/cache/routes-v7.php';
if (file_exists($routeCache)) {
    unlink($routeCache);
    $deleted++;
}

echo json_encode([
    'views_cleared' => $deleted,
    'message' => 'Cache cleared successfully'
]);
