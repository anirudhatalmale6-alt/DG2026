<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cims_emp201_declarations', function (Blueprint $table) {
            $table->unsignedBigInteger('emp201_status')->nullable()->after('status');
            $table->string('approved_by', 255)->nullable()->after('prepared_by');

            $table->index('emp201_status');
        });
    }

    public function down(): void
    {
        Schema::table('cims_emp201_declarations', function (Blueprint $table) {
            $table->dropIndex(['emp201_status']);
            $table->dropColumn(['emp201_status', 'approved_by']);
        });
    }
};
