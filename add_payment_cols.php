<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=grow_crm_2026', '5fokp_qnbo1', '4P9716bzm7598A');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $cols = [
        "ADD COLUMN IF NOT EXISTS `payment_date` DATE NULL DEFAULT NULL",
        "ADD COLUMN IF NOT EXISTS `payment_method` VARCHAR(50) NULL DEFAULT NULL",
        "ADD COLUMN IF NOT EXISTS `amount_paid` DECIMAL(15,2) NULL DEFAULT 0.00",
        "ADD COLUMN IF NOT EXISTS `payment_ref_no` VARCHAR(255) NULL DEFAULT NULL",
        "ADD COLUMN IF NOT EXISTS `file_proof_of_payment` VARCHAR(500) NULL DEFAULT NULL",
        "ADD COLUMN IF NOT EXISTS `payment_notes` TEXT NULL DEFAULT NULL",
    ];
    
    foreach ($cols as $col) {
        try {
            $pdo->exec("ALTER TABLE cims_emp201_declarations " . $col);
            echo "OK: " . $col . "\n";
        } catch (Exception $e) {
            // Column might already exist
            echo "SKIP: " . $e->getMessage() . "\n";
        }
    }
    
    echo "\nDone!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
