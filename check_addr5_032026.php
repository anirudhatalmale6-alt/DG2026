<?php
require __DIR__.'/application/vendor/autoload.php';
$app = require_once __DIR__.'/application/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// ATP100 client_id
$cl = DB::table('client_master')->where('client_code', 'ATP100')->first();
echo "ATP100 client_id = {$cl->client_id}\n\n";

// Check cims_client_addresses for client_id=16
echo "=== cims_client_addresses for client_id=16 ===\n";
$cas = DB::table('cims_client_addresses')->where('client_id', 16)->get();
echo "Count: " . count($cas) . "\n";
foreach($cas as $ca) {
    foreach((array)$ca as $k => $v) { echo "  {$k}: " . ($v ?? 'NULL') . "\n"; }
    echo "  ---\n";
    if ($ca->address_id) {
        $a = DB::table('cims_addresses')->where('id', $ca->address_id)->first();
        if ($a) {
            echo "  >> Address record:\n";
            echo "    street_number: " . ($a->street_number ?? 'NULL') . "\n";
            echo "    street_name: " . ($a->street_name ?? 'NULL') . "\n";
            echo "    suburb: " . ($a->suburb ?? 'NULL') . "\n";
            echo "    city: " . ($a->city ?? 'NULL') . "\n";
            echo "    province: " . ($a->province ?? 'NULL') . "\n";
            echo "    postal_code: " . ($a->postal_code ?? 'NULL') . "\n";
            echo "    long_address: " . ($a->long_address ?? 'NULL') . "\n";
        } else {
            echo "  >> No address record found for id={$ca->address_id}\n";
        }
    }
}

// Also check client_master_addresses for client_id=16
echo "\n=== client_master_addresses for client_id=16 ===\n";
$cmas = DB::table('client_master_addresses')->where('client_id', 16)->get();
echo "Count: " . count($cmas) . "\n";
foreach($cmas as $cma) {
    echo "  type: {$cma->address_type}, address_id: {$cma->address_id}, long_address: " . ($cma->long_address ?? 'NULL') . "\n";
    if ($cma->address_id) {
        $a2 = DB::table('cims_addresses')->where('id', $cma->address_id)->first();
        if ($a2) {
            echo "    >> long_address: " . ($a2->long_address ?? 'NULL') . "\n";
            echo "    >> street: " . ($a2->street_number ?? '') . " " . ($a2->street_name ?? '') . "\n";
            echo "    >> suburb: " . ($a2->suburb ?? 'NULL') . ", city: " . ($a2->city ?? 'NULL') . "\n";
        }
    }
}
