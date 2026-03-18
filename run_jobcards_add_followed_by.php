<?php
/**
 * CIMS Job Cards — Add followed_by column + revert assigned_to to BIGINT
 * Run once via browser: https://smartweigh.co.za/run_jobcards_add_followed_by.php
 * DELETE this file from public_html after running.
 */

require __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "<h2>CIMS Job Cards — Add followed_by + Fix assigned_to</h2><pre>\n";

try {
    // 1. Revert assigned_to to BIGINT if it was changed to VARCHAR
    $columns = DB::select("SHOW COLUMNS FROM cims_job_cards WHERE Field = 'assigned_to'");
    if (!empty($columns)) {
        $currentType = strtolower($columns[0]->Type);
        echo "assigned_to current type: {$currentType}\n";
        if (strpos($currentType, 'varchar') !== false) {
            DB::statement("ALTER TABLE cims_job_cards MODIFY COLUMN assigned_to BIGINT UNSIGNED NULL COMMENT 'FK to users table'");
            echo "  -> Reverted to BIGINT UNSIGNED.\n";
        } else {
            echo "  -> Already BIGINT, no change needed.\n";
        }
    }

    // 2. Add followed_by column if it doesn't exist
    $hasFB = DB::select("SHOW COLUMNS FROM cims_job_cards WHERE Field = 'followed_by'");
    if (empty($hasFB)) {
        DB::statement("ALTER TABLE cims_job_cards ADD COLUMN followed_by BIGINT UNSIGNED NULL COMMENT 'FK to users table - follower' AFTER assigned_to");
        DB::statement("ALTER TABLE cims_job_cards ADD INDEX idx_job_cards_followed (followed_by)");
        echo "SUCCESS: followed_by column added.\n";
    } else {
        echo "followed_by column already exists, skipping.\n";
    }

    echo "\nDone! Please DELETE this file from public_html.\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

echo "</pre>";
