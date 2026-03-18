<?php
header('Content-Type: text/plain');
$pdo = new PDO('mysql:host=localhost;dbname=grow_crm_2026', '5fokp_qnbo1', '4P9716bzm7598A');

// Check what's stored in client_master_banks
$stmt = $pdo->query("SELECT id, bank_name, bank_statement_frequency_id, bank_statement_frequency_name, bank_statement_cut_off_date, bank_account_status_id, bank_account_status_name, deleted_at FROM client_master_banks ORDER BY id DESC LIMIT 5");
$banks = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "BANK RECORDS:\n";
foreach ($banks as $b) {
    echo "id={$b['id']} bank={$b['bank_name']}\n";
    echo "  freq_id={$b['bank_statement_frequency_id']} freq_name={$b['bank_statement_frequency_name']}\n";
    echo "  cutoff={$b['bank_statement_cut_off_date']}\n";
    echo "  status_id={$b['bank_account_status_id']} status_name={$b['bank_account_status_name']}\n";
    echo "  deleted={$b['deleted_at']}\n";
    echo "---\n";
}

// Check columns exist
$stmt2 = $pdo->query("SHOW COLUMNS FROM client_master_banks");
$cols = $stmt2->fetchAll(PDO::FETCH_ASSOC);
echo "\nTABLE COLUMNS:\n";
foreach ($cols as $c) {
    echo "  {$c['Field']} ({$c['Type']}) null={$c['Null']} default={$c['Default']}\n";
}
