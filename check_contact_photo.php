<?php
require __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle($request = Illuminate\Http\Request::capture());

$contacts = DB::table('cims_client_contacts')
    ->whereNotNull('photo')
    ->where('photo', '!=', '')
    ->select('id', 'first_name', 'last_name', 'email', 'photo')
    ->limit(10)
    ->get();

foreach ($contacts as $c) {
    echo "Contact #{$c->id}: {$c->first_name} {$c->last_name} | email: {$c->email} | photo: {$c->photo}\n";
}

// Check if the photo file exists
if ($contacts->count() > 0) {
    $first = $contacts->first();
    $paths = [
        base_path('../storage/contact_photos/' . $first->photo),
        base_path('storage/contact_photos/' . $first->photo),
        public_path('storage/contact_photos/' . $first->photo),
    ];
    echo "\nChecking file paths for '{$first->photo}':\n";
    foreach ($paths as $p) {
        echo "  $p => " . (file_exists($p) ? "EXISTS" : "NOT FOUND") . "\n";
    }
}
