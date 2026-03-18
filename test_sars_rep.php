<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

$user = \App\Models\User::first();
\Illuminate\Support\Facades\Auth::login($user);

// Check client_master_directors table
echo "<h3>Director Types:</h3><pre>";
$types = \Illuminate\Support\Facades\DB::table('cims_director_types')->get();
foreach ($types as $t) { echo $t->id . " | " . $t->name . " | active: " . $t->is_active . "\n"; }
echo "</pre>";

// Get a list of clients that have directors
echo "<h3>Clients with directors in client_master_directors:</h3><pre>";
$directors = \Illuminate\Support\Facades\DB::table('client_master_directors')
    ->select('client_id', 'firstname', 'surname', 'director_type_id', 'director_type_name', 'is_active', 'email', 'mobile_phone', 'office_phone')
    ->orderBy('client_id')
    ->get();
foreach ($directors as $d) {
    echo "client_id=" . $d->client_id . " | " . $d->firstname . " " . $d->surname 
         . " | type_id=" . $d->director_type_id . " (" . $d->director_type_name . ")"
         . " | active=" . $d->is_active 
         . " | email=" . $d->email
         . " | mobile=" . $d->mobile_phone
         . " | office=" . $d->office_phone
         . "\n";
}
echo "</pre>";

// Check the legacy table too
echo "<h3>Legacy: tbldirectorclientappoint (first 10):</h3><pre>";
$legacy = \Illuminate\Support\Facades\DB::table('tbldirectorclientappoint')
    ->select('id', 'client_id', 'first_name_1', 'last_name_1', 'cellphone_1', 'email_1', 'type')
    ->limit(10)
    ->get();
foreach ($legacy as $l) {
    echo "id=" . $l->id . " | client_id=" . $l->client_id . " | " . $l->first_name_1 . " " . $l->last_name_1 
         . " | cell=" . $l->cellphone_1 . " | email=" . $l->email_1 . " | type=" . $l->type . "\n";
}
echo "</pre>";
