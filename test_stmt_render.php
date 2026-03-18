<?php
require __DIR__.'/application/vendor/autoload.php';
$app = require_once __DIR__.'/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle($request = Illuminate\Http\Request::capture());
header('Content-Type: application/json');

// Test: make an internal request to the statement API
$controller = app()->make(\Modules\CIMS_EMP201\Http\Controllers\Emp201Controller::class);

// Find client with most data
$bestClient = \Illuminate\Support\Facades\DB::table('cims_emp201_declarations')
    ->select('client_id', 'financial_year', \Illuminate\Support\Facades\DB::raw('COUNT(*) as cnt'))
    ->groupBy('client_id', 'financial_year')
    ->orderBy('cnt', 'desc')
    ->first();

$request = new \Illuminate\Http\Request([
    'client_id' => $bestClient->client_id,
    'tax_year' => $bestClient->financial_year,
]);

$response = $controller->apiStatementData($request);
$data = json_decode($response->getContent(), true);

// Output summary
echo json_encode([
    'success' => true,
    'client' => $data['client']['company_name'] ?? 'N/A',
    'paye' => $data['client']['paye_number'] ?? 'N/A',
    'address' => $data['client']['address'],
    'tax_year' => $data['tax_year'],
    'total_periods' => count($data['periods']),
    'period_details' => array_map(function($p) {
        return [
            'label' => $p['period_label'],
            'month' => $p['month_label'],
            'txn_count' => count($p['transactions']),
            'balance_paye' => $p['balance_paye'],
            'balance_sdl' => $p['balance_sdl'],
            'balance_uif' => $p['balance_uif'],
            'balance_total' => $p['balance_total'],
        ];
    }, $data['periods']),
    'summary' => $data['summary'],
    'aging' => $data['aging'],
    'compliance' => $data['compliance'],
], JSON_PRETTY_PRINT);
