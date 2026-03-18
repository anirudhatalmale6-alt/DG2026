<?php
header('Content-Type: application/json');

require __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle($request = Illuminate\Http\Request::capture());

$result = [];

// Active clients with PAYE numbers
$result['clients'] = DB::table('client_master')
    ->where('is_active', 1)
    ->select('client_id', 'client_code', 'company_name', 'trading_name',
             'paye_number', 'sdl_number', 'uif_number',
             'company_reg_number', 'vat_number', 'tax_number')
    ->orderBy('client_id')
    ->limit(20)
    ->get()
    ->toArray();

// Tax years
$result['tax_years'] = DB::table('cims_document_periods')
    ->distinct()
    ->pluck('tax_year')
    ->sort()
    ->values()
    ->toArray();

// Periods
$result['periods'] = DB::table('cims_document_periods')
    ->select('id', 'period_name', 'period_combo', 'tax_year')
    ->orderBy('tax_year')
    ->orderBy('period_combo')
    ->get()
    ->toArray();

// Existing EMP201 count
$result['existing_count'] = DB::table('cims_emp201_declarations')
    ->whereNull('deleted_at')
    ->count();

echo json_encode($result, JSON_PRETTY_PRINT);
