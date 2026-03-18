<?php
$pdo = new PDO("mysql:host=localhost;dbname=grow_crm_2026;port=3306", "5fokp_qnbo1", "4P9716bzm7598A");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
    echo "=== client_master_directors records ===\n";
    $stmt = $pdo->query("SELECT id, client_id, person_id, firstname, surname, profile_photo FROM client_master_directors LIMIT 10");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "ID: " . $row['id'] . " | person_id: " . $row['person_id'] . " | name: " . ($row['firstname'] ?? '') . " " . ($row['surname'] ?? '') . " | profile_photo: '" . ($row['profile_photo'] ?? 'NULL') . "'\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== Profile photo columns ===\n";
$stmt2 = $pdo->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='grow_crm_2026' AND TABLE_NAME='client_master_directors' AND COLUMN_NAME LIKE '%photo%'");
while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
    echo "Column: " . $row2['COLUMN_NAME'] . "\n";
}

echo "\n=== Person profile_photo for person_id used ===\n";
$stmt3 = $pdo->query("SELECT id, profile_photo FROM cims_persons WHERE id IN (SELECT DISTINCT person_id FROM client_master_directors)");
while ($row3 = $stmt3->fetch(PDO::FETCH_ASSOC)) {
    echo "Person ID " . $row3['id'] . ": '" . ($row3['profile_photo'] ?? 'NULL') . "'\n";
}
?>
