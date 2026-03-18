<?php
$pdo = new PDO("mysql:host=localhost;dbname=grow_crm_2026;port=3306", "5fokp_qnbo1", "4P9716bzm7598A");

// Update person ID 1 (Krish Moodley) with profile photo path
$stmt = $pdo->prepare("UPDATE cims_persons SET profile_photo = ? WHERE id = 1");
$stmt->execute(['/storage/profile_photos/test_profile_photo.jpg']);
echo "Updated rows: " . $stmt->rowCount() . "\n";

// Verify
$stmt2 = $pdo->query("SELECT id, firstname, surname, profile_photo FROM cims_persons WHERE id = 1");
$row = $stmt2->fetch(PDO::FETCH_ASSOC);
echo "Person: " . $row['firstname'] . " " . $row['surname'] . " | photo: " . $row['profile_photo'] . "\n";
?>
