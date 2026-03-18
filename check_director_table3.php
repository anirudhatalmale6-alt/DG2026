<?php
$pdo = new PDO("mysql:host=localhost;dbname=grow_crm_2026;port=3306", "5fokp_qnbo1", "4P9716bzm7598A");
$stmt = $pdo->query("DESCRIBE client_master_directors");
echo "=== client_master_directors table columns ===\n";
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo $row['Field'] . " | " . $row['Type'] . " | " . $row['Null'] . " | " . $row['Key'] . " | " . ($row['Default'] ?? 'NULL') . "\n";
}
echo "\n=== INDEXES ===\n";
$stmt2 = $pdo->query("SHOW INDEX FROM client_master_directors");
while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
    echo $row2['Key_name'] . " | " . $row2['Column_name'] . " | " . $row2['Non_unique'] . "\n";
}
echo "\n=== cims_persons profile_photo sample ===\n";
$stmt3 = $pdo->query("SELECT id, firstname, surname, profile_photo FROM cims_persons LIMIT 10");
while ($row3 = $stmt3->fetch(PDO::FETCH_ASSOC)) {
    echo $row3['id'] . " | " . $row3['firstname'] . " " . $row3['surname'] . " | photo: " . ($row3['profile_photo'] ?? 'NULL') . "\n";
}
?>
