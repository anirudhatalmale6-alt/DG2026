<?php
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

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;

echo "<pre>\n=== Fixing Bank Tables ===\n\n";

// 1. Fix cims_bank_names - add tooltip column if missing, then truncate and reseed with all 13 rows
echo "--- cims_bank_names ---\n";
if (!Schema::hasColumn('cims_bank_names', 'tooltip')) {
    Schema::table('cims_bank_names', function (Blueprint $table) {
        $table->text('tooltip')->nullable()->after('bank_name');
    });
    echo "Added 'tooltip' column\n";
}

// Truncate and reseed with correct data from SQL dump
DB::table('cims_bank_names')->truncate();
DB::table('cims_bank_names')->insert([
    ['id' => 1, 'bank_name' => 'ABSA', 'tooltip' => 'Tooltip for ABSA Bank', 'branch_name' => 'ABSA Universal', 'branch_code' => '632005', 'swift_code' => 'ABSAZAJJ', 'bank_logo' => '/bank_logo/absa_logo.jpg', 'is_active' => 1],
    ['id' => 2, 'bank_name' => 'Capitec Bank', 'tooltip' => "Tooltip for Capitec\nBank", 'branch_name' => 'Capitec Main', 'branch_code' => '470010', 'swift_code' => 'CABORAJJ', 'bank_logo' => '/bank_logo/capitec_bank.jpg', 'is_active' => 1],
    ['id' => 3, 'bank_name' => 'First National Bank', 'tooltip' => "Tooltip for FNB\nBank", 'branch_name' => 'FNB Universal', 'branch_code' => '250655', 'swift_code' => 'FIRNZAJJ', 'bank_logo' => '/bank_logo/fnb_logo.jpg', 'is_active' => 1],
    ['id' => 4, 'bank_name' => 'Investec', 'tooltip' => null, 'branch_name' => 'Investec Private', 'branch_code' => '580105', 'swift_code' => 'INVLZAJJ', 'bank_logo' => '/bank_logo/investec.png', 'is_active' => 0],
    ['id' => 5, 'bank_name' => 'Nedbank', 'tooltip' => null, 'branch_name' => 'Nedbank Universal', 'branch_code' => '198765', 'swift_code' => 'NEDSZAJJ', 'bank_logo' => '/bank_logo/nedbank_logo.jpg', 'is_active' => 1],
    ['id' => 6, 'bank_name' => 'Standard Bank', 'tooltip' => null, 'branch_name' => 'Standard Universal', 'branch_code' => '051001', 'swift_code' => 'SBZAZAJJ', 'bank_logo' => '/bank_logo/stb_bank_logo.jpg', 'is_active' => 1],
    ['id' => 7, 'bank_name' => 'African Bank', 'tooltip' => null, 'branch_name' => 'African Bank', 'branch_code' => '430000', 'swift_code' => 'AABORAJJ', 'bank_logo' => '/bank_logo/african.png', 'is_active' => 0],
    ['id' => 8, 'bank_name' => 'Bidvest Bank', 'tooltip' => null, 'branch_name' => 'Bidvest', 'branch_code' => '462005', 'swift_code' => 'BIDVZAJJ', 'bank_logo' => '/bank_logo/Bidvest-Logo.png', 'is_active' => 0],
    ['id' => 9, 'bank_name' => 'Discovery Bank', 'tooltip' => null, 'branch_name' => 'Discovery', 'branch_code' => '679000', 'swift_code' => 'DISCZAJJ', 'bank_logo' => '/bank_logo/dicovery.png', 'is_active' => 0],
    ['id' => 10, 'bank_name' => 'TymeBank', 'tooltip' => null, 'branch_name' => 'TymeBank Digital', 'branch_code' => '678910', 'swift_code' => 'TABORAJJ', 'bank_logo' => '/bank_logo/other_logo.jpg', 'is_active' => 1],
    ['id' => 11, 'bank_name' => 'Sasfin Bank', 'tooltip' => null, 'branch_name' => 'Sasfin', 'branch_code' => '683000', 'swift_code' => 'SASFZAJJ', 'bank_logo' => '/bank_logo/sasfin.png', 'is_active' => 0],
    ['id' => 12, 'bank_name' => 'Grindrod Bank', 'tooltip' => null, 'branch_name' => 'Grindrod', 'branch_code' => '223626', 'swift_code' => 'GRINZAJJ', 'bank_logo' => '/bank_logo/grindrod.jpeg', 'is_active' => 0],
    ['id' => 13, 'bank_name' => 'Capitec Business', 'tooltip' => "Tooltip for Capitec\r\nBusiness", 'branch_name' => 'Capitec Main', 'branch_code' => '470010', 'swift_code' => 'CABORAJJ', 'bank_logo' => '/bank_logo/capitec_business.jpg', 'is_active' => 1],
]);
echo "RESEEDED: cims_bank_names (13 rows)\n";

// List them
$banks = DB::table('cims_bank_names')->get();
foreach ($banks as $b) {
    echo "  ID {$b->id}: {$b->bank_name} | logo: {$b->bank_logo} | active: {$b->is_active}\n";
}

// 2. Create cims_bank_statement_frequency if missing
echo "\n--- cims_bank_statement_frequency ---\n";
if (!Schema::hasTable('cims_bank_statement_frequency')) {
    Schema::create('cims_bank_statement_frequency', function (Blueprint $table) {
        $table->id();
        $table->string('bank_account_statement_frequency', 255);
        $table->integer('bank_link_id');
        $table->string('bank_name', 80);
        $table->boolean('is_active')->default(true);
        $table->text('tooltip')->nullable();
        $table->timestamps();
    });
    echo "CREATED: cims_bank_statement_frequency\n";
} else {
    echo "EXISTS: cims_bank_statement_frequency\n";
}

if (DB::table('cims_bank_statement_frequency')->count() == 0) {
    DB::table('cims_bank_statement_frequency')->insert([
        ['id' => 1, 'bank_account_statement_frequency' => 'Monthly', 'bank_link_id' => 3, 'bank_name' => 'First National Bank', 'is_active' => 1, 'tooltip' => 'This account gets monthly statements.'],
        ['id' => 2, 'bank_account_statement_frequency' => 'Bi-Monthly', 'bank_link_id' => 3, 'bank_name' => 'First National Bank', 'is_active' => 1, 'tooltip' => 'This account gets statements every 60 days.'],
        ['id' => 3, 'bank_account_statement_frequency' => 'Do Not Show', 'bank_link_id' => 3, 'bank_name' => 'First National Bank', 'is_active' => 0, 'tooltip' => 'This account gets statements every 60 days.'],
    ]);
    echo "SEEDED: cims_bank_statement_frequency (3 rows)\n";
} else {
    echo "Already has " . DB::table('cims_bank_statement_frequency')->count() . " rows\n";
}

// 3. Verify cims_bank_account_types
echo "\n--- cims_bank_account_types ---\n";
$count = DB::table('cims_bank_account_types')->count();
echo "Has $count rows\n";
$acctTypes = DB::table('cims_bank_account_types')->get();
foreach ($acctTypes as $a) {
    echo "  ID {$a->id}: {$a->bank_account_type} | bank: {$a->bank_name} (link_id: {$a->bank_link_id}) | active: {$a->is_active}\n";
}

echo "\n=== DONE - DELETE THIS FILE ===\n</pre>";
