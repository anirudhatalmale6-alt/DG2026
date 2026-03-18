<?php
require __DIR__.'/application/vendor/autoload.php';
$app = require_once __DIR__.'/application/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// List all signatures
$sigs = DB::table('cims_email_signatures')->get();
echo "=== ALL SIGNATURES ===\n";
foreach($sigs as $s) {
    echo "ID: {$s->id} | user_id: {$s->user_id} | name: {$s->full_name} | active: {$s->is_active} | has_html: " . (!empty($s->signature_html) ? 'YES' : 'NO') . "\n";
}

// Check settings
echo "\n=== EMAIL SETTINGS ===\n";
$settings = DB::table('cims_email_settings')->get();
foreach($settings as $st) {
    $val = strlen($st->setting_value) > 80 ? substr($st->setting_value, 0, 80) . '...' : $st->setting_value;
    echo "{$st->setting_key}: {$val}\n";
}

// Check who the current logged-in users are
echo "\n=== USERS ===\n";
$users = DB::table('users')->select('id','first_name','last_name','email')->limit(10)->get();
foreach($users as $u) {
    echo "ID: {$u->id} | {$u->first_name} {$u->last_name} | {$u->email}\n";
}
