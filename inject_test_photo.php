<?php
// Create a simple test profile photo and inject it into cims_persons
$pdo = new PDO("mysql:host=localhost;dbname=grow_crm_2026;port=3306", "5fokp_qnbo1", "4P9716bzm7598A");

// First check what columns exist for profile_photo path pattern
$stmt = $pdo->query("SELECT id, firstname, surname, profile_photo FROM cims_persons WHERE id IN (1,3,5,6)");
echo "=== Current persons ===\n";
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo $row['id'] . " | " . $row['firstname'] . " " . $row['surname'] . " | photo: " . ($row['profile_photo'] ?? 'NULL') . "\n";
}

// Check what path format is used for photos - look at any existing photo references in the system
echo "\n=== Checking existing photo paths in system ===\n";
$stmt2 = $pdo->query("SELECT profile_photo FROM cims_persons WHERE profile_photo IS NOT NULL AND profile_photo != '' LIMIT 5");
$found = false;
while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
    echo "Found photo path: " . $row2['profile_photo'] . "\n";
    $found = true;
}
if (!$found) {
    echo "No existing photos found in cims_persons\n";
}

// Check if there's a persons directory in storage
echo "\n=== Checking storage paths ===\n";
$storagePaths = [
    '/usr/www/users/smartucbmh/application/storage/app/public/',
    '/usr/www/users/smartucbmh/storage/',
];
foreach ($storagePaths as $sp) {
    if (is_dir($sp)) {
        echo "Found: $sp\n";
        $dirs = array_diff(scandir($sp), array('.', '..'));
        foreach ($dirs as $d) {
            echo "  - $d\n";
        }
    }
}

// Check client_master_directors for any existing profile_photo values
echo "\n=== Existing director profile photos ===\n";
$stmt3 = $pdo->query("SELECT id, profile_photo FROM client_master_directors WHERE profile_photo IS NOT NULL AND profile_photo != '' LIMIT 5");
$found2 = false;
while ($row3 = $stmt3->fetch(PDO::FETCH_ASSOC)) {
    echo "Director ID " . $row3['id'] . ": " . $row3['profile_photo'] . "\n";
    $found2 = true;
}
if (!$found2) {
    echo "No existing director profile photos found\n";
}
?>
