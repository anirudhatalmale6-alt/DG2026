<?php
$pdo = new PDO("mysql:host=localhost;dbname=grow_crm_2026;port=3306", "5fokp_qnbo1", "4P9716bzm7598A");

// Update lookup table
$stmt1 = $pdo->prepare("UPDATE cims_director_status SET name = 'Active' WHERE id = 1 AND name = 'Current'");
$stmt1->execute();
echo "cims_director_status updated: " . $stmt1->rowCount() . " row(s)\n";

// Update any existing director records that have "Current" as status name
$stmt2 = $pdo->prepare("UPDATE client_master_directors SET director_status_name = 'Active' WHERE director_status_name = 'Current'");
$stmt2->execute();
echo "client_master_directors updated: " . $stmt2->rowCount() . " row(s)\n";

// Verify
echo "\n=== Verification ===\n";
$stmt3 = $pdo->query("SELECT * FROM cims_director_status ORDER BY id");
while ($row = $stmt3->fetch(PDO::FETCH_ASSOC)) {
    echo "ID: " . $row['id'] . " | name: " . $row['name'] . "\n";
}
?>
