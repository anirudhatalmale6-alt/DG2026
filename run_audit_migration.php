<?php
/**
 * One-time migration runner for CIMS Audit Log table.
 * DELETE THIS FILE AFTER RUNNING.
 */
require __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

header('Content-Type: text/plain');
echo "=== CIMS Audit Log Migration ===\n\n";

try {
    if (!Schema::hasTable('cims_audit_log')) {
        Schema::create('cims_audit_log', function ($table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_name', 255)->nullable();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->string('client_code', 50)->nullable();
            $table->string('return_type', 20)->nullable(); // emp201, emp501, itax, vat
            $table->unsignedBigInteger('period_id')->nullable();
            $table->string('period_name', 100)->nullable();
            $table->string('action', 50); // e.g. duplicate_override
            $table->text('notes')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index('client_id');
            $table->index('return_type');
            $table->index('action');
            $table->index('user_id');
            $table->index('created_at');
        });
        echo "✓ Created cims_audit_log table\n";
    } else {
        echo "• cims_audit_log table already exists — skipped\n";
    }

    echo "\n=== Migration Complete ===\n";
    echo "\n⚠️  DELETE this file now!\n";

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
