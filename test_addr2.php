<?php
require_once '/usr/www/users/smartucbmh/application/vendor/autoload.php';
$app = require_once '/usr/www/users/smartucbmh/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

$types = DB::table('client_master_addresses')
    ->select('address_type', DB::raw('count(*) as cnt'))
    ->groupBy('address_type')
    ->get();
echo "=== Address types in use ===\n";
foreach ($types as $t) {
    echo "'" . $t->address_type . "' => " . $t->cnt . " records\n";
}
