<?php
error_reporting(0);
define('LARAVEL_START', microtime(true));
require __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

echo "<pre>";

// Invoice status values
echo "=== INVOICE STATUSES ===\n";
$statuses = \DB::table('invoices')->select('bill_status')->distinct()->get();
foreach ($statuses as $s) echo "Status: {$s->bill_status}\n";

// Counts
echo "\nInvoices: " . \DB::table('invoices')->count() . "\n";
echo "Payments: " . \DB::table('payments')->count() . "\n";
echo "Clients: " . \DB::table('clients')->count() . "\n";

// Invoice format settings
echo "\n=== FORMAT SETTINGS ===\n";
$settings = \DB::table('settings')->where('settings_id', 1)->first();
foreach ((array)$settings as $k => $v) {
    if (strpos($k, 'invoice') !== false || strpos($k, 'prefix') !== false || strpos($k, 'customfield') !== false) {
        echo "$k = $v\n";
    }
}

// All clients
echo "\n=== CLIENTS ===\n";
$clients = \DB::table('clients')->select('client_id', 'client_company_name', 'client_vat', 'client_billing_street', 'client_billing_city', 'client_billing_state', 'client_billing_zip', 'client_billing_country')->get();
foreach ($clients as $c) {
    echo "ID:{$c->client_id} | {$c->client_company_name} | VAT:{$c->client_vat}\n";
}

// Check bill custom fields table
echo "\n=== TABLES WITH CUSTOM ===\n";
$tables = \DB::select("SHOW TABLES LIKE '%custom%'");
foreach ($tables as $t) {
    $arr = (array)$t;
    echo array_values($arr)[0] . "\n";
}

// Check for client code in invoice custom fields
echo "\n=== INVOICE CUSTOM FIELDS (bill_custom_*) ===\n";
$inv = \DB::table('invoices')->first();
if ($inv) {
    foreach ((array)$inv as $k => $v) {
        if (strpos($k, 'custom') !== false && $v) {
            echo "$k = $v\n";
        }
    }
}

echo "</pre>";
