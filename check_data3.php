<?php
header('Content-Type: application/json');

// Bootstrap Laravel
require __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle($request = Illuminate\Http\Request::capture());

$result = [];

// Active clients
$result['clients'] = DB::table('client_master')
    ->where('is_active', 1)
    ->select('client_id', 'clientcode', 'company_name', 'paye_number', 'sdl_number', 'uif_number')
    ->orderBy('client_id')
    ->limit(20)
    ->get()
    ->toArray();

// Financial years
$result['years'] = DB::table('cims_document_periods')
    ->distinct()
    ->pluck('financial_year')
    ->sort()
    ->values()
    ->toArray();

// Periods for recent years
$result['periods'] = DB::table('cims_document_periods')
    ->whereIn('financial_year', [2024, 2025, 2026])
    ->select('id', 'period_name', 'period_combo', 'financial_year')
    ->orderBy('financial_year')
    ->orderBy('period_combo')
    ->get()
    ->toArray();

// Existing EMP201 count
$result['existing_count'] = DB::table('cims_emp201_declarations')
    ->whereNull('deleted_at')
    ->count();

// Existing records sample
$result['existing'] = DB::table('cims_emp201_declarations')
    ->whereNull('deleted_at')
    ->select('id', 'client_id', 'client_code', 'financial_year', 'period_combo')
    ->limit(10)
    ->get()
    ->toArray();

echo json_encode($result, JSON_PRETTY_PRINT);
