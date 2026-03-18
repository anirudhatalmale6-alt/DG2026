<?php
error_reporting(0);
define('LARAVEL_START', microtime(true));
require __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

echo "<pre>";

// Check if DomPDF is installed
echo "DomPDF class exists: " . (class_exists('Barryvdh\DomPDF\Facade\Pdf') ? 'YES' : 'NO') . "\n";
echo "DomPDF ServiceProvider: " . (class_exists('Barryvdh\DomPDF\ServiceProvider') ? 'YES' : 'NO') . "\n";

// Check config/app.php providers
echo "\n=== Registered Providers (search for DomPDF) ===\n";
$providers = config('app.providers', []);
foreach ($providers as $p) {
    if (strpos($p, 'DomPDF') !== false || strpos($p, 'dompdf') !== false || strpos($p, 'Pdf') !== false) {
        echo $p . "\n";
    }
}

// Check if Modules directory exists
echo "\n=== Modules dir ===\n";
echo "Modules path: " . base_path('Modules') . "\n";
echo "Modules dir exists: " . (is_dir(base_path('Modules')) ? 'YES' : 'NO') . "\n";

// Check config/app.php for existing module providers
echo "\n=== Module Providers ===\n";
foreach ($providers as $p) {
    if (strpos($p, 'Modules\\') !== false) {
        echo $p . "\n";
    }
}

echo "</pre>";
