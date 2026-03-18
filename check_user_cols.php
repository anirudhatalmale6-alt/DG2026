<?php
require __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle($request = Illuminate\Http\Request::capture());

$cols = DB::select("SHOW COLUMNS FROM users");
foreach ($cols as $c) {
    echo $c->Field . " (" . $c->Type . ")\n";
}
echo "\n--- Signature columns ---\n";
$cols2 = DB::select("SHOW COLUMNS FROM cims_email_signatures");
foreach ($cols2 as $c) {
    echo $c->Field . " (" . $c->Type . ")\n";
}
echo "\n--- Current user photo ---\n";
$user = DB::table('users')->where('id', 1)->first();
$photoFields = ['avatar', 'photo', 'profile_image', 'profile_photo', 'image'];
foreach ($photoFields as $f) {
    if (isset($user->$f)) echo "$f: " . $user->$f . "\n";
}
