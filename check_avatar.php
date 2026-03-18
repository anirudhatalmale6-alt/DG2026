<?php
require __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle($request = Illuminate\Http\Request::capture());

$users = DB::table('users')->select('id', 'first_name', 'last_name', 'avatar_directory', 'avatar_filename')->get();
foreach ($users as $u) {
    echo "User #{$u->id}: {$u->first_name} {$u->last_name} | dir: '{$u->avatar_directory}' | file: '{$u->avatar_filename}'\n";
}
