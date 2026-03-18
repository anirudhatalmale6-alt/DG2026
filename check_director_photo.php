<?php
$pdo = new PDO("mysql:host=localhost;dbname=grow_crm_2026;port=3306", "5fokp_qnbo1", "4P9716bzm7598A");

echo "=== client_master_directors records ===\n";
$stmt = $pdo->query("SELECT id, client_id, person_id, firstname, surname, profile_photo, director_profile_image FROM client_master_directors LIMIT 10");
$cols = null;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    if (!$cols) {
        $cols = array_keys($row);
        echo implode(" | ", $cols) . "\n";
    }
    echo implode(" | ", array_map(function($v) { return $v ?? 'NULL'; }, $row)) . "\n";
}

// Check if director_profile_image column exists
echo "\n=== Checking if director_profile_image column exists ===\n";
$stmt2 = $pdo->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='grow_crm_2026' AND TABLE_NAME='client_master_directors' AND COLUMN_NAME LIKE '%profile%'");
while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
    echo "Column: " . $row2['COLUMN_NAME'] . "\n";
}

// Check if profile_photo has the stored value
echo "\n=== profile_photo value for all directors ===\n";
$stmt3 = $pdo->query("SELECT id, profile_photo FROM client_master_directors");
while ($row3 = $stmt3->fetch(PDO::FETCH_ASSOC)) {
    echo "Director ID " . $row3['id'] . ": '" . ($row3['profile_photo'] ?? 'NULL') . "'\n";
}
?>
