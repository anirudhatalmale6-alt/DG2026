<?php
$pdo = new PDO("mysql:host=localhost;dbname=grow_crm_2026;port=3306", "5fokp_qnbo1", "4P9716bzm7598A");
$stmt = $pdo->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='grow_crm_2026' AND TABLE_NAME='client_master_directors' ORDER BY ORDINAL_POSITION");
$cols = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $cols[] = $row['COLUMN_NAME'];
}
echo implode("\n", $cols);
?>
