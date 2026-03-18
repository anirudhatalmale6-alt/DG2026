<?php
$db = new mysqli('localhost', '5fokp_qnbo1', '4P9716bzm7598A', 'grow_crm_2026');
if ($db->connect_error) die("DB Error: " . $db->connect_error);

echo "<h3>cims_bank_names</h3><pre>";
$r = $db->query("SELECT id, bank_name, branch_name, branch_code, swift_code, bank_logo, is_active FROM cims_bank_names ORDER BY id");
while ($row = $r->fetch_assoc()) {
    echo json_encode($row) . "\n";
}
echo "</pre>";
$db->close();
?>
