<?php
/**
 * Migration: Create employee payslip defaults table
 * Run once via browser, then delete
 */
require_once __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

echo "<pre>\n";
echo "=== Employee Payslip Defaults Migration ===\n\n";

try {
    if (!Schema::hasTable('cims_payroll_employee_payslip_defaults')) {
        Schema::create('cims_payroll_employee_payslip_defaults', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->string('section', 20); // earnings, deductions, contributions, fringe
            $table->string('name', 100);
            $table->decimal('hours', 10, 4)->default(0);
            $table->decimal('rate', 12, 4)->default(0);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('employee_id')
                ->references('id')
                ->on('cims_payroll_employees')
                ->onDelete('cascade');

            $table->index(['employee_id', 'section']);
        });
        echo "✓ Created table: cims_payroll_employee_payslip_defaults\n";
    } else {
        echo "• Table already exists: cims_payroll_employee_payslip_defaults\n";
    }

    echo "\n=== Migration Complete ===\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
echo "</pre>";
