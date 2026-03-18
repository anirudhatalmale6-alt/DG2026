<?php
// Check what the route prefix is for cims_pm_pro module
echo "=== Checking route prefix ===\n";

// Read the module's route service provider or module.json
$moduleJson = '/usr/www/users/smartucbmh/public_html/application/Modules/cims_pm_pro/module.json';
if (file_exists($moduleJson)) {
    echo "module.json:\n" . file_get_contents($moduleJson) . "\n\n";
}

// Check RouteServiceProvider
$rsp = '/usr/www/users/smartucbmh/public_html/application/Modules/cims_pm_pro/Providers/RouteServiceProvider.php';
if (file_exists($rsp)) {
    echo "RouteServiceProvider.php:\n" . file_get_contents($rsp) . "\n\n";
}

// Check .env APP_URL
$env = file_get_contents('/usr/www/users/smartucbmh/public_html/application/.env');
preg_match('/^APP_URL=(.*)$/m', $env, $matches);
echo "APP_URL: " . ($matches[1] ?? 'not found') . "\n";

// Parse the path
$url = trim($matches[1] ?? '');
$path = parse_url($url, PHP_URL_PATH);
echo "parse_url path: " . ($path ?? 'null') . "\n";
echo "rtrim result: " . rtrim($path ?? '', '/') . "\n";
?>
