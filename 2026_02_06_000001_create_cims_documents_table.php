<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cims_documents', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255)->nullable();
            $table->string('document_code', 255)->nullable();
            $table->string('document_ref', 255)->nullable();
            $table->string('file_original_name', 255)->nullable();
            $table->string('file_stored_name', 255)->nullable();
            $table->string('file_mime_type', 100)->nullable();
            $table->string('file_path', 255)->nullable();
            $table->unsignedInteger('client_id')->nullable();
            $table->string('client_name', 255)->nullable();
            $table->string('client_code', 100)->nullable();
            $table->string('client_email', 255)->nullable();
            $table->string('registration_number', 255)->nullable();
            $table->unsignedInteger('category_id')->nullable();
            $table->unsignedInteger('type_id')->nullable();
            $table->string('doc_group', 255)->nullable();
            $table->unsignedInteger('period_id')->nullable();
            $table->string('period_name', 255)->nullable();
            $table->string('period_combo', 100)->nullable();
            $table->string('financial_year', 10)->nullable();
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->date('date_registered')->nullable();
            $table->string('has_expiry', 10)->default('NO');
            $table->integer('lead_time_days')->default(0);
            $table->integer('days_to_expire')->default(0);
            $table->string('status', 50)->default('Current');
            $table->boolean('show_as_current')->default(true);
            $table->boolean('is_archived')->default(false);
            $table->boolean('is_trashed')->default(false);
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->string('uploaded_by', 255)->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cims_documents');
    }
};
