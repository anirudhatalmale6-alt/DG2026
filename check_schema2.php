<?php
error_reporting(0);
define('LARAVEL_START', microtime(true));
require __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

echo "<pre>";

// Custom fields for invoices/clients
echo "=== CUSTOM FIELDS ===\n";
$fields = \DB::table('custom_fields')->get();
foreach ($fields as $f) {
    echo "ID:{$f->customfields_id} type:{$f->customfields_type} name:{$f->customfields_name} status:{$f->customfields_status}\n";
}

// Invoice status values
echo "\n=== INVOICE STATUSES ===\n";
$statuses = \DB::table('invoices')->select('bill_status')->distinct()->get();
foreach ($statuses as $s) echo "Status: {$s->bill_status}\n";

// Count invoices and payments
echo "\n=== COUNTS ===\n";
echo "Invoices: " . \DB::table('invoices')->count() . "\n";
echo "Payments: " . \DB::table('payments')->count() . "\n";
echo "Clients: " . \DB::table('clients')->count() . "\n";

// Invoice formatting
echo "\n=== INVOICE FORMAT SETTINGS ===\n";
$settings = \DB::table('settings')->where('settings_id', 1)->first();
foreach ((array)$settings as $k => $v) {
    if (strpos($k, 'invoice') !== false || strpos($k, 'bill') !== false || strpos($k, 'prefix') !== false) {
        echo "$k = $v\n";
    }
}

// Sample: all clients with names
echo "\n=== ALL CLIENTS ===\n";
$clients = \DB::table('clients')->select('client_id', 'client_company_name', 'client_billing_street', 'client_billing_city', 'client_vat', 'client_custom_field_1', 'client_custom_field_2')->get();
foreach ($clients as $c) {
    echo "ID:{$c->client_id} Name:{$c->client_company_name} VAT:{$c->client_vat} CF1:{$c->client_custom_field_1} CF2:{$c->client_custom_field_2}\n";
}

echo "</pre>";
