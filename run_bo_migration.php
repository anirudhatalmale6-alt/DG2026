<?php
/**
 * Beneficial Ownership Migration
 * 1. Create cims_share_certificates table
 * 2. Add director_id + document_category to cims_job_card_attachments
 * 3. Add poa_uploaded_at to cims_persons (if missing)
 *
 * DELETE after running.
 */
require __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "<h2>Beneficial Ownership Migration</h2><pre>\n";

// 1. Create cims_share_certificates
echo "=== 1. cims_share_certificates ===\n";
if (Schema::hasTable('cims_share_certificates')) {
    echo "  Table already exists — skipping.\n";
} else {
    try {
        DB::statement("
            CREATE TABLE cims_share_certificates (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                client_id BIGINT UNSIGNED NOT NULL COMMENT 'FK to client_master.client_id',
                director_id BIGINT UNSIGNED NULL COMMENT 'FK to client_master_directors.id',
                person_id BIGINT UNSIGNED NULL COMMENT 'FK to cims_persons.id',
                certificate_number VARCHAR(50) NOT NULL COMMENT 'Sequential per company e.g. SC-001',
                share_class VARCHAR(100) DEFAULT 'Ordinary Shares',
                number_of_shares INT UNSIGNED NOT NULL DEFAULT 0,
                percentage DECIMAL(8,4) DEFAULT 0,
                date_issued DATE NULL,
                date_ceased DATE NULL,
                is_active TINYINT(1) DEFAULT 1,
                notes TEXT NULL,
                created_by BIGINT UNSIGNED NULL,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL,
                INDEX idx_sc_client (client_id),
                INDEX idx_sc_director (director_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "  Created successfully.\n";
    } catch (\Exception $e) {
        echo "  ERROR: " . $e->getMessage() . "\n";
    }
}

// 2. Add columns to cims_job_card_attachments
echo "\n=== 2. cims_job_card_attachments — add director_id, document_category ===\n";
try {
    if (!Schema::hasColumn('cims_job_card_attachments', 'director_id')) {
        DB::statement("ALTER TABLE cims_job_card_attachments ADD COLUMN director_id BIGINT UNSIGNED NULL AFTER uploaded_by");
        echo "  Added director_id.\n";
    } else {
        echo "  director_id already exists.\n";
    }

    if (!Schema::hasColumn('cims_job_card_attachments', 'document_category')) {
        DB::statement("ALTER TABLE cims_job_card_attachments ADD COLUMN document_category VARCHAR(50) NULL AFTER director_id");
        echo "  Added document_category.\n";
    } else {
        echo "  document_category already exists.\n";
    }
} catch (\Exception $e) {
    echo "  ERROR: " . $e->getMessage() . "\n";
}

// 3. Add poa_uploaded_at to cims_persons if missing
echo "\n=== 3. cims_persons — add poa_uploaded_at ===\n";
try {
    if (!Schema::hasColumn('cims_persons', 'poa_uploaded_at')) {
        DB::statement("ALTER TABLE cims_persons ADD COLUMN poa_uploaded_at TIMESTAMP NULL AFTER poa_image");
        echo "  Added poa_uploaded_at.\n";
    } else {
        echo "  poa_uploaded_at already exists.\n";
    }
} catch (\Exception $e) {
    echo "  ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== DONE ===\n";
echo "</pre>";
