<?php
$pdo = new PDO("mysql:host=localhost;dbname=grow_crm_2026;port=3306", "5fokp_qnbo1", "4P9716bzm7598A");

// Test what asset() would produce with various path formats
// We can't call asset() without Laravel, but let's simulate it
// asset() basically does: $app_url . '/' . ltrim($path, '/')

$app_url = 'https://smartweigh.co.za';

// Test path formats
$paths = [
    'profile_photos/test_profile_photo.jpg',
    '/storage/profile_photos/test_profile_photo.jpg',
];

foreach ($paths as $p) {
    $asset_result = $app_url . '/' . ltrim("storage/$p", '/');
    echo "DB value: '$p'\n";
    echo "  asset('storage/\$val') => $asset_result\n";
    echo "  Direct as img src => $p\n\n";
}

// Check current values
echo "=== Current DB values ===\n";
$stmt = $pdo->query("SELECT id, profile_photo FROM cims_persons WHERE id = 1");
$row = $stmt->fetch(PDO::FETCH_ASSOC);
echo "cims_persons ID 1: '" . ($row['profile_photo'] ?? 'NULL') . "'\n";

$stmt2 = $pdo->query("SELECT id, person_id, profile_photo FROM client_master_directors");
while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
    echo "client_master_directors ID " . $row2['id'] . ": '" . ($row2['profile_photo'] ?? 'NULL') . "'\n";
}

// Test if the web server resolves double-slash URLs
echo "\n=== URL Test ===\n";
echo "Normal: https://smartweigh.co.za/storage/profile_photos/test_profile_photo.jpg\n";
echo "Double: https://smartweigh.co.za/storage//storage/profile_photos/test_profile_photo.jpg\n";
?>
