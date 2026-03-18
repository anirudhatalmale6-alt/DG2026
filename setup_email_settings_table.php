<?php
// Setup script: Create cims_email_settings table
// Run once via browser then delete

$host = 'localhost';
$db   = 'grow_crm_2026';
$user = '5fokp_qnbo1';
$pass = '4P9716bzm7598A';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create cims_email_settings table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS cims_email_settings (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            setting_key VARCHAR(100) NOT NULL UNIQUE,
            setting_value TEXT NULL,
            created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    echo "Table cims_email_settings created.<br>";

    // Insert default settings if not exist
    $defaults = [
        'smtp_host' => '',
        'smtp_port' => '587',
        'smtp_encryption' => 'tls',
        'smtp_username' => '',
        'smtp_password' => '',
        'from_email' => '',
        'from_name' => 'SmartWeigh CIMS',
    ];

    $stmt = $pdo->prepare("INSERT IGNORE INTO cims_email_settings (setting_key, setting_value) VALUES (:k, :v)");
    foreach ($defaults as $k => $v) {
        $stmt->execute(['k' => $k, 'v' => $v]);
    }
    echo "Default settings inserted.<br>";

    echo "<br>Done! Delete this file now.";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
