<?php
// Bootstrap Laravel
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

echo "<pre>\n=== Seeding Remaining Tables ===\n\n";

// First check actual columns on cims_bank_names
$columns = Schema::getColumnListing('cims_bank_names');
echo "cims_bank_names columns: " . implode(', ', $columns) . "\n\n";

// cims_bank_names (without tooltip if it doesn't exist)
if (DB::table('cims_bank_names')->count() == 0) {
    $hasTooltip = in_array('tooltip', $columns);

    $banks = [
        ['id' => 1, 'bank_name' => 'ABSA', 'branch_name' => 'ABSA Universal', 'branch_code' => '632005', 'swift_code' => 'ABSAZAJJ', 'bank_logo' => '/bank_logo/absa_logo.jpg', 'is_active' => 1],
        ['id' => 2, 'bank_name' => 'Capitec Bank', 'branch_name' => 'Capitec Main', 'branch_code' => '470010', 'swift_code' => 'CABORAJJ', 'bank_logo' => '/bank_logo/capitec_bank.jpg', 'is_active' => 1],
        ['id' => 3, 'bank_name' => 'First National Bank', 'branch_name' => 'FNB Universal', 'branch_code' => '250655', 'swift_code' => 'FIRNZAJJ', 'bank_logo' => '/bank_logo/fnb_logo.jpg', 'is_active' => 1],
        ['id' => 4, 'bank_name' => 'Investec', 'branch_name' => 'Investec Private', 'branch_code' => '580105', 'swift_code' => 'INVLZAJJ', 'bank_logo' => '/bank_logo/investec.png', 'is_active' => 0],
        ['id' => 5, 'bank_name' => 'Nedbank', 'branch_name' => 'Nedbank Universal', 'branch_code' => '198765', 'swift_code' => 'NEDSZAJJ', 'bank_logo' => '/bank_logo/nedbank_logo.jpg', 'is_active' => 1],
        ['id' => 6, 'bank_name' => 'Standard Bank', 'branch_name' => 'Standard Universal', 'branch_code' => '051001', 'swift_code' => 'SBZAZAJJ', 'bank_logo' => '/bank_logo/stb_bank_logo.jpg', 'is_active' => 1],
        ['id' => 7, 'bank_name' => 'African Bank', 'branch_name' => 'African Bank', 'branch_code' => '430000', 'swift_code' => 'AABORAJJ', 'bank_logo' => '/bank_logo/african.png', 'is_active' => 0],
        ['id' => 8, 'bank_name' => 'Bidvest Bank', 'branch_name' => 'Bidvest', 'branch_code' => '462005', 'swift_code' => 'BIDVZAJJ', 'bank_logo' => '/bank_logo/Bidvest-Logo.png', 'is_active' => 0],
        ['id' => 9, 'bank_name' => 'Discovery Bank', 'branch_name' => 'Discovery', 'branch_code' => '679000', 'swift_code' => 'DISCZAJJ', 'bank_logo' => '/bank_logo/dicovery.png', 'is_active' => 0],
        ['id' => 10, 'bank_name' => 'TymeBank', 'branch_name' => 'TymeBank Digital', 'branch_code' => '678910', 'swift_code' => 'TABORAJJ', 'bank_logo' => '/bank_logo/other_logo.jpg', 'is_active' => 1],
    ];

    DB::table('cims_bank_names')->insert($banks);
    echo "SEEDED: cims_bank_names (10 rows)\n";
} else {
    echo "EXISTS: cims_bank_names (" . DB::table('cims_bank_names')->count() . " rows)\n";
}

// cims_address_types
if (DB::table('cims_address_types')->count() == 0) {
    DB::table('cims_address_types')->insert([
        ['id' => 1, 'name' => 'Registered Address', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
        ['id' => 2, 'name' => 'Business Address', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
        ['id' => 3, 'name' => 'Postal Address', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
        ['id' => 4, 'name' => 'Trading Address', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
    ]);
    echo "SEEDED: cims_address_types (4 rows)\n";
} else {
    echo "EXISTS: cims_address_types (" . DB::table('cims_address_types')->count() . " rows)\n";
}

// Summary of ALL tables
echo "\n--- Final Summary ---\n";
$allTables = ['company_types', 'client_titles', 'client_positions', 'share_types', 'cims_vat_cycles', 'cims_director_types', 'cims_director_status', 'cims_bank_names', 'cims_bank_account_types', 'cims_address_types'];
foreach ($allTables as $t) {
    if (Schema::hasTable($t)) {
        echo "  $t: " . DB::table($t)->count() . " rows\n";
    } else {
        echo "  $t: TABLE MISSING!\n";
    }
}

echo "\n=== DONE - DELETE THIS FILE ===\n</pre>";
