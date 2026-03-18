<?php
// Bootstrap Laravel to test asset() function
require __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Test asset() with different paths
$paths = [
    "storage/profile_photos/test_profile_photo.jpg",
    "storage//storage/profile_photos/test_profile_photo.jpg",
];

foreach ($paths as $p) {
    echo "asset('$p') => " . asset($p) . "\n";
}

// Test with the actual controller logic
$profile_photo_value = '/storage/profile_photos/test_profile_photo.jpg';
echo "\n--- With value from DB: '$profile_photo_value' ---\n";
echo "asset('storage/$profile_photo_value') => " . asset("storage/$profile_photo_value") . "\n";

$profile_photo_value2 = 'profile_photos/test_profile_photo.jpg';
echo "\n--- With value from DB: '$profile_photo_value2' ---\n";
echo "asset('storage/$profile_photo_value2') => " . asset("storage/$profile_photo_value2") . "\n";
?>
