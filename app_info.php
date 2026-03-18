<?php
// Get main app info
$info = [];

// Laravel version
$composerJson = __DIR__ . '/composer.json';
if (file_exists($composerJson)) {
    $composer = json_decode(file_get_contents($composerJson), true);
    $info['laravel_version'] = $composer['require']['laravel/framework'] ?? 'unknown';
    $info['php_require'] = $composer['require']['php'] ?? 'unknown';
    $info['app_name_composer'] = $composer['name'] ?? 'unknown';
}

// Check .env for APP_NAME
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $env = file_get_contents($envFile);
    preg_match('/APP_NAME=(.*)/', $env, $m);
    $info['app_name'] = trim($m[1] ?? 'unknown', '"\'');
    preg_match('/APP_URL=(.*)/', $env, $m);
    $info['app_url'] = trim($m[1] ?? 'unknown', '"\'');
    preg_match('/DB_DATABASE=(.*)/', $env, $m);
    $info['db_database'] = trim($m[1] ?? 'unknown', '"\'');
}

// Count total modules
$modulesDir = __DIR__ . '/Modules';
if (is_dir($modulesDir)) {
    $mods = array_diff(scandir($modulesDir), ['.', '..']);
    $info['total_modules'] = count($mods);
    $info['module_names'] = array_values($mods);
}

// List main routes file if available
$routesFile = __DIR__ . '/routes/web.php';
if (file_exists($routesFile)) {
    $info['main_routes'] = file_get_contents($routesFile);
}

// Check modules_statuses if it exists
$statusFile = __DIR__ . '/../modules_statuses.json';
if (file_exists($statusFile)) {
    $info['modules_statuses'] = json_decode(file_get_contents($statusFile), true);
}

header('Content-Type: application/json');
echo json_encode($info, JSON_PRETTY_PRINT);
