<?php
require_once '/usr/www/users/smartucbmh/application/vendor/autoload.php';
$app = require_once '/usr/www/users/smartucbmh/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

// Check addresses for client 16
$all = DB::table('client_master_addresses')->where('client_id', 16)->get();
echo "=== All addresses for client 16 ===\n";
foreach ($all as $a) {
    echo "type: " . $a->address_type . " | address_id: " . $a->address_id . "\n";
}

// Check what the controller would return
$reg = DB::table('client_master_addresses')
    ->where('client_id', 16)
    ->where('address_type', 'Registered')
    ->first();
echo "\nRegistered address link: " . ($reg ? "YES (id: {$reg->address_id})" : "NOT FOUND") . "\n";

if (!$reg) {
    $fallback = DB::table('client_master_addresses')
        ->where('client_id', 16)
        ->whereNotNull('address_id')
        ->first();
    echo "Fallback address link: " . ($fallback ? "YES (type: {$fallback->address_type}, id: {$fallback->address_id})" : "NOT FOUND") . "\n";
    $reg = $fallback;
}

if ($reg && $reg->address_id) {
    $addr = DB::table('cims_addresses')->where('id', $reg->address_id)->first();
    if ($addr) {
        echo "\nAddress details:\n";
        echo "Street: " . ($addr->street_number ?? '') . " " . ($addr->street_name ?? '') . "\n";
        echo "Suburb: " . ($addr->suburb ?? '') . "\n";
        echo "City: " . ($addr->city ?? '') . "\n";
        echo "Province: " . ($addr->province ?? '') . "\n";
        echo "Postal: " . ($addr->postal_code ?? '') . "\n";
    }
}

// Also check client 17
echo "\n=== Client 17 addresses ===\n";
$all2 = DB::table('client_master_addresses')->where('client_id', 17)->get();
foreach ($all2 as $a) {
    echo "type: " . $a->address_type . " | address_id: " . $a->address_id . "\n";
}
