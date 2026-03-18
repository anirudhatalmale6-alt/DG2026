<?php
/**
 * One-time migration runner for SARS Status table and EMP201 declaration updates.
 * Access via: https://smartweigh.co.za/run_migration.php
 * DELETE THIS FILE AFTER RUNNING.
 */

// Load Laravel
require __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

header('Content-Type: text/plain');

echo "=== CIMS Migration Runner ===\n\n";

try {
    // Step 1: Create sars_status table
    if (!Schema::hasTable('sars_status')) {
        Schema::create('sars_status', function ($table) {
            $table->id();
            $table->string('status_name', 100);
            $table->tinyInteger('emp201')->default(0);
            $table->tinyInteger('emp501')->default(0);
            $table->tinyInteger('itax')->default(0);
            $table->tinyInteger('vat')->default(0);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('emp201');
            $table->index('emp501');
            $table->index('itax');
            $table->index('vat');
            $table->index('is_active');
        });

        // Seed default statuses
        DB::table('sars_status')->insert([
            ['status_name' => 'Draft',              'emp201' => 1, 'emp501' => 1, 'itax' => 1, 'vat' => 1, 'is_active' => 1, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['status_name' => 'Prepared',            'emp201' => 1, 'emp501' => 1, 'itax' => 1, 'vat' => 1, 'is_active' => 1, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['status_name' => 'Reviewed',            'emp201' => 1, 'emp501' => 1, 'itax' => 1, 'vat' => 1, 'is_active' => 1, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['status_name' => 'Approved',            'emp201' => 1, 'emp501' => 1, 'itax' => 1, 'vat' => 1, 'is_active' => 1, 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['status_name' => 'Submitted to SARS',   'emp201' => 1, 'emp501' => 1, 'itax' => 1, 'vat' => 1, 'is_active' => 1, 'sort_order' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['status_name' => 'Accepted by SARS',    'emp201' => 1, 'emp501' => 1, 'itax' => 1, 'vat' => 1, 'is_active' => 1, 'sort_order' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['status_name' => 'Rejected',            'emp201' => 1, 'emp501' => 1, 'itax' => 1, 'vat' => 1, 'is_active' => 1, 'sort_order' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['status_name' => 'Payment Pending',     'emp201' => 1, 'emp501' => 0, 'itax' => 1, 'vat' => 1, 'is_active' => 1, 'sort_order' => 8, 'created_at' => now(), 'updated_at' => now()],
            ['status_name' => 'Paid',                'emp201' => 1, 'emp501' => 0, 'itax' => 1, 'vat' => 1, 'is_active' => 1, 'sort_order' => 9, 'created_at' => now(), 'updated_at' => now()],
            ['status_name' => 'Assessment Received', 'emp201' => 1, 'emp501' => 1, 'itax' => 1, 'vat' => 1, 'is_active' => 1, 'sort_order' => 10, 'created_at' => now(), 'updated_at' => now()],
        ]);

        echo "✓ Created sars_status table with 10 seed records\n";
    } else {
        echo "• sars_status table already exists — skipped\n";
    }

    // Step 2: Add emp201_status and approved_by to cims_emp201_declarations
    if (!Schema::hasColumn('cims_emp201_declarations', 'emp201_status')) {
        Schema::table('cims_emp201_declarations', function ($table) {
            $table->unsignedBigInteger('emp201_status')->nullable()->after('status');
            $table->index('emp201_status');
        });
        echo "✓ Added emp201_status column to cims_emp201_declarations\n";
    } else {
        echo "• emp201_status column already exists — skipped\n";
    }

    if (!Schema::hasColumn('cims_emp201_declarations', 'approved_by')) {
        Schema::table('cims_emp201_declarations', function ($table) {
            $table->string('approved_by', 255)->nullable()->after('prepared_by');
        });
        echo "✓ Added approved_by column to cims_emp201_declarations\n";
    } else {
        echo "• approved_by column already exists — skipped\n";
    }

    // Step 3: Ensure prepared_by column exists (it's in the model but may be missing from DB)
    if (!Schema::hasColumn('cims_emp201_declarations', 'prepared_by')) {
        Schema::table('cims_emp201_declarations', function ($table) {
            $table->string('prepared_by', 255)->nullable()->after('declaration_date');
        });
        echo "✓ Added prepared_by column to cims_emp201_declarations\n";
    } else {
        echo "• prepared_by column already exists — OK\n";
    }

    echo "\n=== Migration Complete ===\n";
    echo "\n⚠️  IMPORTANT: Delete this file from the server now!\n";

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
