<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientMasterTable extends Migration
{
    public function up()
    {
        Schema::create('client_master', function (Blueprint $table) {
            $table->id('client_id');

            // Company Details
            $table->string('company_name', 255);
            $table->string('company_reg_number', 50)->nullable();
            $table->string('company_type', 50)->nullable();
            $table->date('company_reg_date')->nullable();
            $table->string('financial_year_end', 20)->nullable();
            $table->string('trading_name', 255)->nullable();
            $table->string('client_code', 50)->unique();
            $table->string('bizportal_number', 100)->nullable();
            $table->string('month_no', 10)->nullable();
            $table->integer('number_of_directors')->nullable();
            $table->integer('number_of_shares')->nullable();
            $table->string('share_type_name', 255)->nullable();

            // Income Tax Registration
            $table->string('tax_number', 50)->nullable();
            $table->date('tax_reg_date')->nullable();
            $table->string('cipc_annual_returns', 20)->nullable();

            // Payroll Registration
            $table->string('paye_number', 50)->nullable();
            $table->string('sdl_number', 50)->nullable();
            $table->string('uif_number', 50)->nullable();
            $table->string('dept_labour_number', 50)->nullable();
            $table->string('wca_coida_number', 50)->nullable();
            $table->date('payroll_liability_date')->nullable();

            // VAT Registration
            $table->string('vat_number', 50)->nullable();
            $table->date('vat_reg_date')->nullable();
            $table->string('vat_return_cycle', 20)->nullable();
            $table->string('vat_cycle', 255)->nullable();
            $table->date('vat_effect_from')->nullable();

            // Contact Information
            $table->string('phone_business', 20)->nullable();
            $table->string('phone_mobile', 20)->nullable();
            $table->string('phone_whatsapp', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('website', 150)->nullable();

            // SARS Representative (Public Officer)
            $table->string('sars_rep_first_name', 100)->nullable();
            $table->string('sars_rep_middle_name', 100)->nullable();
            $table->string('sars_rep_surname', 100)->nullable();
            $table->string('sars_rep_initial', 10)->nullable();
            $table->string('sars_rep_title', 20)->nullable();
            $table->string('sars_rep_gender', 10)->nullable();
            $table->string('sars_rep_id_number', 20)->nullable();
            $table->string('sars_rep_id_type', 30)->nullable();
            $table->date('sars_rep_id_issue_date')->nullable();
            $table->string('sars_rep_tax_number', 20)->nullable();
            $table->string('sars_rep_position', 100)->nullable();
            $table->date('sars_rep_date_registered')->nullable();

            // SARS E-Filing Login Details
            $table->string('sars_login', 100)->nullable();
            $table->string('sars_password', 255)->nullable();
            $table->string('sars_otp_mobile', 20)->nullable();
            $table->string('sars_otp_email', 100)->nullable();

            // Banking Details
            $table->string('bank_account_holder', 150)->nullable();
            $table->string('bank_account_number', 30)->nullable();
            $table->string('bank_account_type', 30)->nullable();
            $table->string('bank_name', 100)->nullable();
            $table->string('bank_branch_code', 20)->nullable();

            // Director Personal Details
            $table->string('director_first_name', 100)->nullable();
            $table->string('director_middle_name', 100)->nullable();
            $table->string('director_surname', 100)->nullable();
            $table->string('director_initial', 10)->nullable();
            $table->string('director_title', 20)->nullable();
            $table->string('director_gender', 10)->nullable();
            $table->string('director_id_number', 20)->nullable();
            $table->string('director_id_type', 30)->nullable();
            $table->date('director_id_issue_date')->nullable();
            $table->string('director_marital_status', 30)->nullable();
            $table->string('director_marriage_type', 30)->nullable();
            $table->date('director_marriage_date')->nullable();

            // Marital Status (Partner)
            $table->string('partner_first_name', 100)->nullable();
            $table->string('partner_middle_name', 100)->nullable();
            $table->string('partner_surname', 100)->nullable();
            $table->string('partner_title', 20)->nullable();
            $table->string('partner_gender', 10)->nullable();
            $table->string('partner_id_number', 20)->nullable();
            $table->string('partner_id_type', 30)->nullable();
            $table->date('partner_id_issue_date')->nullable();

            // Photo & Signature
            $table->string('photo_path', 255)->nullable();
            $table->string('signature_path', 255)->nullable();
            $table->boolean('cor_certificate_uploaded')->default(0)->comment('Checkbox: COR 14.3 Certificate uploaded');
            $table->boolean('income_tax_notice_registration_uploaded')->default(0)->comment('Checkbox: SARS INCOME TAX - Notice of Registration');
            $table->boolean('payroll_notice_registration_uploaded')->default(0)->comment('Checkbox: Payroll Notice of Registration');
            $table->boolean('vat_registration_uploaded')->default(0)->comment('Checkbox: VAT Registration');
            $table->boolean('sars_representative_uploaded')->default(0)->comment('Checkbox: SARS Representative');
            $table->boolean('confirmation_of_banking_uplaoded')->default(0)->comment('Checkbox: Confirmation of Banking');
            // Photo & Signature
            // $table->string('income_tax_notice_registration_photo_path', 255)->nullable();
            // $table->string('income_tax_notice_registration_signature_path', 255)->nullable();
            // $table->boolean('income_tax_notice_registration_upload')->default(0)->comment('Checkbox: Income Tax Notice Registration uploaded');

            // Status & Audit
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('company_name');
            $table->index('client_code');
            $table->index('tax_number');
            $table->index('vat_number');
            $table->index('is_active');
        });

        // Linking table for addresses
        Schema::create('client_master_addresses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('address_id');
            $table->string('unit_number', 50)->nullable();
            $table->string('complex_name', 150)->nullable();
            $table->string('street_number', 50)->nullable();
            $table->string('street_name', 150)->nullable();
            $table->string('suburb', 150)->nullable();
            $table->string('city', 150)->nullable();
            $table->string('postal_code', 50)->nullable();
            $table->string('province', 100)->nullable();
            $table->string('municipality', 150)->nullable();
            $table->string('ward', 100)->nullable();
            $table->string('country', 100)->default('South Africa');
            $table->string('long_address', 255);
            $table->text('google_address')->nullable();
            $table->string('latitude', 50)->nullable();
            $table->string('longitude', 50)->nullable();
            $table->text('map_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_checked');
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

           // Foreign keys with cascade delete
            $table->foreign('client_id')
                ->references('client_id')
                ->on('client_master')
                ->onDelete('cascade');

            $table->foreign('address_id')
                ->references('id')
                ->on('cims_addresses')
                ->onDelete('cascade');

            // Unique combination to prevent duplicates
            $table->unique(['client_id', 'address_id']);
        });

        // Audit trail table
        Schema::create('client_master_audit', function (Blueprint $table) {
            $table->id('audit_id');
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('action', 50);
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->timestamps();

            $table->foreign('client_id')->references('client_id')->on('client_master')->onDelete('cascade');
            $table->index('client_id');
            $table->index('action');
        });
    }

    public function down()
    {
        Schema::dropIfExists('client_master_audit');
        Schema::dropIfExists('client_master_addresses');
        Schema::dropIfExists('client_master');
    }
}
