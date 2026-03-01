<?php
// Migration: Create cims_master_contacts table
// Run via: curl https://smartweigh.co.za/create_contacts_table_v1.php

$host = 'localhost';
$db   = 'grow_crm_2026';
$user = '5fokp_qnbo1';
$pass = '4P9716bzm7598A';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create cims_master_contacts table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `cims_master_contacts` (
            `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            `client_id` BIGINT UNSIGNED NOT NULL,
            `source` VARCHAR(50) DEFAULT 'manual' COMMENT 'manual, director, client_master',
            `source_id` BIGINT UNSIGNED DEFAULT NULL COMMENT 'ID from source table for sync',
            `title` VARCHAR(20) DEFAULT NULL COMMENT 'Mr, Mrs, Ms, Dr, Prof, Adv',
            `first_name` VARCHAR(100) NOT NULL,
            `last_name` VARCHAR(100) NOT NULL,
            `known_as` VARCHAR(100) DEFAULT NULL COMMENT 'Friendly name e.g. Angus',
            `gender` ENUM('Male','Female','Other') DEFAULT NULL,
            `email` VARCHAR(255) DEFAULT NULL,
            `phone` VARCHAR(50) DEFAULT NULL,
            `mobile` VARCHAR(50) DEFAULT NULL,
            `whatsapp` VARCHAR(50) DEFAULT NULL,
            `position` VARCHAR(150) DEFAULT NULL COMMENT 'Job title / designation',
            `department` VARCHAR(150) DEFAULT NULL,
            `photo` VARCHAR(500) DEFAULT NULL COMMENT 'Path to profile photo',
            `is_primary` TINYINT(1) DEFAULT 0,
            `is_active` TINYINT(1) DEFAULT 1,
            `notes` TEXT DEFAULT NULL,
            `created_by` BIGINT UNSIGNED DEFAULT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            INDEX `idx_client_id` (`client_id`),
            INDEX `idx_email` (`email`),
            INDEX `idx_source` (`source`, `source_id`),
            INDEX `idx_active` (`is_active`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    echo "Table cims_master_contacts created.\n";

    // Seed contacts from client_master_directors (existing directors)
    $stmt = $pdo->query("
        SELECT d.id, d.client_id, d.title, d.firstname, d.middlename, d.surname,
               d.gender, d.email, d.office_phone, d.mobile_phone, d.whatsapp_number,
               d.director_type_name, d.profile_photo, d.created_by, d.created_at
        FROM client_master_directors d
        WHERE d.is_active = 1
    ");
    $directors = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $inserted = 0;
    $skipped = 0;
    foreach ($directors as $d) {
        // Check if already synced
        $check = $pdo->prepare("SELECT id FROM cims_master_contacts WHERE source = 'director' AND source_id = ?");
        $check->execute([$d['id']]);
        if ($check->fetch()) {
            $skipped++;
            continue;
        }

        $firstName = trim($d['firstname'] ?? '');
        $lastName = trim($d['surname'] ?? '');
        if (empty($firstName) && empty($lastName)) {
            $skipped++;
            continue;
        }

        $ins = $pdo->prepare("
            INSERT INTO cims_master_contacts
            (client_id, source, source_id, title, first_name, last_name, known_as, gender, email, phone, mobile, whatsapp, position, photo, is_primary, is_active, created_by, created_at)
            VALUES (?, 'director', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 1, ?, ?)
        ");
        $ins->execute([
            $d['client_id'],
            $d['id'],
            $d['title'] ?? null,
            $firstName,
            $lastName,
            null, // known_as - user can set later
            $d['gender'] ?? null,
            $d['email'] ?? null,
            $d['office_phone'] ?? null,
            $d['mobile_phone'] ?? null,
            $d['whatsapp_number'] ?? null,
            $d['director_type_name'] ?? null,
            $d['profile_photo'] ?? null,
            $d['created_by'] ?? null,
            $d['created_at'] ?? date('Y-m-d H:i:s'),
        ]);
        $inserted++;
    }

    echo "Seeded $inserted contacts from directors (skipped $skipped).\n";

    // Also seed primary contacts from client_master itself
    $stmt2 = $pdo->query("
        SELECT client_id, company_name, director_first_name, director_surname, email, phone_business, phone_mobile, phone_whatsapp
        FROM client_master
        WHERE is_active = 1 AND (email IS NOT NULL AND email != '')
    ");
    $clients = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    $cmInserted = 0;
    foreach ($clients as $c) {
        // Check if this email already exists for this client
        $check2 = $pdo->prepare("SELECT id FROM cims_master_contacts WHERE client_id = ? AND email = ? AND source = 'client_master'");
        $check2->execute([$c['client_id'], $c['email']]);
        if ($check2->fetch()) continue;

        $fn = trim($c['director_first_name'] ?? '');
        $ln = trim($c['director_surname'] ?? '');
        if (empty($fn) && empty($ln)) {
            $fn = $c['company_name'] ?? 'Primary';
            $ln = 'Contact';
        }

        $ins2 = $pdo->prepare("
            INSERT INTO cims_master_contacts
            (client_id, source, source_id, first_name, last_name, email, phone, mobile, whatsapp, position, is_primary, is_active, created_at)
            VALUES (?, 'client_master', ?, ?, ?, ?, ?, ?, ?, 'Primary Contact', 1, 1, NOW())
        ");
        $ins2->execute([
            $c['client_id'],
            $c['client_id'],
            $fn,
            $ln,
            $c['email'],
            $c['phone_business'] ?? null,
            $c['phone_mobile'] ?? null,
            $c['phone_whatsapp'] ?? null,
        ]);
        $cmInserted++;
    }

    echo "Seeded $cmInserted primary contacts from client_master.\n";

    // Count totals
    $total = $pdo->query("SELECT COUNT(*) FROM cims_master_contacts")->fetchColumn();
    echo "Total contacts in table: $total\n";
    echo "\nDONE - Migration complete!\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
