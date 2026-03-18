<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$db = new mysqli('localhost', '5fokp_qnbo1', '4P9716bzm7598A', 'grow_crm_2026');
if ($db->connect_error) die("DB Error: " . $db->connect_error);

echo "cims_documents columns:\n";
$r = $db->query("DESCRIBE cims_documents");
while ($row = $r->fetch_assoc()) {
    echo "  {$row['Field']} ({$row['Type']})\n";
}

echo "\nRecent cims_documents:\n";
$r = $db->query("SELECT * FROM cims_documents ORDER BY id DESC LIMIT 3");
while ($row = $r->fetch_assoc()) {
    foreach ($row as $k => $v) echo "  $k=$v | ";
    echo "\n---\n";
}

echo "\nClient Master Banks:\n";
$r = $db->query("SELECT * FROM client_master_banks ORDER BY id DESC LIMIT 3");
while ($row = $r->fetch_assoc()) {
    foreach ($row as $k => $v) if ($v) echo "  $k=$v | ";
    echo "\n---\n";
}

echo "\nDocument ID 70 exists: ";
$r = $db->query("SELECT COUNT(*) as cnt FROM cims_documents WHERE id = 70");
$row = $r->fetch_assoc();
echo $row['cnt'] . "\n";

echo "\nMax document ID: ";
$r = $db->query("SELECT MAX(id) as maxid FROM cims_documents");
$row = $r->fetch_assoc();
echo $row['maxid'] . "\n";

$db->close();
?>
