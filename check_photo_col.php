<?php
// Quick check for photo column in cims_master_contacts
try {
    $pdo = new PDO('mysql:host=localhost;dbname=grow_crm_2026', '5fokp_qnbo1', '4P9716bzm7598A');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query("SHOW COLUMNS FROM cims_master_contacts LIKE 'photo'");
    $col = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($col) {
        echo "PHOTO COLUMN EXISTS: " . json_encode($col);
    } else {
        echo "PHOTO COLUMN DOES NOT EXIST";
    }

    // Also check storage directory
    $storagePath = dirname(__DIR__) . '/storage/contact_photos';
    echo "\nSTORAGE PATH: " . $storagePath;
    echo "\nSTORAGE EXISTS: " . (is_dir($storagePath) ? 'YES' : 'NO');

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
