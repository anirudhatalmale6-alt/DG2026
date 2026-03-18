<?php
error_reporting(0);
define('LARAVEL_START', microtime(true));
require __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

echo "<pre>";

// Check for nwidart/laravel-modules
echo "nwidart modules: " . (class_exists('Nwidart\Modules\LaravelModulesServiceProvider') ? 'YES' : 'NO') . "\n";

// Check modules.php config
$modules = config('modules');
if ($modules) {
    echo "\nmodules config exists: YES\n";
    echo "Scan paths: " . json_encode($modules['scan']['paths'] ?? 'N/A') . "\n";
    echo "Enabled: " . json_encode($modules['scan']['enabled'] ?? 'N/A') . "\n";
} else {
    echo "\nmodules config: NO\n";
}

// Check modules_statuses.json
$statusFile = base_path('modules_statuses.json');
echo "\nmodules_statuses.json exists: " . (file_exists($statusFile) ? 'YES' : 'NO') . "\n";
if (file_exists($statusFile)) {
    echo file_get_contents($statusFile) . "\n";
}

// List all providers that are loaded
$allProviders = $app->getLoadedProviders();
foreach ($allProviders as $p => $loaded) {
    if (strpos($p, 'Module') !== false || strpos($p, 'CIMS') !== false || strpos($p, 'Cims') !== false) {
        echo "Loaded: $p\n";
    }
}

echo "</pre>";
