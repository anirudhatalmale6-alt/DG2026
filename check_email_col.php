<?php
require_once '/usr/www/users/smartucbmh/application/vendor/autoload.php';
$app = require_once '/usr/www/users/smartucbmh/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

// Check client_master for email columns
$cols = DB::select("SHOW COLUMNS FROM client_master LIKE '%email%'");
echo "=== client_master email columns ===\n";
foreach ($cols as $c) {
    echo $c->Field . " | " . $c->Type . " | " . $c->Null . " | Default: " . $c->Default . "\n";
}

// Get a sample
$sample = DB::table('client_master')
    ->whereNotNull('contact_email')
    ->where('contact_email', '!=', '')
    ->select('client_id', 'client_code', 'company_name', 'contact_email')
    ->limit(5)
    ->get();
echo "\n=== Sample clients with email ===\n";
foreach ($sample as $s) {
    echo $s->client_id . " | " . $s->client_code . " | " . $s->company_name . " | " . $s->contact_email . "\n";
}

// Check auth user email
echo "\n=== Auth user structure ===\n";
$userCols = DB::select("SHOW COLUMNS FROM users WHERE Field IN ('id','email','first_name','last_name')");
foreach ($userCols as $c) {
    echo $c->Field . " | " . $c->Type . "\n";
}
