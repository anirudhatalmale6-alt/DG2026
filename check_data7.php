<?php
header('Content-Type: application/json');

require __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle($request = Illuminate\Http\Request::capture());

// Check existing declarations
$result = [];
$result['declarations'] = DB::table('cims_emp201_declarations')
    ->whereNull('deleted_at')
    ->select('id', 'client_id', 'client_code', 'company_name', 'financial_year', 'period_combo', 'pay_period', 'period_id',
             'paye_liability', 'sdl_liability', 'uif_liability', 'tax_payable', 'status')
    ->get()
    ->toArray();

echo json_encode($result, JSON_PRETTY_PRINT);
