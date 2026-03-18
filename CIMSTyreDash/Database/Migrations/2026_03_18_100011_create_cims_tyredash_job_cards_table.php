<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Job card header
        Schema::create('cims_tyredash_job_cards', function (Blueprint $table) {
            $table->id();
            $table->string('job_card_number', 30)->unique();
            $table->unsignedBigInteger('quote_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('vehicle_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('technician_id')->nullable()->comment('User ID of technician');
            $table->string('technician_name', 150)->nullable();
            $table->date('job_date');
            $table->string('status', 20)->default('open')->comment('open, in_progress, awaiting_parts, complete, invoiced, cancelled');
            $table->integer('odometer_in')->nullable();
            $table->integer('odometer_out')->nullable();
            $table->text('vehicle_condition_notes')->nullable();
            $table->text('work_notes')->nullable();
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedBigInteger('invoice_id')->nullable()->comment('Grow CRM invoice ID');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('quote_id')->references('id')->on('cims_tyredash_quotes')->onDelete('set null');
            $table->foreign('customer_id')->references('id')->on('cims_tyredash_customers')->onDelete('set null');
            $table->foreign('vehicle_id')->references('id')->on('cims_tyredash_vehicles')->onDelete('set null');
            $table->foreign('branch_id')->references('id')->on('cims_tyredash_branches')->onDelete('set null');
            $table->index('status');
            $table->index('job_date');
            $table->index('branch_id');
        });

        // Job card tyre lines (tyres fitted/removed)
        Schema::create('cims_tyredash_job_card_tyres', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_card_id');
            $table->unsignedBigInteger('product_id');
            $table->integer('quantity')->default(1);
            $table->string('position', 20)->nullable()->comment('FL, FR, RL, RR, spare');
            $table->string('serial_number_new', 50)->nullable();
            $table->string('serial_number_old', 50)->nullable();
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('line_total', 15, 2)->default(0);
            $table->timestamps();

            $table->foreign('job_card_id')->references('id')->on('cims_tyredash_job_cards')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('cims_tyredash_products')->onDelete('restrict');
            $table->index('job_card_id');
            $table->index('serial_number_new');
        });

        // Job card services
        Schema::create('cims_tyredash_job_card_services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_card_id');
            $table->unsignedBigInteger('service_id');
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('line_total', 15, 2)->default(0);
            $table->boolean('completed')->default(false);
            $table->timestamps();

            $table->foreign('job_card_id')->references('id')->on('cims_tyredash_job_cards')->onDelete('cascade');
            $table->foreign('service_id')->references('id')->on('cims_tyredash_services')->onDelete('restrict');
            $table->index('job_card_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cims_tyredash_job_card_services');
        Schema::dropIfExists('cims_tyredash_job_card_tyres');
        Schema::dropIfExists('cims_tyredash_job_cards');
    }
};
