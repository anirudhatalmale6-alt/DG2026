<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

// Login as first user
$user = \App\Models\User::first();
\Illuminate\Support\Facades\Auth::login($user);

// Check if controller works
try {
    $taxYears = \Illuminate\Support\Facades\DB::table('cims_document_periods')
        ->where('is_active', 1)
        ->whereNotNull('tax_year')
        ->select('tax_year')
        ->distinct()
        ->orderBy('tax_year', 'desc')
        ->pluck('tax_year');

    echo "<h3>Tax Years:</h3><pre>";
    print_r($taxYears->toArray());
    echo "</pre>";

    // Test API periods for year 2026
    $periods = \Illuminate\Support\Facades\DB::table('cims_document_periods')
        ->where('is_active', 1)
        ->where('tax_year', '2026')
        ->whereRaw("period_name NOT LIKE '%Tax Year%'")
        ->orderBy('display_order', 'asc')
        ->get();

    echo "<h3>Periods for 2026 (excluding Tax Year):</h3><pre>";
    foreach ($periods as $p) {
        echo $p->id . " | " . $p->period_name . " | combo: " . $p->period_combo . " | order: " . $p->display_order . "\n";
    }
    echo "</pre>";

    // Test for 2025
    $periods25 = \Illuminate\Support\Facades\DB::table('cims_document_periods')
        ->where('is_active', 1)
        ->where('tax_year', '2025')
        ->whereRaw("period_name NOT LIKE '%Tax Year%'")
        ->orderBy('display_order', 'asc')
        ->get();

    echo "<h3>Periods for 2025 (excluding Tax Year):</h3><pre>";
    foreach ($periods25 as $p) {
        echo $p->id . " | " . $p->period_name . " | combo: " . $p->period_combo . " | order: " . $p->display_order . "\n";
    }
    echo "</pre>";

    echo "<h3>All OK!</h3>";
} catch (Exception $e) {
    echo "<h3>Error:</h3><pre>" . $e->getMessage() . "\n" . $e->getTraceAsString() . "</pre>";
}
