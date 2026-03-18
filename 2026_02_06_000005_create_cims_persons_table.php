<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cims_persons', function (Blueprint $table) {
            $table->id();
            $table->string('identity_type', 40)->nullable();
            $table->string('citizenship', 40)->default('SOUTH AFRICAN');
            $table->string('identity_number', 20)->nullable();
            $table->string('gender', 10)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('person_status', 20)->default('Active');
            $table->date('date_of_death')->nullable();
            $table->date('person_deceased_date')->nullable();
            $table->string('ethnic_group', 40)->nullable();
            $table->string('disability', 20)->default('0');
            $table->string('passport_number', 30)->nullable();
            $table->date('passport_expiry')->nullable();
            $table->string('country_of_origin', 80)->nullable();
            $table->string('country', 80)->default('South Africa');
            $table->string('country_code', 10)->nullable();
            $table->string('nationality', 80)->nullable();
            $table->string('smart_card_number', 30)->nullable();
            $table->date('date_of_issue')->nullable();
            $table->string('title', 10)->nullable();
            $table->string('initials', 10)->nullable();
            $table->string('surname', 80);
            $table->string('firstname', 80);
            $table->string('middlename', 80)->nullable();
            $table->string('known_as', 80)->nullable();
            $table->string('tax_number', 20)->nullable();
            $table->string('mobile_phone', 20)->nullable();
            $table->string('whatsapp_number', 20)->nullable();
            $table->string('office_phone', 20)->nullable();
            $table->string('other_phone', 20)->nullable();
            $table->string('business_phone', 20)->nullable();
            $table->string('home_phone', 20)->nullable();
            $table->string('direct_number', 20)->nullable();
            $table->string('email', 120)->nullable();
            $table->string('accounts_email', 120)->nullable();
            $table->string('sars_tax_number', 20)->nullable();
            $table->string('sars_login_name', 120)->nullable();
            $table->string('sars_login_password', 120)->nullable();
            $table->string('sars_cell_number', 20)->nullable();
            $table->string('sars_email_address', 120)->nullable();
            $table->string('marital_status', 40)->nullable();
            $table->date('marital_status_date')->nullable();
            $table->string('spouse_id_number', 20)->nullable();
            $table->string('spouse_gender', 10)->nullable();
            $table->string('spouse_title', 10)->nullable();
            $table->string('spouse_initials', 10)->nullable();
            $table->string('spouse_name', 80)->nullable();
            $table->string('spouse_surname', 80)->nullable();
            $table->string('spouse_marriage_type', 40)->nullable();
            $table->date('spouse_date_of_marriage')->nullable();
            $table->string('spouse_tax_number', 20)->nullable();
            $table->unsignedBigInteger('address_id')->nullable();
            $table->string('complex_name', 120)->nullable();
            $table->string('address_line', 200)->nullable();
            $table->string('address_line_2', 200)->nullable();
            $table->string('suburb', 80)->nullable();
            $table->string('city', 80)->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->string('province', 40)->nullable();
            $table->string('address_country', 80)->default('South Africa');
            $table->string('latitude', 30)->nullable();
            $table->string('longitude', 30)->nullable();
            $table->string('bank_account_holder', 120)->nullable();
            $table->string('bank_name', 80)->nullable();
            $table->string('bank_branch_name', 80)->nullable();
            $table->string('bank_branch', 20)->nullable();
            $table->string('bank_branch_code', 20)->nullable();
            $table->string('bank_account_name', 120)->nullable();
            $table->string('bank_account_number', 40)->nullable();
            $table->string('bank_account_type', 40)->nullable();
            $table->string('bank_swift_code', 20)->nullable();
            $table->date('bank_date_opened')->nullable();
            $table->string('bank_account_status', 20)->nullable();
            $table->string('profile_picture', 255)->nullable();
            $table->string('signature_upload', 255)->nullable();
            $table->text('notes')->nullable();
            $table->string('sp_citizenship', 40)->nullable();
            $table->string('sp_identity_type', 40)->nullable();
            $table->string('sp_identity_number', 20)->nullable();
            $table->date('sp_date_of_birth')->nullable();
            $table->date('sp_date_of_issue')->nullable();
            $table->string('sp_person_status', 20)->nullable();
            $table->string('sp_gender', 10)->nullable();
            $table->string('sp_ethnic_group', 40)->nullable();
            $table->string('sp_disability', 20)->nullable();
            $table->string('sp_title', 10)->nullable();
            $table->string('sp_initials', 10)->nullable();
            $table->string('sp_tax_number', 20)->nullable();
            $table->string('sp_firstname', 80)->nullable();
            $table->string('sp_middlename', 80)->nullable();
            $table->string('sp_surname', 80)->nullable();
            $table->string('sp_known_as', 80)->nullable();
            $table->string('sp_mobile_phone', 20)->nullable();
            $table->string('sp_whatsapp_number', 20)->nullable();
            $table->string('sp_office_phone', 20)->nullable();
            $table->string('sp_other_phone', 20)->nullable();
            $table->string('sp_email', 120)->nullable();
            $table->string('sp_accounts_email', 120)->nullable();
            $table->string('sp_country', 80)->nullable();
            $table->string('sp_country_code', 10)->nullable();
            $table->string('sp_nationality', 80)->nullable();
            $table->date('sp_date_of_death')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('created_by', 80)->nullable();
            $table->string('id_front_image', 255)->nullable();
            $table->string('id_back_image', 255)->nullable();
            $table->string('green_book_image', 255)->nullable();
            $table->string('update_image', 255)->nullable();
            $table->string('passport_image', 255)->nullable();
            $table->string('signature_image', 255)->nullable();
            $table->string('poa_image', 255)->nullable();
            $table->string('banking_image', 255)->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->string('updated_by', 100)->nullable();
            $table->string('profile_photo', 255)->nullable();
             $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cims_persons');
    }
};
