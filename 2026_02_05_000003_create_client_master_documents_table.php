<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_master_documents', function (Blueprint $table): void {
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
    }

    public function down(): void
    {
        Schema::dropIfExists('client_master_documents');
    }
};
