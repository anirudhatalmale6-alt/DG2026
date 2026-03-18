<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=grow_crm_2026', '5fokp_qnbo1', '4P9716bzm7598A');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    try {
        $pdo->exec("ALTER TABLE cims_emp201_declarations ADD COLUMN IF NOT EXISTS `declaration_date` DATE NULL DEFAULT NULL");
        echo "OK: declaration_date added\n";
    } catch (Exception $e) {
        echo "SKIP: " . $e->getMessage() . "\n";
    }
    try {
        $pdo->exec("ALTER TABLE cims_emp201_declarations ADD COLUMN IF NOT EXISTS `prepared_by` VARCHAR(255) NULL DEFAULT NULL");
        echo "OK: prepared_by added\n";
    } catch (Exception $e) {
        echo "SKIP: " . $e->getMessage() . "\n";
    }
    echo "Done!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
