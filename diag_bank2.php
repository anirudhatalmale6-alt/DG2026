<?php
header('Content-Type: text/plain');
$pdo = new PDO('mysql:host=localhost;dbname=grow_crm_2026', '5fokp_qnbo1', '4P9716bzm7598A');

// All bank records
$stmt = $pdo->query("SELECT id, bank_name, document_id, confirmation_of_banking_uploaded, created_at, deleted_at FROM client_master_banks ORDER BY id DESC LIMIT 10");
echo "ALL BANK RECORDS:\n";
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $b) {
    echo "id={$b['id']} bank={$b['bank_name']} doc_id={$b['document_id']} uploaded={$b['confirmation_of_banking_uploaded']} created={$b['created_at']} del={$b['deleted_at']}\n";
}

// All banking documents
$stmt2 = $pdo->query("SELECT id, file_path, file_original_name, doc_group, created_at FROM cims_documents WHERE doc_group='BANKING' ORDER BY id DESC LIMIT 10");
echo "\nBANKING DOCUMENTS:\n";
foreach ($stmt2->fetchAll(PDO::FETCH_ASSOC) as $d) {
    echo "id={$d['id']} original={$d['file_original_name']} created={$d['created_at']}\n";
    echo "  path={$d['file_path']}\n";
    $fp = $d['file_path'];
    echo "  app_storage: " . (file_exists('/usr/www/users/smartucbmh/application/storage/app/public/' . $fp) ? "EXISTS" : "MISSING") . "\n";
    echo "  root_storage: " . (file_exists('/usr/www/users/smartucbmh/storage/' . $fp) ? "EXISTS" : "MISSING") . "\n";
    echo "---\n";
}

// Check Laravel log for bank upload errors
$log = '/usr/www/users/smartucbmh/application/storage/logs/laravel.log';
if (file_exists($log)) {
    $lines = file($log);
    $last = array_slice($lines, -100);
    $relevant = array_filter($last, function($l) { return stripos($l, 'bank') !== false || stripos($l, 'ERROR') !== false || stripos($l, 'confirmation') !== false; });
    if ($relevant) {
        echo "\nRELEVANT LOG ENTRIES:\n";
        foreach ($relevant as $l) echo trim($l) . "\n";
    } else {
        echo "\nNo bank-related errors in recent log.\n";
    }
}
