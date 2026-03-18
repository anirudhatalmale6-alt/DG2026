<?php
$db = new mysqli('localhost', '5fokp_qnbo1', '4P9716bzm7598A', 'grow_crm_2026');
if ($db->connect_error) die("DB Error: " . $db->connect_error);
$r = $db->query("DESCRIBE client_master_banks");
while ($row = $r->fetch_assoc()) echo "{$row['Field']} ({$row['Type']}) {$row['Null']} {$row['Default']}\n";
$db->close();
?>
