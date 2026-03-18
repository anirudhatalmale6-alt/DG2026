<?php
$pdo = new PDO("mysql:host=localhost;dbname=grow_crm_2026;port=3306", "5fokp_qnbo1", "4P9716bzm7598A");

echo "=== cims_director_status table ===\n";
$stmt = $pdo->query("SELECT * FROM cims_director_status ORDER BY id");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "ID: " . $row['id'] . " | name: " . $row['name'] . " | is_active: " . $row['is_active'] . "\n";
}

echo "\n=== Any code references to 'Current' in directors ===\n";
echo "Checking client_master_directors for status names used:\n";
$stmt2 = $pdo->query("SELECT DISTINCT director_status_id, director_status_name FROM client_master_directors");
while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
    echo "status_id: " . $row2['director_status_id'] . " | status_name: " . $row2['director_status_name'] . "\n";
}
?>
