<?php
require __DIR__.'/application/vendor/autoload.php';
$app = require_once __DIR__.'/application/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$cl = DB::table('client_master')->where('client_code', 'ATP100')->first();
if ($cl) {
    foreach((array)$cl as $k => $v) {
        if (preg_match('/phone|tel|mobile|whatsapp|email|fax|direct|web/i', $k)) {
            echo "FIELD=[{$k}] VALUE=[" . ($v ?: 'NULL') . "]\n";
        }
    }
}
