<?php
require __DIR__.'/application/vendor/autoload.php';
$app = require_once __DIR__.'/application/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Check what Auth::id() would be for each possible user
echo "=== Checking signature per user ===\n";
$users = DB::table('users')->select('id','first_name','last_name')->get();
foreach($users as $u) {
    $sig = DB::table('cims_email_signatures')->where('user_id', $u->id)->where('is_active', 1)->first();
    echo "User {$u->id} ({$u->first_name} {$u->last_name}): " . ($sig ? "HAS sig (name={$sig->full_name})" : "NO signature") . "\n";
}

// The Krish signature - test JSON encoding
$sig = DB::table('cims_email_signatures')->where('id', 1)->first();
$controller = app()->make('Modules\CIMS_Email\Http\Controllers\EmailController');
$method = new ReflectionMethod($controller, 'buildSignatureHtml');
$method->setAccessible(true);
$html = $method->invoke($controller, $sig);

echo "\n=== JSON-encoded signature (first 300 chars) ===\n";
echo substr(json_encode($html), 0, 300) . "\n";

// Check if json_encode fails
if (json_encode($html) === false) {
    echo "\nJSON ENCODE FAILED! Error: " . json_last_error_msg() . "\n";
}
