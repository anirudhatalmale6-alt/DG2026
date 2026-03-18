<?php
header('Content-Type: text/plain');

require __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

// Get all persons with profile_photo
$persons = \DB::table('cims_persons')->get(['id', 'firstname', 'surname', 'identity_number', 'identity_type', 'nationality', 'profile_photo', 'profile_picture']);
echo "=== All persons ===\n";
foreach ($persons as $p) {
    echo "ID={$p->id}: {$p->firstname} {$p->surname}\n";
    echo "  identity_number: {$p->identity_number}\n";
    echo "  identity_type: {$p->identity_type}\n";
    echo "  nationality: {$p->nationality}\n";
    echo "  profile_photo: " . ($p->profile_photo ?? 'NULL') . "\n";
    echo "  profile_picture: " . ($p->profile_picture ?? 'NULL') . "\n\n";
}
