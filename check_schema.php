<?php
error_reporting(0);
define('LARAVEL_START', microtime(true));
require __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

echo "<pre>";

echo "=== INVOICES TABLE ===\n";
$cols = \DB::select("SHOW COLUMNS FROM invoices");
foreach ($cols as $c) echo "{$c->Field} ({$c->Type})\n";

echo "\n=== PAYMENTS TABLE ===\n";
$cols = \DB::select("SHOW COLUMNS FROM payments");
foreach ($cols as $c) echo "{$c->Field} ({$c->Type})\n";

echo "\n=== CLIENTS TABLE ===\n";
$cols = \DB::select("SHOW COLUMNS FROM clients");
foreach ($cols as $c) echo "{$c->Field} ({$c->Type})\n";

echo "\n=== SAMPLE INVOICE ===\n";
$inv = \DB::table('invoices')->first();
if ($inv) print_r((array)$inv);

echo "\n=== SAMPLE PAYMENT ===\n";
$pay = \DB::table('payments')->first();
if ($pay) print_r((array)$pay);

echo "\n=== COMPANY SETTINGS ===\n";
$settings = \DB::table('settings')->where('settings_id', 1)->first();
if ($settings) {
    foreach ((array)$settings as $k => $v) {
        if (strpos($k, 'company') !== false || strpos($k, 'system') !== false) {
            echo "$k = $v\n";
        }
    }
}

echo "</pre>";
