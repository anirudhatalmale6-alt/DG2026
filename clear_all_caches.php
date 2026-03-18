<?php
require __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "<pre>\n";
echo "=== CLEARING ALL CACHES ===\n\n";

// 1. Clear compiled views
$viewPath = storage_path('framework/views');
$files = glob($viewPath . '/*.php');
$count = count($files);
foreach ($files as $file) {
    unlink($file);
}
echo "Deleted {$count} compiled view files\n";

// 2. Clear route cache
$routeCache = base_path('bootstrap/cache/routes-v7.php');
if (file_exists($routeCache)) {
    unlink($routeCache);
    echo "Deleted route cache\n";
} else {
    echo "No route cache file\n";
}

// 3. Clear config cache
$configCache = base_path('bootstrap/cache/config.php');
if (file_exists($configCache)) {
    unlink($configCache);
    echo "Deleted config cache\n";
} else {
    echo "No config cache file\n";
}

// 4. Check if payroll routes exist
$router = app('router');
$routes = $router->getRoutes();
$payrollRoutes = [];
foreach ($routes as $route) {
    $name = $route->getName();
    if ($name && str_contains($name, 'cimspayroll.')) {
        $payrollRoutes[] = $name . ' => ' . $route->uri();
    }
}
echo "\nPayroll routes registered: " . count($payrollRoutes) . "\n";
foreach ($payrollRoutes as $r) {
    echo "  " . $r . "\n";
}

// 5. Test route generation
echo "\n=== TEST ROUTE GENERATION ===\n";
try {
    echo "pay-runs.index => " . route('cimspayroll.pay-runs.index') . "\n";
    echo "loans.index => " . route('cimspayroll.loans.index') . "\n";
    echo "payslips.index => " . route('cimspayroll.payslips.index') . "\n";
    echo "reports.payroll-summary => " . route('cimspayroll.reports.payroll-summary') . "\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== DONE ===\n";
echo "</pre>\n";
