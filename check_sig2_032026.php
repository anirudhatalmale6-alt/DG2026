<?php
require __DIR__.'/application/vendor/autoload.php';
$app = require_once __DIR__.'/application/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Full signature details
$sigs = DB::table('cims_email_signatures')->get();
foreach($sigs as $s) {
    echo "=== Signature ID: {$s->id} ===\n";
    foreach((array)$s as $k => $v) {
        $v = is_null($v) ? 'NULL' : (strlen((string)$v) > 120 ? substr($v, 0, 120) . '...' : $v);
        echo "  {$k}: {$v}\n";
    }
    echo "\n";
}
