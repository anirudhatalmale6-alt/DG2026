<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Quote header
        Schema::create('cims_tyredash_quotes', function (Blueprint $table) {
            $table->id();
            $table->string('quote_number', 30)->unique();
            $table->string('customer_order_ref', 100)->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('vehicle_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('salesman_id')->nullable()->comment('User ID of salesman');
            $table->string('salesman_name', 150)->nullable();
            $table->date('quote_date');
            $table->date('valid_until')->nullable();
            $table->string('status', 20)->default('draft')->comment('draft, sent, accepted, declined, expired, invoiced');
            $table->text('customer_comment')->nullable();
            $table->text('internal_notes')->nullable();
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('customer_id')->references('id')->on('cims_tyredash_customers')->onDelete('set null');
            $table->foreign('vehicle_id')->references('id')->on('cims_tyredash_vehicles')->onDelete('set null');
            $table->foreign('branch_id')->references('id')->on('cims_tyredash_branches')->onDelete('set null');
            $table->index('quote_number');
            $table->index('status');
            $table->index('quote_date');
            $table->index('branch_id');
            $table->index('salesman_id');
        });

        // Quote options (up to 5 tyre options per quote)
        Schema::create('cims_tyredash_quote_options', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quote_id');
            $table->tinyInteger('option_number')->comment('1-5');
            $table->unsignedBigInteger('product_id');
            $table->integer('quantity')->default(4);
            $table->decimal('unit_cost', 15, 2)->default(0)->comment('Cost price at time of quote');
            $table->decimal('unit_price', 15, 2)->default(0)->comment('Sell price incl VAT');
            $table->decimal('markup_pct', 5, 2)->default(20.00);
            $table->decimal('discount_pct', 5, 2)->default(0);
            $table->decimal('line_total', 15, 2)->default(0);
            $table->boolean('is_selected')->default(false)->comment('Customer selected this option');
            $table->timestamps();

            $table->foreign('quote_id')->references('id')->on('cims_tyredash_quotes')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('cims_tyredash_products')->onDelete('restrict');
            $table->unique(['quote_id', 'option_number']);
            $table->index('quote_id');
        });

        // Quote services (shared services attached to a quote)
        Schema::create('cims_tyredash_quote_services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quote_id');
            $table->unsignedBigInteger('service_id');
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('line_total', 15, 2)->default(0);
            $table->timestamps();

            $table->foreign('quote_id')->references('id')->on('cims_tyredash_quotes')->onDelete('cascade');
            $table->foreign('service_id')->references('id')->on('cims_tyredash_services')->onDelete('restrict');
            $table->index('quote_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cims_tyredash_quote_services');
        Schema::dropIfExists('cims_tyredash_quote_options');
        Schema::dropIfExists('cims_tyredash_quotes');
    }
};
