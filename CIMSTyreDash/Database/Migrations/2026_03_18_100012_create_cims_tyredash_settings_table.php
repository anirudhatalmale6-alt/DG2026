<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cims_tyredash_settings', function (Blueprint $table) {
            $table->id();
            $table->string('setting_key', 100)->unique();
            $table->text('setting_value')->nullable();
            $table->string('setting_group', 50)->default('general');
            $table->timestamps();
        });

        $now = now();
        DB::table('cims_tyredash_settings')->insert([
            // General
            ['setting_key' => 'company_name', 'setting_value' => 'Apex Tyres', 'setting_group' => 'general', 'created_at' => $now, 'updated_at' => $now],
            ['setting_key' => 'company_phone', 'setting_value' => '', 'setting_group' => 'general', 'created_at' => $now, 'updated_at' => $now],
            ['setting_key' => 'company_email', 'setting_value' => '', 'setting_group' => 'general', 'created_at' => $now, 'updated_at' => $now],
            ['setting_key' => 'company_address', 'setting_value' => '', 'setting_group' => 'general', 'created_at' => $now, 'updated_at' => $now],
            ['setting_key' => 'vat_number', 'setting_value' => '', 'setting_group' => 'general', 'created_at' => $now, 'updated_at' => $now],

            // Pricing
            ['setting_key' => 'default_markup_pct', 'setting_value' => '20', 'setting_group' => 'pricing', 'created_at' => $now, 'updated_at' => $now],
            ['setting_key' => 'vat_rate', 'setting_value' => '15', 'setting_group' => 'pricing', 'created_at' => $now, 'updated_at' => $now],
            ['setting_key' => 'currency_symbol', 'setting_value' => 'R', 'setting_group' => 'pricing', 'created_at' => $now, 'updated_at' => $now],

            // Quotes
            ['setting_key' => 'quote_prefix', 'setting_value' => 'TD', 'setting_group' => 'quotes', 'created_at' => $now, 'updated_at' => $now],
            ['setting_key' => 'quote_validity_days', 'setting_value' => '14', 'setting_group' => 'quotes', 'created_at' => $now, 'updated_at' => $now],
            ['setting_key' => 'max_quote_options', 'setting_value' => '5', 'setting_group' => 'quotes', 'created_at' => $now, 'updated_at' => $now],

            // Job Cards
            ['setting_key' => 'job_card_prefix', 'setting_value' => 'JC', 'setting_group' => 'jobcards', 'created_at' => $now, 'updated_at' => $now],

            // Stock
            ['setting_key' => 'default_min_stock', 'setting_value' => '4', 'setting_group' => 'stock', 'created_at' => $now, 'updated_at' => $now],
            ['setting_key' => 'low_stock_alert', 'setting_value' => '1', 'setting_group' => 'stock', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('cims_tyredash_settings');
    }
};
