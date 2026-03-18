<?php
header('Content-Type: text/plain');
$pdo = new PDO('mysql:host=localhost;dbname=grow_crm_2026', '5fokp_qnbo1', '4P9716bzm7598A');

// Check latest docs
$stmt = $pdo->query("SELECT id, file_path, file_original_name, doc_group, created_at FROM cims_documents ORDER BY id DESC LIMIT 5");
echo "LATEST DOCS:\n";
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $d) {
    echo "id={$d['id']} group={$d['doc_group']} original={$d['file_original_name']} created={$d['created_at']}\n";
    $fp = $d['file_path'];
    $loc1 = '/usr/www/users/smartucbmh/application/storage/app/public/' . $fp;
    $loc2 = '/usr/www/users/smartucbmh/storage/' . $fp;
    echo "  app_storage: " . (file_exists($loc1) ? "EXISTS" : "MISSING") . "\n";
    echo "  root_storage: " . (file_exists($loc2) ? "EXISTS" : "MISSING") . "\n";
    echo "---\n";
}

// Check bank records
$stmt2 = $pdo->query("SELECT id, bank_name, document_id, confirmation_of_banking_uploaded FROM client_master_banks WHERE deleted_at IS NULL ORDER BY id DESC LIMIT 5");
echo "\nBANK RECORDS:\n";
foreach ($stmt2->fetchAll(PDO::FETCH_ASSOC) as $b) {
    echo "id={$b['id']} bank={$b['bank_name']} doc_id={$b['document_id']} uploaded={$b['confirmation_of_banking_uploaded']}\n";
}
