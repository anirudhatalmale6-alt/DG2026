<?php
$pdo = new PDO("mysql:host=localhost;dbname=grow_crm_2026;port=3306", "5fokp_qnbo1", "4P9716bzm7598A");

// Check what the actual URL should be
echo "=== Route Check ===\n";
echo "Module route prefix: cims/pm\n";
echo "Director update route: cims/pm/ajax/directors/{directorId}\n";
echo "JS constructs URL as: {appBasePath}/ajax/directors/{directorId}\n";
echo "appBasePath on this server: '' (empty - APP_URL has no path)\n";
echo "So JS calls: /ajax/directors/{id} - MISSING 'cims/pm' prefix!\n";

// Check existing directors to confirm IDs
echo "\n=== Existing Directors ===\n";
$stmt = $pdo->query("SELECT id, client_id, person_id, firstname, surname, director_type_name, director_status_name FROM client_master_directors");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "ID: " . $row['id'] . " | client_id: " . $row['client_id'] . " | " . $row['firstname'] . " " . $row['surname'] . " | type: " . $row['director_type_name'] . " | status: " . $row['director_status_name'] . "\n";
}
?>
