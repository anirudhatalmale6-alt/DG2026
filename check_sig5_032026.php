<?php
require __DIR__.'/application/vendor/autoload.php';
$app = require_once __DIR__.'/application/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Simulate a web request with session
// Check if there's a session/cookie that could tell us who is logged in
echo "Auth check (CLI): " . (Auth::check() ? 'YES, user=' . Auth::id() : 'NO (expected in CLI)') . "\n\n";

// The signature only exists for user_id=1
// If client logs in as user 2,3,4 etc - no signature
echo "Only user_id=1 has a signature.\n";
echo "If the client logs in as any other user, \$signatureHtml will be empty.\n\n";

// Check users table for likely login users
$users = DB::table('users')->select('id','first_name','last_name','email','role_id')->get();
foreach($users as $u) {
    echo "User {$u->id}: {$u->first_name} {$u->last_name} ({$u->email}) role={$u->role_id}\n";
}
