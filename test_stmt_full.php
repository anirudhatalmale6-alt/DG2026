<?php
require __DIR__.'/application/vendor/autoload.php';
$app = require_once __DIR__.'/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle($request = Illuminate\Http\Request::capture());
header('Content-Type: application/json');

use Illuminate\Support\Facades\DB;

// Find a client with lots of data (sample data is in tax year 2025 or 2026)
$bestClient = DB::table('cims_emp201_declarations')
    ->select('client_id', 'financial_year', DB::raw('COUNT(*) as cnt'))
    ->groupBy('client_id', 'financial_year')
    ->orderBy('cnt', 'desc')
    ->first();

// Now call the controller's apiStatementData method directly
$controller = app()->make(\Modules\CIMS_EMP201\Http\Controllers\Emp201Controller::class);
$request = new \Illuminate\Http\Request([
    'client_id' => $bestClient->client_id,
    'tax_year' => $bestClient->financial_year,
]);

$response = $controller->apiStatementData($request);
$data = json_decode($response->getContent(), true);

echo json_encode([
    'test_client_id' => $bestClient->client_id,
    'test_tax_year' => $bestClient->financial_year,
    'declarations_count' => $bestClient->cnt,
    'client_name' => $data['client']['company_name'] ?? 'N/A',
    'periods_count' => count($data['periods'] ?? []),
    'summary' => $data['summary'] ?? null,
    'aging' => $data['aging'] ?? null,
    'compliance' => $data['compliance'] ?? null,
    'first_period' => isset($data['periods'][0]) ? [
        'label' => $data['periods'][0]['period_label'],
        'txn_count' => count($data['periods'][0]['transactions']),
        'balance' => $data['periods'][0]['balance_total'],
    ] : null,
    'error' => $data['error'] ?? null,
], JSON_PRETTY_PRINT);
