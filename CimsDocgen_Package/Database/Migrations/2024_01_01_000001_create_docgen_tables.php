<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Master document templates
        Schema::create('docgen_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 50)->unique();
            $table->text('description')->nullable();
            $table->string('category')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Template pages (one template = many pages, reorderable)
        Schema::create('docgen_template_pages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('template_id');
            $table->integer('page_number');
            $table->string('page_label')->nullable();
            $table->string('pdf_path')->comment('Path to background PDF page file');
            $table->string('orientation')->default('portrait');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('template_id')->references('id')->on('docgen_templates')->onDelete('cascade');
            $table->unique(['template_id', 'page_number']);
        });

        // Field mappings - defines where each data field goes on a page (x,y coordinates)
        Schema::create('docgen_field_mappings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('template_page_id');
            $table->string('field_name')->comment('Database column or custom field name');
            $table->string('field_label');
            $table->string('field_source')->default('client_master')->comment('Table/source: client_master, client_master_directors, client_master_addresses, form_input');
            $table->decimal('pos_x', 8, 2)->comment('X position in mm from left');
            $table->decimal('pos_y', 8, 2)->comment('Y position in mm from top');
            $table->decimal('width', 8, 2)->nullable()->comment('Max width in mm');
            $table->decimal('height', 8, 2)->nullable()->comment('Max height in mm');
            $table->string('font_family')->nullable()->comment('Override global font');
            $table->decimal('font_size', 5, 2)->nullable()->comment('Override global size');
            $table->string('font_style')->nullable()->comment('bold, italic, underline');
            $table->string('font_color', 20)->nullable()->comment('Hex color e.g. #000000');
            $table->string('text_align')->default('left')->comment('left, center, right');
            $table->string('field_type')->default('text')->comment('text, date, checkbox, image, signature');
            $table->string('date_format')->nullable()->comment('e.g. d/m/Y');
            $table->string('default_value')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('template_page_id')->references('id')->on('docgen_template_pages')->onDelete('cascade');
        });

        // Generated documents
        Schema::create('docgen_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('template_id');
            $table->unsignedBigInteger('client_id')->nullable();
            $table->string('client_code', 50)->nullable();
            $table->string('client_name')->nullable();
            $table->string('document_name');
            $table->string('document_number')->unique();
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->string('requested_by')->nullable();
            $table->string('prepared_by')->nullable();
            $table->string('approved_by')->nullable();
            $table->string('signed_by')->nullable();
            $table->date('document_date')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('active')->comment('active, inactive, deleted');
            $table->boolean('emailed')->default(false);
            $table->string('emailed_to')->nullable();
            $table->timestamp('emailed_at')->nullable();
            $table->unsignedBigInteger('generated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('template_id')->references('id')->on('docgen_templates');
            $table->index('client_id');
            $table->index('client_code');
            $table->index('document_number');
            $table->index('status');
        });

        // Audit log for document actions
        Schema::create('docgen_audit_log', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('document_id');
            $table->string('action')->comment('generated, viewed, emailed, downloaded, status_changed, deleted');
            $table->string('action_by')->nullable();
            $table->unsignedBigInteger('action_by_id')->nullable();
            $table->text('details')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->foreign('document_id')->references('id')->on('docgen_documents')->onDelete('cascade');
            $table->index('document_id');
        });

        // Global settings
        Schema::create('docgen_settings', function (Blueprint $table) {
            $table->id();
            $table->string('setting_key')->unique();
            $table->text('setting_value')->nullable();
            $table->string('setting_group')->default('general');
            $table->string('setting_type')->default('text')->comment('text, number, select, color, boolean');
            $table->string('label')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('docgen_audit_log');
        Schema::dropIfExists('docgen_documents');
        Schema::dropIfExists('docgen_field_mappings');
        Schema::dropIfExists('docgen_template_pages');
        Schema::dropIfExists('docgen_templates');
        Schema::dropIfExists('docgen_settings');
    }
};
