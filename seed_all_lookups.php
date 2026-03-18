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

echo "<pre>\n=== Seeding ALL Empty Lookup Tables ===\n\n";

// 1. client_titles
if (DB::table('client_titles')->count() == 0) {
    DB::table('client_titles')->insert([
        ['id' => 1, 'name' => 'Mr.', 'tooltip' => 'Adult males.', 'created_at' => '2026-02-08 15:48:06', 'updated_at' => '2026-02-08 15:48:06'],
        ['id' => 2, 'name' => 'Ms.', 'tooltip' => 'Women (marital status irrelevant or unknown).', 'created_at' => '2026-02-08 15:48:06', 'updated_at' => '2026-02-08 15:48:06'],
        ['id' => 3, 'name' => 'Mrs.', 'tooltip' => 'Married women.', 'created_at' => '2026-02-08 15:48:06', 'updated_at' => '2026-02-08 15:48:06'],
        ['id' => 4, 'name' => 'Miss', 'tooltip' => 'Unmarried women or girls.', 'created_at' => '2026-02-08 15:48:06', 'updated_at' => '2026-02-08 15:48:06'],
        ['id' => 5, 'name' => 'Mx.', 'tooltip' => 'Gender-neutral title.', 'created_at' => '2026-02-08 15:48:06', 'updated_at' => '2026-02-08 15:48:06'],
        ['id' => 6, 'name' => 'Master', 'tooltip' => 'Boys, usually under 13-18.', 'created_at' => '2026-02-08 15:48:06', 'updated_at' => '2026-02-08 15:48:06'],
        ['id' => 7, 'name' => "Madam / Ma'am", 'tooltip' => 'Formal address for women.', 'created_at' => '2026-02-08 15:48:06', 'updated_at' => '2026-02-08 15:48:06'],
        ['id' => 8, 'name' => 'Sir', 'tooltip' => 'Formal address for men.', 'created_at' => '2026-02-08 15:48:06', 'updated_at' => '2026-02-08 15:48:06'],
    ]);
    echo "SEEDED: client_titles (8 rows)\n";
} else {
    echo "EXISTS: client_titles (" . DB::table('client_titles')->count() . " rows)\n";
}

// 2. client_positions
if (DB::table('client_positions')->count() == 0) {
    DB::table('client_positions')->insert([
        ['id' => 1, 'name' => 'Public Officer', 'tooltip' => null, 'created_at' => '2026-02-08 15:48:05', 'updated_at' => '2026-02-08 15:48:05'],
        ['id' => 2, 'name' => 'Accounting Officers', 'tooltip' => null, 'created_at' => '2026-02-08 15:48:05', 'updated_at' => '2026-02-08 15:48:05'],
        ['id' => 3, 'name' => 'Main Partner', 'tooltip' => null, 'created_at' => '2026-02-08 15:48:05', 'updated_at' => '2026-02-08 15:48:05'],
        ['id' => 4, 'name' => 'Main Trustee', 'tooltip' => null, 'created_at' => '2026-02-08 15:48:05', 'updated_at' => '2026-02-08 15:48:05'],
        ['id' => 5, 'name' => 'Treasurer', 'tooltip' => null, 'created_at' => '2026-02-08 15:48:05', 'updated_at' => '2026-02-08 15:48:05'],
        ['id' => 6, 'name' => 'Administrator', 'tooltip' => null, 'created_at' => '2026-02-08 15:48:05', 'updated_at' => '2026-02-08 15:48:05'],
        ['id' => 7, 'name' => 'Executor / Curator', 'tooltip' => null, 'created_at' => '2026-02-08 15:48:05', 'updated_at' => '2026-02-08 15:48:05'],
    ]);
    echo "SEEDED: client_positions (7 rows)\n";
} else {
    echo "EXISTS: client_positions (" . DB::table('client_positions')->count() . " rows)\n";
}

// 3. share_types
if (DB::table('share_types')->count() == 0) {
    DB::table('share_types')->insert([
        ['id' => 1, 'name' => 'Ordinary Shares', 'tooltip' => 'Standard shares with voting rights and dividend entitlements.', 'created_at' => '2026-02-08 15:48:05', 'updated_at' => '2026-02-08 15:48:05'],
        ['id' => 2, 'name' => 'Preference Shares', 'tooltip' => 'Priority in dividends and liquidation, usually no voting rights.', 'created_at' => '2026-02-08 15:48:05', 'updated_at' => '2026-02-08 15:48:05'],
        ['id' => 3, 'name' => 'Non-Voting Shares', 'tooltip' => 'Shares with economic rights only, no votes.', 'created_at' => '2026-02-08 15:48:05', 'updated_at' => '2026-02-08 15:48:05'],
        ['id' => 4, 'name' => 'Redeemable Shares', 'tooltip' => 'Shares the company can buy back at a later date.', 'created_at' => '2026-02-08 15:48:05', 'updated_at' => '2026-02-08 15:48:05'],
        ['id' => 5, 'name' => 'Convertible Shares', 'tooltip' => 'Shares that can be converted into another class later.', 'created_at' => '2026-02-08 15:48:05', 'updated_at' => '2026-02-08 15:48:05'],
        ['id' => 6, 'name' => 'Deferred Shares', 'tooltip' => 'Shares with delayed rights.', 'created_at' => '2026-02-08 15:48:05', 'updated_at' => '2026-02-08 15:48:05'],
        ['id' => 7, 'name' => 'Par Value Shares', 'tooltip' => 'Shares with a stated face value in the MOI.', 'created_at' => '2026-02-08 15:48:05', 'updated_at' => '2026-02-08 15:48:05'],
    ]);
    echo "SEEDED: share_types (7 rows)\n";
} else {
    echo "EXISTS: share_types (" . DB::table('share_types')->count() . " rows)\n";
}

// 4. cims_vat_cycles
if (DB::table('cims_vat_cycles')->count() == 0) {
    DB::table('cims_vat_cycles')->insert([
        ['id' => 1, 'name' => 'Category A (Odd Months) Jan Mar May Jul Sep and Nov', 'tooltip' => 'Tax periods end on the last day of Jan, Mar, May, July, Sept, and Nov.', 'created_at' => '2026-02-08 15:48:05', 'updated_at' => '2026-02-08 15:48:05'],
        ['id' => 2, 'name' => 'Category B (Even Months) Feb Apr Jun Aug Oct and Dec', 'tooltip' => 'Tax periods end on the last day of Feb, Apr, June, Aug, Oct, and Dec.', 'created_at' => '2026-02-08 15:48:05', 'updated_at' => '2026-02-08 15:48:05'],
        ['id' => 3, 'name' => 'Category C (Monthly)', 'tooltip' => 'Mandatory for companies with taxable supplies exceeding R30 million annually.', 'created_at' => '2026-02-08 15:48:05', 'updated_at' => '2026-02-08 15:48:05'],
        ['id' => 4, 'name' => 'Category D (6-Monthly)', 'tooltip' => 'For specific farmers/small businesses with turnover under R1.5 million (Feb/Aug).', 'created_at' => '2026-02-08 15:48:05', 'updated_at' => '2026-02-08 15:48:05'],
        ['id' => 5, 'name' => 'Category E (Annual)', 'tooltip' => '12-month period, often for property letting.', 'created_at' => '2026-02-08 15:48:05', 'updated_at' => '2026-02-08 15:48:05'],
    ]);
    echo "SEEDED: cims_vat_cycles (5 rows)\n";
} else {
    echo "EXISTS: cims_vat_cycles (" . DB::table('cims_vat_cycles')->count() . " rows)\n";
}

// 5. cims_director_types
if (DB::table('cims_director_types')->count() == 0) {
    DB::table('cims_director_types')->insert([
        ['id' => 1, 'name' => 'Director', 'is_active' => 1, 'created_at' => '2026-02-08 15:48:05', 'updated_at' => '2026-02-08 15:48:05'],
        ['id' => 2, 'name' => 'Incorporator', 'is_active' => 1, 'created_at' => '2026-02-08 15:48:05', 'updated_at' => '2026-02-08 15:48:05'],
        ['id' => 3, 'name' => 'SARS Representative', 'is_active' => 1, 'created_at' => '2026-02-08 15:48:05', 'updated_at' => '2026-02-08 15:48:05'],
    ]);
    echo "SEEDED: cims_director_types (3 rows)\n";
} else {
    echo "EXISTS: cims_director_types (" . DB::table('cims_director_types')->count() . " rows)\n";
}

// 6. cims_director_status
if (DB::table('cims_director_status')->count() == 0) {
    DB::table('cims_director_status')->insert([
        ['id' => 1, 'name' => 'Current', 'is_active' => 1, 'created_at' => '2026-02-15 03:52:37', 'updated_at' => '2026-02-15 03:52:46'],
        ['id' => 2, 'name' => 'Past', 'is_active' => 1, 'created_at' => '2026-02-15 03:52:51', 'updated_at' => '2026-02-15 03:52:56'],
        ['id' => 3, 'name' => 'Resigned', 'is_active' => 1, 'created_at' => '2026-02-15 03:53:03', 'updated_at' => '2026-02-15 03:53:03'],
    ]);
    echo "SEEDED: cims_director_status (3 rows)\n";
} else {
    echo "EXISTS: cims_director_status (" . DB::table('cims_director_status')->count() . " rows)\n";
}

// 7. cims_bank_names (check if empty)
if (DB::table('cims_bank_names')->count() == 0) {
    DB::table('cims_bank_names')->insert([
        ['id' => 1, 'bank_name' => 'ABSA', 'tooltip' => 'Tooltip for ABSA Bank', 'branch_name' => 'ABSA Universal', 'branch_code' => '632005', 'swift_code' => 'ABSAZAJJ', 'bank_logo' => '/bank_logo/absa_logo.jpg', 'is_active' => 1, 'created_at' => null, 'updated_at' => null],
        ['id' => 2, 'bank_name' => 'Capitec Bank', 'tooltip' => 'Tooltip for Capitec Bank', 'branch_name' => 'Capitec Main', 'branch_code' => '470010', 'swift_code' => 'CABORAJJ', 'bank_logo' => '/bank_logo/capitec_bank.jpg', 'is_active' => 1, 'created_at' => null, 'updated_at' => null],
        ['id' => 3, 'bank_name' => 'First National Bank', 'tooltip' => 'Tooltip for FNB Bank', 'branch_name' => 'FNB Universal', 'branch_code' => '250655', 'swift_code' => 'FIRNZAJJ', 'bank_logo' => '/bank_logo/fnb_logo.jpg', 'is_active' => 1, 'created_at' => null, 'updated_at' => null],
        ['id' => 4, 'bank_name' => 'Investec', 'tooltip' => null, 'branch_name' => 'Investec Private', 'branch_code' => '580105', 'swift_code' => 'INVLZAJJ', 'bank_logo' => '/bank_logo/investec.png', 'is_active' => 0, 'created_at' => null, 'updated_at' => null],
        ['id' => 5, 'bank_name' => 'Nedbank', 'tooltip' => null, 'branch_name' => 'Nedbank Universal', 'branch_code' => '198765', 'swift_code' => 'NEDSZAJJ', 'bank_logo' => '/bank_logo/nedbank_logo.jpg', 'is_active' => 1, 'created_at' => null, 'updated_at' => null],
        ['id' => 6, 'bank_name' => 'Standard Bank', 'tooltip' => null, 'branch_name' => 'Standard Universal', 'branch_code' => '051001', 'swift_code' => 'SBZAZAJJ', 'bank_logo' => '/bank_logo/stb_bank_logo.jpg', 'is_active' => 1, 'created_at' => null, 'updated_at' => null],
        ['id' => 7, 'bank_name' => 'African Bank', 'tooltip' => null, 'branch_name' => 'African Bank', 'branch_code' => '430000', 'swift_code' => 'AABORAJJ', 'bank_logo' => '/bank_logo/african.png', 'is_active' => 0, 'created_at' => null, 'updated_at' => null],
        ['id' => 8, 'bank_name' => 'Bidvest Bank', 'tooltip' => null, 'branch_name' => 'Bidvest', 'branch_code' => '462005', 'swift_code' => 'BIDVZAJJ', 'bank_logo' => '/bank_logo/Bidvest-Logo.png', 'is_active' => 0, 'created_at' => null, 'updated_at' => null],
        ['id' => 9, 'bank_name' => 'Discovery Bank', 'tooltip' => null, 'branch_name' => 'Discovery', 'branch_code' => '679000', 'swift_code' => 'DISCZAJJ', 'bank_logo' => '/bank_logo/dicovery.png', 'is_active' => 0, 'created_at' => null, 'updated_at' => null],
        ['id' => 10, 'bank_name' => 'TymeBank', 'tooltip' => null, 'branch_name' => 'TymeBank Digital', 'branch_code' => '678910', 'swift_code' => 'TABORAJJ', 'bank_logo' => '/bank_logo/other_logo.jpg', 'is_active' => 1, 'created_at' => null, 'updated_at' => null],
    ]);
    echo "SEEDED: cims_bank_names (10 rows)\n";
} else {
    echo "EXISTS: cims_bank_names (" . DB::table('cims_bank_names')->count() . " rows)\n";
}

// 8. cims_address_types (check if empty)
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

echo "\n=== ALL DONE - DELETE THIS FILE ===\n</pre>";
