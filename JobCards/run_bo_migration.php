<?php
/**
 * BO Migration Runner — Adds columns to cims_job_card_attachments
 * Access: https://cimsolutions.co.za/application/Modules/JobCards/run_bo_migration.php
 * DELETE THIS FILE AFTER RUNNING
 */

// Bootstrap Laravel
require_once __DIR__ . '/../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle($request = Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

header('Content-Type: text/html; charset=utf-8');
echo '<h2>BO Migration Runner</h2><pre style="font-family:monospace;font-size:13px;">';

try {
    // Step 1: Expand file_type ENUM
    echo "Step 1: Expanding file_type ENUM...\n";
    DB::statement("ALTER TABLE `cims_job_card_attachments` MODIFY COLUMN `file_type` ENUM('source_document','internal_pack','external_pack','bo_document','id_document','poa_document','other') NOT NULL DEFAULT 'source_document'");
    echo "  ✓ file_type ENUM updated.\n\n";

    // Step 2: Add director_id column (if not exists)
    echo "Step 2: Adding director_id column...\n";
    if (!Schema::hasColumn('cims_job_card_attachments', 'director_id')) {
        DB::statement("ALTER TABLE `cims_job_card_attachments` ADD COLUMN `director_id` BIGINT UNSIGNED NULL AFTER `file_type`, ADD INDEX `idx_attachments_director` (`director_id`)");
        echo "  ✓ director_id column added.\n\n";
    } else {
        echo "  – director_id already exists, skipping.\n\n";
    }

    // Step 3: Add document_category column (if not exists)
    echo "Step 3: Adding document_category column...\n";
    if (!Schema::hasColumn('cims_job_card_attachments', 'document_category')) {
        DB::statement("ALTER TABLE `cims_job_card_attachments` ADD COLUMN `document_category` VARCHAR(100) NULL AFTER `director_id`");
        echo "  ✓ document_category column added.\n\n";
    } else {
        echo "  – document_category already exists, skipping.\n\n";
    }

    echo "\n<strong style='color:green;'>✅ Migration complete! All BO columns are ready.</strong>\n";
    echo "\n<strong style='color:red;'>⚠ DELETE THIS FILE NOW for security.</strong>\n";

} catch (\Exception $e) {
    echo "\n<strong style='color:red;'>ERROR: " . htmlspecialchars($e->getMessage()) . "</strong>\n";
}

echo '</pre>';
