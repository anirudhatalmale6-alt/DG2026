<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sars_status', function (Blueprint $table) {
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

        // Seed default SARS return statuses
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
    }

    public function down(): void
    {
        Schema::dropIfExists('sars_status');
    }
};
