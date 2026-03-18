<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

$user = \App\Models\User::first();
\Illuminate\Support\Facades\Auth::login($user);

// Test the SARS rep lookup for client_id 13
$sarsRep = \Illuminate\Support\Facades\DB::table('client_master_directors')
    ->where('client_id', 13)
    ->where('director_type_id', 3)
    ->where('is_active', 1)
    ->first();

echo "<h3>SARS Rep for Client 13:</h3><pre>";
if ($sarsRep) {
    echo "Found: YES\n";
    echo "First Name: " . ($sarsRep->firstname ?? 'NULL') . "\n";
    echo "Surname: " . ($sarsRep->surname ?? 'NULL') . "\n";
    echo "Type: " . ($sarsRep->director_type_name ?? 'NULL') . "\n";
    echo "Email: " . ($sarsRep->email ?? 'NULL') . "\n";
    echo "Mobile: " . ($sarsRep->mobile_phone ?? 'NULL') . "\n";
    echo "Office: " . ($sarsRep->office_phone ?? 'NULL') . "\n";
} else {
    echo "NOT FOUND\n";
}
echo "</pre>";

// Test for client without SARS rep
echo "<h3>SARS Rep for Client 17 (no SARS rep, has Director):</h3><pre>";
$sarsRep17 = \Illuminate\Support\Facades\DB::table('client_master_directors')
    ->where('client_id', 17)
    ->where('director_type_id', 3)
    ->where('is_active', 1)
    ->first();
if ($sarsRep17) {
    echo "SARS Rep found: " . $sarsRep17->firstname . " " . $sarsRep17->surname . "\n";
} else {
    echo "No SARS Rep - checking fallback (first director)\n";
    $fallback = \Illuminate\Support\Facades\DB::table('client_master_directors')
        ->where('client_id', 17)
        ->where('is_active', 1)
        ->first();
    if ($fallback) {
        echo "Fallback found: " . $fallback->firstname . " " . $fallback->surname . " (" . $fallback->director_type_name . ")\n";
    } else {
        echo "No directors at all\n";
    }
}
echo "</pre>";

echo "<h3>All OK!</h3>";
