<?php
// Bootstrap Laravel
$basePaths = [
    __DIR__ . '/../application',
    __DIR__ . '/../../application',
    __DIR__ . '/..',
];
$bootstrapped = false;
foreach ($basePaths as $base) {
    if (file_exists($base . '/bootstrap/app.php')) {
        if (file_exists($base . '/bootstrap/autoload.php')) {
            require $base . '/bootstrap/autoload.php';
        } elseif (file_exists($base . '/vendor/autoload.php')) {
            require $base . '/vendor/autoload.php';
        }
        $app = require_once $base . '/bootstrap/app.php';
        $bootstrapped = true;
        break;
    }
}
if (!$bootstrapped) { die("Could not find Laravel bootstrap."); }
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "<pre>\n";

// Check company_types table
if (!Schema::hasTable('company_types')) {
    echo "TABLE company_types DOES NOT EXIST!\n";
    Schema::create('company_types', function ($table) {
        $table->id();
        $table->tinyInteger('type_code')->unsigned()->unique();
        $table->string('type_name', 100);
        $table->string('type_abbreviation', 20)->nullable();
        $table->text('type_description')->nullable();
        $table->string('type_label', 255)->nullable();
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });
    echo "CREATED: company_types\n";
}

$count = DB::table('company_types')->count();
echo "company_types row count: $count\n";

if ($count == 0) {
    echo "Table is EMPTY - seeding now...\n";
    DB::table('company_types')->insert([
        ['id' => 1, 'type_code' => 6, 'type_name' => 'External Company', 'type_abbreviation' => 'External', 'type_description' => 'A foreign company operating in South Africa.', 'type_label' => 'Foreign company registered to operate in SA', 'is_active' => 1, 'created_at' => null, 'updated_at' => null],
        ['id' => 2, 'type_code' => 7, 'type_name' => 'Private Company (Pty) Ltd', 'type_abbreviation' => 'Pty', 'type_description' => 'Most common business structure. Separate legal entity with limited liability.', 'type_label' => 'Limited liability company - most common for SMEs', 'is_active' => 1, 'created_at' => null, 'updated_at' => null],
        ['id' => 3, 'type_code' => 8, 'type_name' => 'Non-Profit Company (NPC)', 'type_abbreviation' => 'NPC', 'type_description' => 'Established for public benefit, social, cultural, religious or charitable purposes.', 'type_label' => 'For charitable/public benefit - no profit distribution', 'is_active' => 1, 'created_at' => null, 'updated_at' => null],
        ['id' => 4, 'type_code' => 9, 'type_name' => 'State Owned Company (SOC)', 'type_abbreviation' => 'SOC', 'type_description' => 'Company owned by the South African government.', 'type_label' => 'Government-owned entity providing public services', 'is_active' => 1, 'created_at' => null, 'updated_at' => null],
        ['id' => 5, 'type_code' => 10, 'type_name' => 'Public Company (Ltd)', 'type_abbreviation' => 'Ltd', 'type_description' => 'Large-scale business structure that can offer shares to the general public.', 'type_label' => 'Can offer shares publicly - for large businesses', 'is_active' => 1, 'created_at' => null, 'updated_at' => null],
        ['id' => 6, 'type_code' => 11, 'type_name' => 'Personal Liability Company (Inc)', 'type_abbreviation' => 'Inc', 'type_description' => 'Used by professionals like lawyers, doctors and accountants.', 'type_label' => 'Directors personally liable for debts - for professionals', 'is_active' => 1, 'created_at' => null, 'updated_at' => null],
        ['id' => 7, 'type_code' => 21, 'type_name' => 'Incorporated Company', 'type_abbreviation' => 'Inc', 'type_description' => 'A company incorporated under previous legislation.', 'type_label' => 'Company incorporated under previous legislation', 'is_active' => 1, 'created_at' => null, 'updated_at' => null],
        ['id' => 8, 'type_code' => 23, 'type_name' => 'Close Corporation (CC)', 'type_abbreviation' => 'CC', 'type_description' => 'No longer available for new registrations since 2011. Existing CCs remain valid.', 'type_label' => 'Legacy structure - no new registrations since 2011', 'is_active' => 1, 'created_at' => null, 'updated_at' => null],
        ['id' => 9, 'type_code' => 24, 'type_name' => 'Co-operative', 'type_abbreviation' => 'Co-op', 'type_description' => 'An autonomous association of persons united to meet common needs.', 'type_label' => 'Member-owned democratic enterprise', 'is_active' => 1, 'created_at' => null, 'updated_at' => null],
        ['id' => 10, 'type_code' => 25, 'type_name' => 'Primary Co-operative', 'type_abbreviation' => 'Primary Co-op', 'type_description' => 'A co-operative formed by natural persons to provide services directly to its members.', 'type_label' => 'Direct member services co-operative', 'is_active' => 1, 'created_at' => null, 'updated_at' => null],
        ['id' => 11, 'type_code' => 26, 'type_name' => 'Secondary Co-operative', 'type_abbreviation' => 'Secondary Co-op', 'type_description' => 'A co-operative formed by two or more primary co-operatives.', 'type_label' => 'Co-operative of primary co-operatives', 'is_active' => 1, 'created_at' => null, 'updated_at' => null],
    ]);
    echo "SEEDED: 11 company types\n";
} else {
    echo "Table has data - listing:\n";
    $rows = DB::table('company_types')->get();
    foreach ($rows as $r) {
        echo "  Code {$r->type_code}: {$r->type_name} (active: {$r->is_active})\n";
    }
}

// Also check other lookup tables
$lookups = ['client_titles', 'client_positions', 'share_types', 'cims_vat_cycles', 'cims_director_types', 'cims_director_status'];
echo "\n--- Other lookup tables ---\n";
foreach ($lookups as $table) {
    if (Schema::hasTable($table)) {
        $c = DB::table($table)->count();
        echo "  $table: $c rows\n";
    } else {
        echo "  $table: TABLE MISSING!\n";
    }
}

echo "\nDONE - DELETE THIS FILE\n</pre>";
