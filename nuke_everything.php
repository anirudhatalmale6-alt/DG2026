<?php
require __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "<pre>\n";
echo "=== NUCLEAR CACHE CLEAR ===\n\n";

// 1. Delete ALL compiled views
$viewPath = storage_path('framework/views');
$files = glob($viewPath . '/*.php');
$count = count($files);
foreach ($files as $file) {
    unlink($file);
}
echo "1. Deleted {$count} compiled view files\n";

// 2. Clear route cache
$routeCache = base_path('bootstrap/cache/routes-v7.php');
if (file_exists($routeCache)) {
    unlink($routeCache);
    echo "2. Deleted route cache\n";
} else {
    echo "2. No route cache file\n";
}

// 3. Clear config cache
$configCache = base_path('bootstrap/cache/config.php');
if (file_exists($configCache)) {
    unlink($configCache);
    echo "3. Deleted config cache\n";
} else {
    echo "3. No config cache file\n";
}

// 4. Clear services cache
$servicesCache = base_path('bootstrap/cache/services.php');
if (file_exists($servicesCache)) {
    unlink($servicesCache);
    echo "4. Deleted services cache\n";
} else {
    echo "4. No services cache file\n";
}

// 5. Clear packages cache
$packagesCache = base_path('bootstrap/cache/packages.php');
if (file_exists($packagesCache)) {
    unlink($packagesCache);
    echo "5. Deleted packages cache\n";
} else {
    echo "5. No packages cache file\n";
}

// 6. Flush OPcache if available
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "6. OPcache flushed!\n";
} else {
    echo "6. OPcache not available\n";
}

// 7. Verify the menu source file
$menuPath = base_path('Modules/CIMSCore/Resources/views/partials/cims_master_menu.blade.php');
echo "\n=== MENU SOURCE FILE CHECK ===\n";
echo "Path: {$menuPath}\n";
echo "Exists: " . (file_exists($menuPath) ? 'YES' : 'NO') . "\n";
if (file_exists($menuPath)) {
    $content = file_get_contents($menuPath);
    echo "Size: " . strlen($content) . " bytes\n";
    echo "Modified: " . date('Y-m-d H:i:s', filemtime($menuPath)) . "\n";

    // Check for route() calls for payroll
    $checks = [
        "route('cimspayroll.pay-runs.index')" => "Pay Runs route()",
        "route('cimspayroll.loans.index')" => "Loans route()",
        "route('cimspayroll.payslips.index')" => "Payslips route()",
        "route('cimspayroll.reports.payroll-summary')" => "Reports route()",
    ];
    echo "\nMenu link checks:\n";
    foreach ($checks as $search => $label) {
        echo "  {$label}: " . (str_contains($content, $search) ? 'FOUND' : 'MISSING') . "\n";
    }

    // Check for any remaining href="#"
    preg_match_all('/href="#">([^<]+)</', $content, $matches);
    if (!empty($matches[1])) {
        echo "\nItems still using href=\"#\":\n";
        foreach ($matches[1] as $item) {
            echo "  - {$item}\n";
        }
    } else {
        echo "\nNo payroll items using href=\"#\" - all good!\n";
    }
}

// 8. Verify routes exist
echo "\n=== ROUTE VERIFICATION ===\n";
$routeNames = [
    'cimspayroll.pay-runs.index',
    'cimspayroll.loans.index',
    'cimspayroll.payslips.index',
    'cimspayroll.reports.payroll-summary',
    'cimspayroll.reports.paye',
    'cimspayroll.reports.uif',
    'cimspayroll.reports.leave',
    'cimspayroll.reports.loans',
    'cimspayroll.reports.cost-to-company',
];
foreach ($routeNames as $name) {
    try {
        $url = route($name);
        echo "  {$name} => {$url}\n";
    } catch (\Exception $e) {
        echo "  {$name} => ERROR: " . $e->getMessage() . "\n";
    }
}

echo "\n=== DONE ===\n";
echo "</pre>\n";
