<?php
// Setup script: Create cims_email_signatures table
$host = 'localhost';
$db   = 'grow_crm_2026';
$user = '5fokp_qnbo1';
$pass = '4P9716bzm7598A';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS cims_email_signatures (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id INT UNSIGNED NOT NULL,
            full_name VARCHAR(200) NOT NULL DEFAULT '',
            designation VARCHAR(200) NOT NULL DEFAULT '',
            phone VARCHAR(50) NOT NULL DEFAULT '',
            mobile VARCHAR(50) NOT NULL DEFAULT '',
            company_name VARCHAR(200) NOT NULL DEFAULT '',
            company_website VARCHAR(300) NOT NULL DEFAULT '',
            signature_html TEXT NULL,
            is_active TINYINT(1) NOT NULL DEFAULT 1,
            created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY unique_user (user_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    echo "Table cims_email_signatures created.<br>";
    echo "Done! Delete this file now.";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
