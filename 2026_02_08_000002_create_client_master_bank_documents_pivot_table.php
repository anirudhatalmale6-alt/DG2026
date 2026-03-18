<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_master_bank_documents_pivot', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_master_bank_id');
            $table->unsignedBigInteger('client_master_document_id');
            $table->timestamps();

            // Foreign keys with cascade delete
            $table->foreign('client_master_bank_id', 'bank_doc_bank_fk')
                ->references('id')
                ->on('client_master_banks')
                ->onDelete('cascade');

            $table->foreign('client_master_document_id', 'bank_doc_document_fk')
                ->references('id')
                ->on('client_master_documents')
                ->onDelete('cascade');

            // Unique constraint
            $table->unique(
                ['client_master_bank_id', 'client_master_document_id'],
                'bank_doc_pivot_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_master_bank_documents_pivot');
    }
};
