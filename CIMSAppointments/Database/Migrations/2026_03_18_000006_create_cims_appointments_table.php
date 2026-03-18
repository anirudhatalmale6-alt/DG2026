<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cims_appointments', function (Blueprint $table) {
            $table->id();

            // Client reference (links to client_master)
            $table->unsignedBigInteger('client_id')->nullable();
            $table->string('client_code', 50)->nullable();
            $table->string('client_name', 255)->nullable();
            $table->string('client_email', 255)->nullable();
            $table->string('client_phone', 30)->nullable();

            // Staff and service
            $table->unsignedBigInteger('staff_id');
            $table->unsignedBigInteger('service_id');

            // Scheduling
            $table->date('appointment_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('duration_hours')->default(1);

            // Status: pending, confirmed, completed, cancelled, no_show
            $table->string('status', 20)->default('pending');

            // Notes
            $table->text('notes')->nullable();
            $table->text('internal_notes')->nullable();

            // Charging
            $table->boolean('is_chargeable')->default(false);
            $table->decimal('amount', 15, 2)->default(0);
            $table->string('payment_status', 20)->default('unpaid')->comment('unpaid, paid, waived, invoiced');
            $table->unsignedBigInteger('invoice_id')->nullable()->comment('Links to Grow CRM invoices');

            // Email tracking
            $table->timestamp('confirmation_sent_at')->nullable();
            $table->timestamp('reminder_sent_at')->nullable();

            // Cancellation
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancellation_reason', 500)->nullable();

            // Completion
            $table->timestamp('completed_at')->nullable();

            // Audit
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('staff_id')->references('id')->on('cims_appointments_staff')->onDelete('restrict');
            $table->foreign('service_id')->references('id')->on('cims_appointments_services')->onDelete('restrict');

            // Indexes
            $table->index('client_id');
            $table->index('client_code');
            $table->index('staff_id');
            $table->index('service_id');
            $table->index('appointment_date');
            $table->index('status');
            $table->index('payment_status');
            $table->index(['appointment_date', 'staff_id']);
            $table->index(['appointment_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cims_appointments');
    }
};
