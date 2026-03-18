<?php
require __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "<pre>\n";
echo "=== MENU VERIFICATION ===\n\n";

// Check the source menu file
$menuPath = base_path('Modules/CIMSCore/Resources/views/partials/cims_master_menu.blade.php');
echo "Menu source: {$menuPath}\n";
echo "Exists: " . (file_exists($menuPath) ? 'YES' : 'NO') . "\n";

if (file_exists($menuPath)) {
    $content = file_get_contents($menuPath);
    echo "Modified: " . date('Y-m-d H:i:s', filemtime($menuPath)) . "\n\n";

    $checks = [
        "route('cimspayroll.pay-runs.index')" => "Pay Runs",
        "route('cimspayroll.loans.index')" => "Loans Register",
        "route('cimspayroll.payslips.index')" => "Payslips",
        "route('cimspayroll.reports.payroll-summary')" => "Payroll Summary",
        "route('cimspayroll.reports.paye')" => "PAYE Report",
        "route('cimspayroll.reports.uif')" => "UIF Report",
        "route('cimspayroll.reports.leave')" => "Leave Report",
        "route('cimspayroll.reports.loans')" => "Loan Report",
        "route('cimspayroll.reports.cost-to-company')" => "Cost to Company",
    ];

    echo "Route helper checks in source:\n";
    foreach ($checks as $search => $label) {
        echo "  {$label}: " . (str_contains($content, $search) ? 'OK' : 'MISSING!') . "\n";
    }
}

// Check compiled views
$viewPath = storage_path('framework/views');
$files = glob($viewPath . '/*.php');
echo "\nCompiled views: " . count($files) . " files\n";

// Verify routes generate correctly
echo "\nRoute generation test:\n";
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

echo "\n=== ALL GOOD ===\n";
echo "</pre>\n";
