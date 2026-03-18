<?php
require __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "<pre>\n";
echo "=== PHASE 3 ROUTE CHECK ===\n\n";

$routes = app('router')->getRoutes();
$found = 0;
foreach ($routes as $route) {
    $name = $route->getName();
    if ($name && (str_contains($name, 'pay-runs') || str_contains($name, 'loans'))) {
        echo sprintf("%-50s %-8s %s\n", $name, implode('|', $route->methods()), $route->uri());
        $found++;
    }
}

echo "\nFound {$found} routes\n";

// Check controller methods
echo "\n=== CONTROLLER METHODS ===\n";
$controller = 'Modules\CIMS_PAYROLL\Http\Controllers\PayrollController';
if (class_exists($controller)) {
    echo "Controller class exists: YES\n";
    $methods = ['payRuns', 'payRunCreate', 'payRunStore', 'payRunShow', 'payRunProcess', 'loans', 'loanCreate'];
    foreach ($methods as $m) {
        echo "  {$m}: " . (method_exists($controller, $m) ? 'YES' : 'NO') . "\n";
    }
} else {
    echo "Controller class exists: NO\n";
}

echo "</pre>\n";
