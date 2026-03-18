<?php
$pdo = new PDO("mysql:host=localhost;dbname=grow_crm_2026;port=3306", "5fokp_qnbo1", "4P9716bzm7598A");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// The controller does: asset("storage/$director->profile_photo")
// So profile_photo should be just "profile_photos/test_profile_photo.jpg" (no /storage/ prefix)
// Currently it's "/storage/profile_photos/test_profile_photo.jpg" which creates double path

// Fix person record
$stmt = $pdo->prepare("UPDATE cims_persons SET profile_photo = ? WHERE id = 1");
$stmt->execute(['profile_photos/test_profile_photo.jpg']);
echo "Updated cims_persons: " . $stmt->rowCount() . " rows\n";

// Fix director record too
$stmt2 = $pdo->prepare("UPDATE client_master_directors SET profile_photo = ? WHERE person_id = 1");
$stmt2->execute(['profile_photos/test_profile_photo.jpg']);
echo "Updated client_master_directors: " . $stmt2->rowCount() . " rows\n";

// Verify
echo "\n=== Verification ===\n";
$stmt3 = $pdo->query("SELECT id, profile_photo FROM cims_persons WHERE id = 1");
$row = $stmt3->fetch(PDO::FETCH_ASSOC);
echo "Person 1 profile_photo: '" . $row['profile_photo'] . "'\n";

$stmt4 = $pdo->query("SELECT id, profile_photo FROM client_master_directors WHERE person_id = 1");
$row2 = $stmt4->fetch(PDO::FETCH_ASSOC);
echo "Director (person_id=1) profile_photo: '" . $row2['profile_photo'] . "'\n";

// What the asset URL will look like:
echo "\nExpected URL: /storage/profile_photos/test_profile_photo.jpg\n";
?>
