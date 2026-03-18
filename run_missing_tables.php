<?php
/**
 * Create all missing tables for ClientMasterNew module
 * Run via browser, then DELETE this file
 */

// Bootstrap Laravel
// Try different bootstrap paths
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

if (!$bootstrapped) {
    die("Could not find Laravel bootstrap. Tried: " . implode(', ', $basePaths));
}
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

echo "<pre>\n";
echo "=== Creating Missing Tables for ClientMasterNew ===\n\n";

$results = [];

// 1. cims_bank_account_types
if (!Schema::hasTable('cims_bank_account_types')) {
    Schema::create('cims_bank_account_types', function (Blueprint $table) {
        $table->id();
        $table->string('bank_account_type', 255);
        $table->integer('bank_link_id');
        $table->string('bank_name', 80);
        $table->boolean('is_active')->default(true);
        $table->text('tooltip')->nullable();
        $table->timestamps();
    });

    // Seed with data from SQL dump
    DB::table('cims_bank_account_types')->insert([
        ['id' => 1, 'bank_account_type' => 'Easy Account', 'bank_link_id' => 3, 'bank_name' => 'First National Bank', 'is_active' => 0, 'tooltip' => null],
        ['id' => 2, 'bank_account_type' => 'Private Client', 'bank_link_id' => 3, 'bank_name' => 'First National Bank', 'is_active' => 0, 'tooltip' => null],
        ['id' => 3, 'bank_account_type' => 'Platinum Account', 'bank_link_id' => 3, 'bank_name' => 'First National Bank', 'is_active' => 0, 'tooltip' => null],
        ['id' => 4, 'bank_account_type' => 'Hair Cut Account', 'bank_link_id' => 4, 'bank_name' => 'Investec', 'is_active' => 1, 'tooltip' => null],
        ['id' => 5, 'bank_account_type' => 'Old School Account', 'bank_link_id' => 2, 'bank_name' => 'Capitec', 'is_active' => 1, 'tooltip' => null],
        ['id' => 13, 'bank_account_type' => 'First Business Zero Account', 'bank_link_id' => 3, 'bank_name' => 'First National Bank', 'is_active' => 1, 'tooltip' => 'Designed for new entrepreneurs & sole proprietors with minimal fees.'],
        ['id' => 14, 'bank_account_type' => 'Gold Business Account', 'bank_link_id' => 3, 'bank_name' => 'First National Bank', 'is_active' => 1, 'tooltip' => 'A bundle account with fixed monthly fee.'],
        ['id' => 15, 'bank_account_type' => 'Platinum Business Account', 'bank_link_id' => 3, 'bank_name' => 'First National Bank', 'is_active' => 1, 'tooltip' => 'A higher-tier business account with enhanced limits and features.'],
        ['id' => 16, 'bank_account_type' => 'Enterprise or Corporate Account', 'bank_link_id' => 3, 'bank_name' => 'First National Bank', 'is_active' => 1, 'tooltip' => 'Advanced / larger turnover accounts tailored for established businesses.'],
    ]);
    $results[] = "CREATED + SEEDED: cims_bank_account_types (9 rows)";
} else {
    $results[] = "EXISTS: cims_bank_account_types";
}

// 2. client_master_documents
if (!Schema::hasTable('client_master_documents')) {
    Schema::create('client_master_documents', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('client_id');
        $table->string('client_code', 50);
        $table->string('document_type', 150);
        $table->string('original_filename', 255);
        $table->string('stored_filename', 255);
        $table->string('file_path', 255);
        $table->unsignedBigInteger('file_size');
        $table->string('mime_type', 100);
        $table->timestamp('uploaded_at')->nullable();
        $table->unsignedBigInteger('uploaded_by')->nullable();
        $table->timestamps();
        $table->index(['client_id', 'document_type']);
    });
    $results[] = "CREATED: client_master_documents";
} else {
    $results[] = "EXISTS: client_master_documents";
}

// 3. Double-check other tables that should exist
$checkTables = [
    'cims_addresses',
    'cims_address_types',
    'cims_bank_names',
    'cims_director_status',
    'cims_director_types',
    'cims_documents',
    'cims_document_types',
    'cims_persons',
    'cims_vat_cycles',
    'client_master',
    'client_master_addresses',
    'client_master_audit',
    'client_master_banks',
    'client_master_directors',
    'client_master_lookups',
    'client_master_bank_documents_pivot',
    'company_types',
    'client_titles',
    'client_positions',
    'share_types',
    'ref_bank_account_types',
];

echo "--- Checking all required tables ---\n";
foreach ($checkTables as $table) {
    $exists = Schema::hasTable($table);
    echo ($exists ? "  OK" : "  MISSING") . ": $table\n";
}

echo "\n--- Results ---\n";
foreach ($results as $r) {
    echo "  $r\n";
}

echo "\n=== DONE. DELETE THIS FILE NOW ===\n";
echo "</pre>";
