<?php
require __DIR__.'/application/vendor/autoload.php';
$app = require_once __DIR__.'/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle($request = Illuminate\Http\Request::capture());
header('Content-Type: application/json');

use Illuminate\Support\Facades\DB;
use Modules\CIMS_EMP201\Models\Emp201Declaration;

// Get first client with declarations
$firstDecl = DB::table('cims_emp201_declarations')
    ->select('client_id', 'financial_year')
    ->groupBy('client_id', 'financial_year')
    ->orderBy('client_id')
    ->first();

if (!$firstDecl) {
    echo json_encode(['error' => 'No declarations found']);
    exit;
}

$clientId = $firstDecl->client_id;
$taxYear = $firstDecl->financial_year;

// Get client
$client = DB::table('client_master')->where('client_id', $clientId)->first();

// Get address
$addrLink = DB::table('client_master_addresses')
    ->where('client_id', $clientId)
    ->whereIn('address_type', ['Physical', 'Business', 'Registered'])
    ->first();
$address = null;
if ($addrLink && $addrLink->address_id) {
    $address = DB::table('cims_addresses')->where('id', $addrLink->address_id)->first();
}

// Get declarations
$declarations = DB::table('cims_emp201_declarations')
    ->where('client_id', $clientId)
    ->where('financial_year', $taxYear)
    ->get();

echo json_encode([
    'test_client_id' => $clientId,
    'test_tax_year' => $taxYear,
    'client_name' => $client->company_name ?? 'unknown',
    'client_paye' => $client->paye_number ?? 'N/A',
    'address_found' => $address ? true : false,
    'address_data' => $address ? [
        'street' => ($address->street_number ?? '') . ' ' . ($address->street_name ?? ''),
        'city' => $address->city ?? '',
        'postal' => $address->postal_code ?? '',
    ] : null,
    'declarations_count' => count($declarations),
    'period_combos' => $declarations->pluck('period_combo')->toArray(),
    'sample_decl' => $declarations->first() ? [
        'paye_payable' => $declarations->first()->paye_payable,
        'sdl_payable' => $declarations->first()->sdl_payable,
        'uif_payable' => $declarations->first()->uif_payable,
        'amount_paid' => $declarations->first()->amount_paid,
        'penalty' => $declarations->first()->penalty,
        'interest' => $declarations->first()->interest,
    ] : null,
], JSON_PRETTY_PRINT);
