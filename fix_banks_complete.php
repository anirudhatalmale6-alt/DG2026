<?php
/**
 * fix_banks_complete.php
 *
 * Creates the cims_bank_account_status table (if it does not already exist)
 * and seeds it with the initial reference data.
 *
 * Usage:  php fix_banks_complete.php
 */

// -------------------------------------------------------
// 1. Bootstrap Laravel (same approach as diagnose.php)
// -------------------------------------------------------
$basePaths = [__DIR__ . '/../application', __DIR__ . '/../../application', __DIR__ . '/..'];
$bootstrapped = false;
foreach ($basePaths as $base) {
    if (file_exists($base . '/bootstrap/app.php')) {
        if (file_exists($base . '/bootstrap/autoload.php')) require $base . '/bootstrap/autoload.php';
        elseif (file_exists($base . '/vendor/autoload.php')) require $base . '/vendor/autoload.php';
        $app = require_once $base . '/bootstrap/app.php';
        $bootstrapped = true;
        break;
    }
}
if (!$bootstrapped) die("Could not find Laravel bootstrap.");
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "Laravel bootstrapped successfully.\n\n";

// -------------------------------------------------------
// 2. Create cims_bank_account_status table if not exists
// -------------------------------------------------------
$tableName = 'cims_bank_account_status';

if (Schema::hasTable($tableName)) {
    echo "Table '{$tableName}' already exists. Skipping creation.\n";
} else {
    Schema::create($tableName, function ($table) {
        $table->bigIncrements('id');
        $table->string('bank_account_status', 255);
        $table->integer('bank_link_id');
        $table->string('bank_name', 80);
        $table->tinyInteger('is_active')->default(1);
        $table->text('tooltip')->nullable();
        $table->timestamps();
    });
    echo "Table '{$tableName}' created successfully.\n";
}

// -------------------------------------------------------
// 3. Seed reference data
// -------------------------------------------------------
$seedData = [
    [
        'id'                  => 1,
        'bank_account_status' => 'Active / Open',
        'bank_link_id'        => 3,
        'bank_name'           => 'First National Bank',
        'is_active'           => 1,
        'tooltip'             => 'The account is ready for normal use.',
    ],
    [
        'id'                  => 2,
        'bank_account_status' => 'Inactive',
        'bank_link_id'        => 3,
        'bank_name'           => 'First National Bank',
        'is_active'           => 1,
        'tooltip'             => 'No transactions for a defined period.',
    ],
    [
        'id'                  => 3,
        'bank_account_status' => 'Dormant',
        'bank_link_id'        => 3,
        'bank_name'           => 'First National Bank',
        'is_active'           => 1,
        'tooltip'             => 'Inactive for extended period.',
    ],
    [
        'id'                  => 4,
        'bank_account_status' => 'Closed',
        'bank_link_id'        => 3,
        'bank_name'           => 'First National Bank',
        'is_active'           => 1,
        'tooltip'             => 'Account formally terminated.',
    ],
    [
        'id'                  => 5,
        'bank_account_status' => 'Frozen / Restricted / Suspended',
        'bank_link_id'        => 3,
        'bank_name'           => 'First National Bank',
        'is_active'           => 1,
        'tooltip'             => 'Activity restricted.',
    ],
    [
        'id'                  => 6,
        'bank_account_status' => 'Active / Open',
        'bank_link_id'        => 1,
        'bank_name'           => 'ABSA Bank',
        'is_active'           => 1,
        'tooltip'             => 'The account is ready for normal use.',
    ],
    [
        'id'                  => 7,
        'bank_account_status' => 'Active / Open',
        'bank_link_id'        => 2,
        'bank_name'           => 'Capitec Bank',
        'is_active'           => 1,
        'tooltip'             => 'The account is ready for normal use.',
    ],
];

$inserted = 0;
$skipped  = 0;

foreach ($seedData as $row) {
    $exists = DB::table($tableName)->where('id', $row['id'])->exists();

    if ($exists) {
        echo "  [SKIP] Row id={$row['id']} ('{$row['bank_account_status']}' / {$row['bank_name']}) already exists.\n";
        $skipped++;
    } else {
        DB::table($tableName)->insert(array_merge($row, [
            'created_at' => now(),
            'updated_at' => now(),
        ]));
        echo "  [INSERT] Row id={$row['id']} ('{$row['bank_account_status']}' / {$row['bank_name']}) inserted.\n";
        $inserted++;
    }
}

// -------------------------------------------------------
// 4. Output results
// -------------------------------------------------------
echo "\n--- Summary ---\n";
echo "Table:    {$tableName}\n";
echo "Inserted: {$inserted}\n";
echo "Skipped:  {$skipped}\n";
echo "Total:    " . ($inserted + $skipped) . "\n";

$totalRows = DB::table($tableName)->count();
echo "Rows in table now: {$totalRows}\n";
echo "Done.\n";
