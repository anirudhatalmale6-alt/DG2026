<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cims_document_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('category_id')->default(0);
            $table->string('doc_ref', 255)->nullable();
            $table->string('doc_group', 255)->nullable();
            $table->string('description', 255)->nullable();
            $table->integer('lead_time_days')->default(30);
            $table->boolean('has_expiry')->default(false);
            $table->integer('days_to_expire')->default(0);
            $table->unsignedInteger('client_id')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cims_document_types');
    }
};
