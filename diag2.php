<?php
header('Content-Type: text/plain');
$pdo = new PDO('mysql:host=localhost;dbname=grow_crm_2026', '5fokp_qnbo1', '4P9716bzm7598A');

// Get the latest bank-related documents
$stmt = $pdo->query("SELECT d.id, d.file_path, d.file_stored_name, d.doc_group, d.created_at, b.id as bank_record_id
FROM cims_documents d
LEFT JOIN client_master_banks b ON b.document_id = d.id
WHERE d.doc_group = 'BANKING'
ORDER BY d.id DESC LIMIT 5");
$docs = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "BANK DOCS:\n";
foreach ($docs as $doc) {
    echo "docid={$doc['id']} bank_rec={$doc['bank_record_id']} path={$doc['file_path']} created={$doc['created_at']}\n";
    $fp = $doc['file_path'];
    echo "  L:" . (file_exists("/usr/www/users/smartucbmh/public_html/application/storage/app/public/{$fp}") ? "Y" : "N");
    echo " P:" . (file_exists("/usr/www/users/smartucbmh/public_html/application/public/storage/{$fp}") ? "Y" : "N");
    echo " W:" . (file_exists("/usr/www/users/smartucbmh/public_html/storage/{$fp}") ? "Y" : "N");
    echo "\n";
}

// Check client_master_banks for document_id
$stmt2 = $pdo->query("SELECT id, bank_name, document_id, confirmation_of_banking_uploaded, deleted_at FROM client_master_banks ORDER BY id DESC LIMIT 5");
$banks = $stmt2->fetchAll(PDO::FETCH_ASSOC);
echo "\nBANK RECORDS:\n";
foreach ($banks as $b) {
    echo "id={$b['id']} bank={$b['bank_name']} doc_id={$b['document_id']} uploaded={$b['confirmation_of_banking_uploaded']} del={$b['deleted_at']}\n";
}

// Check what directories exist under storage
echo "\nDIR CHECK:\n";
$dirs = [
    '/usr/www/users/smartucbmh/public_html/application/storage/app/public/client_docs/',
    '/usr/www/users/smartucbmh/public_html/storage/client_docs/',
    '/usr/www/users/smartucbmh/public_html/application/public/storage/client_docs/',
];
foreach ($dirs as $d) {
    echo "$d => " . (is_dir($d) ? "EXISTS" : "MISSING") . "\n";
    if (is_dir($d)) {
        $subdirs = glob($d . '*', GLOB_ONLYDIR);
        foreach ($subdirs as $sd) {
            echo "  " . basename($sd) . "/ => " . count(glob($sd . '/*')) . " files\n";
        }
    }
}
