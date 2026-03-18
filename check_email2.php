<?php
require_once '/usr/www/users/smartucbmh/application/vendor/autoload.php';
$app = require_once '/usr/www/users/smartucbmh/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

// Sample clients with email
$sample = DB::table('client_master')
    ->whereNotNull('email')
    ->where('email', '!=', '')
    ->select('client_id', 'client_code', 'company_name', 'email')
    ->limit(5)
    ->get();
echo "=== Clients with email ===\n";
foreach ($sample as $s) {
    echo $s->client_id . " | " . $s->client_code . " | " . $s->company_name . " | " . $s->email . "\n";
}

// Check if Mail facade works
echo "\n=== Mail config ===\n";
echo "driver: " . config('mail.default') . "\n";
echo "from_address: " . config('system.settings_email_from_address') . "\n";
echo "from_name: " . config('system.settings_email_from_name') . "\n";
echo "smtp_host: " . config('mail.mailers.smtp.host') . "\n";
