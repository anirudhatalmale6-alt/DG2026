<?php
/**
 * CIMS Job Cards — Alter assigned_to column to support multi-select (comma-separated IDs)
 * Run once via browser: https://smartweigh.co.za/run_jobcards_alter_assigned.php
 * DELETE this file from public_html after running.
 */

// Bootstrap Laravel
require __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "<h2>CIMS Job Cards — Alter assigned_to Column</h2><pre>\n";

try {
    // Check current column type
    $columns = DB::select("SHOW COLUMNS FROM cims_job_cards WHERE Field = 'assigned_to'");
    if (empty($columns)) {
        echo "ERROR: assigned_to column not found in cims_job_cards table.\n";
        exit;
    }

    $currentType = strtolower($columns[0]->Type);
    echo "Current column type: {$currentType}\n";

    if (strpos($currentType, 'varchar') !== false) {
        echo "Column is already VARCHAR — no change needed.\n";
    } else {
        // Alter from BIGINT to VARCHAR(255) to store comma-separated user IDs
        DB::statement("ALTER TABLE cims_job_cards MODIFY COLUMN assigned_to VARCHAR(255) NULL COMMENT 'Comma-separated user IDs for multi-assign'");
        echo "SUCCESS: assigned_to column altered to VARCHAR(255).\n";

        // Verify
        $columns = DB::select("SHOW COLUMNS FROM cims_job_cards WHERE Field = 'assigned_to'");
        echo "New column type: {$columns[0]->Type}\n";
    }

    echo "\nDone! Please DELETE this file from public_html.\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

echo "</pre>";
