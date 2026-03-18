<?php
$pdo = new PDO("mysql:host=localhost;dbname=grow_crm_2026;port=3306", "5fokp_qnbo1", "4P9716bzm7598A");

// Set person profile_photo back to /storage/ prefix for AJAX to work
$stmt = $pdo->prepare("UPDATE cims_persons SET profile_photo = ? WHERE id = 1");
$stmt->execute(['/storage/profile_photos/test_profile_photo.jpg']);
echo "Updated cims_persons: " . $stmt->rowCount() . " row(s)\n";

// Also update the existing director record to have /storage/ prefix
// so existingDirectors asset("storage/" + value) => /storage/storage/profile_photos/... which we made accessible
$stmt2 = $pdo->prepare("UPDATE client_master_directors SET profile_photo = ? WHERE person_id = 1");
$stmt2->execute(['/storage/profile_photos/test_profile_photo.jpg']);
echo "Updated client_master_directors: " . $stmt2->rowCount() . " row(s)\n";

// Verify
echo "\ncims_persons: " . $pdo->query("SELECT profile_photo FROM cims_persons WHERE id=1")->fetchColumn() . "\n";
echo "client_master_directors: " . $pdo->query("SELECT profile_photo FROM client_master_directors WHERE person_id=1")->fetchColumn() . "\n";
?>
