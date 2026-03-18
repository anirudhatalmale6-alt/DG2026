<?php
/**
 * One-time migration: Add show_in_conversion to cims_bank_names table
 * and set it to 1 for the 5 conversion banks.
 * DELETE THIS FILE AFTER RUNNING.
 */

require __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "<pre>\n";
echo "=== ADD show_in_conversion COLUMN ===\n\n";

try {
    // Check if column exists
    if (Schema::hasColumn('cims_bank_names', 'show_in_conversion')) {
        echo "Column 'show_in_conversion' already exists.\n";
    } else {
        Schema::table('cims_bank_names', function ($table) {
            $table->boolean('show_in_conversion')->default(false)->after('is_active');
        });
        echo "Column 'show_in_conversion' added successfully.\n";
    }

    // Set show_in_conversion = 1 for the 5 conversion banks
    // ABSA (1), Capitec (2), FNB (3), Nedbank (5), Standard Bank (6)
    $updated = DB::table('cims_bank_names')
        ->whereIn('id', [1, 2, 3, 5, 6])
        ->update(['show_in_conversion' => 1]);
    echo "Updated {$updated} banks with show_in_conversion = 1\n";

    // Verify
    $banks = DB::table('cims_bank_names')
        ->where('show_in_conversion', 1)
        ->get(['id', 'bank_name', 'bank_logo', 'show_in_conversion']);
    echo "\nBanks enabled for conversion:\n";
    foreach ($banks as $b) {
        echo "  ID {$b->id}: {$b->bank_name} (logo: {$b->bank_logo})\n";
    }

    echo "\nDone -- DELETE this file!\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
echo "</pre>";
