<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cims_appointments_settings', function (Blueprint $table) {
            $table->id();
            $table->string('setting_key', 100)->unique();
            $table->text('setting_value')->nullable();
            $table->string('setting_group', 50)->default('general');
            $table->timestamps();
        });

        // Seed default settings
        DB::table('cims_appointments_settings')->insert([
            ['setting_key' => 'confirmation_email_enabled', 'setting_value' => '1', 'setting_group' => 'email', 'created_at' => now(), 'updated_at' => now()],
            ['setting_key' => 'reminder_email_enabled', 'setting_value' => '1', 'setting_group' => 'email', 'created_at' => now(), 'updated_at' => now()],
            ['setting_key' => 'reminder_hours_before', 'setting_value' => '24', 'setting_group' => 'email', 'created_at' => now(), 'updated_at' => now()],
            ['setting_key' => 'cancellation_email_enabled', 'setting_value' => '1', 'setting_group' => 'email', 'created_at' => now(), 'updated_at' => now()],
            ['setting_key' => 'booking_buffer_hours', 'setting_value' => '2', 'setting_group' => 'booking', 'created_at' => now(), 'updated_at' => now()],
            ['setting_key' => 'cancellation_policy_hours', 'setting_value' => '24', 'setting_group' => 'booking', 'created_at' => now(), 'updated_at' => now()],
            ['setting_key' => 'default_slot_duration', 'setting_value' => '60', 'setting_group' => 'booking', 'created_at' => now(), 'updated_at' => now()],
            ['setting_key' => 'company_name', 'setting_value' => 'ATP Services', 'setting_group' => 'general', 'created_at' => now(), 'updated_at' => now()],
            ['setting_key' => 'company_email', 'setting_value' => 'info@atpservices.co.za', 'setting_group' => 'general', 'created_at' => now(), 'updated_at' => now()],
            ['setting_key' => 'company_phone', 'setting_value' => '(031) 101 3876', 'setting_group' => 'general', 'created_at' => now(), 'updated_at' => now()],
            ['setting_key' => 'company_address', 'setting_value' => '29 Coedmore Road, Bellair, Durban 4094', 'setting_group' => 'general', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('cims_appointments_settings');
    }
};
