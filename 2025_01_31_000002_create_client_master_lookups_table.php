<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientMasterLookupsTable extends Migration
{
    public function up()
    {
        // Lookup table for all dropdown values
        Schema::create('client_master_lookups', function (Blueprint $table) {
            $table->id();
            $table->string('category', 50); // title, gender, id_type, marital_status, marriage_type, province, address_type, etc.
            $table->string('code', 30);
            $table->string('value', 100);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['category', 'code']);
            $table->index('category');
        });
    }

    public function down()
    {
        Schema::dropIfExists('client_master_lookups');
    }

    
}
