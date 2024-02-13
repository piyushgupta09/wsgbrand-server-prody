<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_decisions', function (Blueprint $table) {
            $table->id();
            $table->boolean('factory')->default(true); // Is this product made in our factory?
            $table->boolean('vendor')->default(false); // Is this product bought from a vendor?
            $table->boolean('market')->default(false); // Is this product bought from the market?
            
            $table->boolean('ecomm')->default(false); // Is this product sold online?
            $table->boolean('retail')->default(false); // Is this product sold offline?
            $table->boolean('inbulk')->default(true); // Is this product sold in bulk?
            $table->boolean('pos')->default(false); // Is this product sold offline?

            $table->boolean('pay_cod')->default(false); // Is this product paid online?
            $table->boolean('pay_part')->default(true); // Is this product paid online?
            $table->boolean('pay_half')->default(false); // Is this product paid online?
            $table->boolean('pay_full')->default(false); // Is this product paid online?
            
            $table->boolean('del_pick')->default(false); // Is this product paid online?
            $table->boolean('del_free')->default(false); // Is this product delivered online?
            $table->boolean('del_paid')->default(true); // Is this product delivered online?

            $table->boolean('locked')->default(false);
            $table->boolean('cost_locked')->default(false);
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // Decision 2.1: Define the fixed costs applicable across all products
        Schema::create('fixedcosts', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->decimal('amount', 14, 2); // Total overhead cost
            $table->decimal('capacity', 14, 2); // Total capacity or basis for apportionment (e.g., factory capacity in units)
            $table->decimal('rate', 10, 2); // Rate of overhead cost per unit of capacity
            $table->text('details')->nullable(); // Description of the overhead cost
            $table->boolean('active')->default(true);
            $table->text('tags')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Decision 2.2: Overheads applicable based on production stage
        Schema::create('overheads', function (Blueprint $table) {
            $table->id();
            $table->string('stage'); // Identifies the production stage (Cutting, Stitching, etc.)
            $table->string('name')->unique();
            $table->decimal('amount', 14, 2); // Total overhead cost for this stage
            $table->decimal('capacity', 14, 2); // Total capacity or basis for apportionment specific to this stage
            $table->decimal('rate', 10, 2); // Rate of overhead cost per unit of capacity for this stage
            $table->text('details')->nullable(); // Description of the overhead cost
            $table->boolean('active')->default(true);
            $table->text('tags')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Decision 2.3: Consumption of consumables used in production (e.g., Thread, Needle)
        Schema::create('consumables', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('unit')->default('unit'); // Unit of measure for this consumable (e.g., meter, piece)
            $table->decimal('rate', 10, 2)->default(1); // Cost per unit of consumable
            $table->text('details')->nullable(); // Description of the consumable
            $table->boolean('active')->default(true);
            $table->text('tags')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Decision 2.4: Associate products with overheads and consumables for cost allocation and tracking
        Schema::create('product_overhead', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('overhead_id')->constrained()->onDelete('cascade');
            $table->decimal('rate', 8, 2); // Specific overhead cost allocated to this product
            $table->decimal('ratio', 5, 2)->default(0.00); // Basis or percentage for overhead cost allocation
            $table->decimal('amount', 14, 2)->default(0.00); // Basis or percentage for overhead cost allocation
            $table->text('reasons')->nullable(); // Explanation for the chosen allocation basis
            $table->boolean('active')->default(true);
            $table->text('tags')->nullable();
            $table->timestamps();
        });
        
        // Decision 2.5: Associate products with consumables for cost allocation and tracking
        Schema::create('product_consumable', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('consumable_id')->constrained()->onDelete('cascade');
            $table->decimal('rate', 8, 2); // Quantity of the consumable used for this product
            $table->decimal('ratio', 5, 2); // Total cost of consumable allocated to this product
            $table->decimal('amount', 14, 2)->default(0.00); // Basis or percentage for consumable cost allocation
            $table->text('reasons')->nullable(); // Explanation for the quantity and cost allocation
            $table->boolean('active')->default(true);
            $table->text('tags')->nullable();
            $table->timestamps();
        });


        // Decision 3: Pricing Strategy

        // Decision 3.1: Define the pricing strategy for each product
        Schema::create('strategies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('value', 8, 2); // This can be a percentage or fixed value based on the 'adjustment_type'
            $table->string('type')->default('percentage'); // Determines if 'adjustment_value' is a percentage or fixed amount
            $table->text('details')->nullable(); // Additional details or conditions
            $table->boolean('active')->default(true);
            $table->text('tags')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Decision 3.2: Associate products with pricing strategies
        Schema::create('product_strategy', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('strategy_id')->constrained()->onDelete('cascade');
            $table->decimal('value', 8, 2); // This can be a percentage or fixed value based on the 'adjustment_type'
            $table->string('type')->default('percentage'); // Determines if 'adjustment_value' is a percentage or fixed amount
            $table->text('details')->nullable(); // Additional details or conditions
            $table->boolean('active')->default(true);
            $table->text('tags')->nullable();
            $table->timestamps();
        });

        // Decision 4: Discount Strategy
            
        // Decision 4.1: Define the discount strategy for each product
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('value', 8, 2); // This can be a percentage or fixed value based on the 'adjustment_type'
            $table->string('type')->default('percentage'); // Determines if 'adjustment_value' is a percentage or fixed amount
            $table->text('details')->nullable(); // Additional details or conditions

            $table->boolean('one_time')->default(false); // One-time discount
            $table->boolean('multi_time')->default(true); // Multi-time discount

            $table->boolean('on_quantity')->default(false); // Based on quantity
            $table->boolean('on_total')->default(false); // Based on total amount

            $table->boolean('on_account')->default(false); // Apply discount on the user account
            $table->boolean('on_checkout')->default(false); // Apply discount on the each checkout
            $table->boolean('on_product')->default(false); // Apply discount on the each product

            // If the discount is based on quantity, then the following fields are used
            $table->integer('min_quantity')->default(0); // Minimum quantity for discount
            $table->integer('max_quantity')->default(0); // Maximum quantity for discount

            // If the discount is based on total amount, then the following fields are used
            $table->decimal('min_total', 8, 2)->default(0.00); // Minimum total for discount
            $table->decimal('max_total', 8, 2)->default(0.00); // Maximum total for discount

            $table->boolean('active')->default(true);
            $table->text('tags')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Decision 4.2: Associate products with discount strategies
        Schema::create('product_discount', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('discount_id')->constrained()->onDelete('cascade');
            $table->decimal('value', 8, 2); // This can be a percentage or fixed value based on the 'adjustment_type'
            $table->string('type')->default('percentage'); // Determines if 'adjustment_value' is a percentage or fixed amount
            $table->text('details')->nullable(); // Additional details or conditions

            $table->boolean('one_time')->default(false); // One-time discount
            $table->boolean('multi_time')->default(true); // Multi-time discount

            $table->boolean('on_quantity')->default(false); // Based on quantity
            $table->boolean('on_total')->default(false); // Based on total amount

            $table->boolean('on_account')->default(false); // Apply discount on the user account
            $table->boolean('on_checkout')->default(false); // Apply discount on the each checkout
            $table->boolean('on_product')->default(false); // Apply discount on the each product

            // If the discount is based on quantity, then the following fields are used
            $table->integer('min_quantity')->default(0); // Minimum quantity for discount
            $table->integer('max_quantity')->default(0); // Maximum quantity for discount

            // If the discount is based on total amount, then the following fields are used
            $table->decimal('min_total', 8, 2)->default(0.00); // Minimum total for discount
            $table->decimal('max_total', 8, 2)->default(0.00); // Maximum total for discount

            $table->boolean('active')->default(true);
            $table->text('tags')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_discount');
        Schema::dropIfExists('discounts');
        Schema::dropIfExists('product_strategy');
        Schema::dropIfExists('strategies');
        Schema::dropIfExists('product_consumable');
        Schema::dropIfExists('product_overhead');
        Schema::dropIfExists('consumables');
        Schema::dropIfExists('overheads');
        Schema::dropIfExists('fixedcosts');
        Schema::dropIfExists('product_platforms');
        Schema::dropIfExists('platforms');
        Schema::dropIfExists('product_decisions');
        Schema::dropIfExists('decisions');
    }
};



/*
    // add various conditions for discount
    $table->boolean('is_active')->default(true);
    $table->boolean('is_percentage')->default(true);
    $table->boolean('is_fixed')->default(false);
    $table->boolean('is_limited')->default(false);
    $table->boolean('is_unlimited')->default(true);
    $table->boolean('is_valid')->default(true);
    $table->boolean('is_invalid')->default(false);
    $table->boolean('is_expired')->default(false);
    $table->boolean('is_not_expired')->default(true);
    $table->boolean('is_redeemable')->default(true);
    $table->boolean('is_not_redeemable')->default(false);
    $table->boolean('is_one_time')->default(false);
    $table->boolean('is_multi_time')->default(true);
    $table->boolean('is_for_all')->default(true);
    $table->boolean('is_for_selected')->default(false);
    $table->boolean('is_for_new')->default(false);
    $table->boolean('is_for_existing')->default(true);
    $table->boolean('is_for_first')->default(false);
    $table->boolean('is_for_repeat')->default(true);
    $table->boolean('is_for_minimum')->default(false);
    $table->boolean('is_for_maximum')->default(false);
    $table->boolean('is_for_specific')->default(false);
    $table->boolean('is_for_all_products')->default(true);
    $table->boolean('is_for_selected_products')->default(false);
    $table->boolean('is_for_all_categories')->default(true);
    $table->boolean('is_for_selected_categories')->default(false);
    $table->boolean('is_for_all_brands')->default(true);
    $table->boolean('is_for_selected_brands')->default(false);
    $table->boolean('is_for_all_users')->default(true);
    $table->boolean('is_for_selected_users')->default(false);
    $table->boolean('is_for_all_roles')->default(true);
    $table->boolean('is_for_selected_roles')->default(false);
    $table->boolean('is_for_all_countries')->default(true);
    $table->boolean('is_for_selected_countries')->default(false);
    $table->boolean('is_for_all_states')->default(true);
    $table->boolean('is_for_selected_states')->default(false);
    $table->boolean('is_for_all_cities')->default(true);
    $table->boolean('is_for_selected_cities')->default(false);
    $table->boolean('is_for_all_areas')->default(true);
    $table->boolean('is_for_selected_areas')->default(false);
    $table->boolean('is_for_all_stores')->default(true);
    $table->boolean('is_for_selected_stores')->default(false);
    $table->boolean('is_for_all_warehouses')->default(true);
    $table->boolean('is_for_selected_warehouses')->default(false);
    $table->boolean('is_for_all_channels')->default(true);
    $table->boolean('is_for_selected_channels')->default(false);
    $table->boolean('is_for_all_markets')->default(true);
    $table->boolean('is_for_selected_markets')->default(false);
    $table->boolean('is_for_all_platforms')->default(true);
    $table->boolean('is_for_selected_platforms')->default(false);
    $table->boolean('is_for_all_gateways')->default(true);
    $table->boolean('is_for_selected_gateways')->default(false);
    $table->boolean('is_for_all_methods')->default(true);
    $table->boolean('is_for_selected_methods')->default(false);
    $table->boolean('is_for_all_times')->default(true);
    $table->boolean('is_for_selected_times')->default(false);
    $table->boolean('is_for_all_days')->default(true);
    $table->boolean('is_for_selected_days')->default(false);
    $table->boolean('is_for_all_dates')->default(true);
    $table->boolean('is_for_selected_dates')->default(false);
    $table->boolean('is_for_all_months')->default(true);
    $table->boolean('is_for_selected_months')->default(false);
    $table->boolean('is_for_all_years')->default(true);
    $table->boolean('is_for_selected_years')->default(false);
    $table->boolean('is_for_all_seasons')->default(true);
    $table->boolean('is_for_selected_seasons')->default(false);
    $table->boolean('is_for_all_holidays')->default(true);
    $table->boolean('is_for_selected_holidays')->default(false);
    $table->boolean('is_for_all_weekdays')->default(true);
    $table->boolean('is_for_selected_weekdays')->default(false);
    $table->boolean('is_for_all_weekends')->default(true);
    $table->boolean('is_for_selected_weekends')->default(false);
    $table->boolean('is_for_all_weeks')->default(true);
    $table->boolean('is_for_selected_weeks')->default(false);
    $table->boolean('is_for_all_months')->default(true);
    $table->boolean('is_for_selected_months')->default(false);
    $table->boolean('is_for_all_quarters')->default(true);
    $table->boolean('is_for_selected_quarters')->default(false);
    $table->boolean('is_for_all_halves')->default(true);
    $table->boolean('is_for_selected_halves')->default(false);
    $table->boolean('is_for_all_years')->default(true);
    $table->boolean('is_for_selected_years')->default(false);
    $table->boolean('is_for_all_decades')->default(true);
    $table->boolean('is_for_selected_decades')->default(false);
    $table->boolean('is_for_all_centuries')->default(true);
    $table->boolean('is_for_selected_centuries')->default(false);
    $table->boolean('is_for_all_millenniums')->default(true);
    $table->boolean('is_for_selected_millenniums')->default(false);
    $table->boolean('is_for_all_eras')->default(true);
    $table->boolean('is_for_selected_eras')->default(false);
    $table->boolean('is_for_all_periods')->default(true);
    $table->boolean('is_for_selected_periods')->default(false);
    $table->boolean('is_for_all_phases')->default(true);
    $table->boolean('is_for_selected_phases')->default(false);
    $table->boolean('is_for_all_epochs')->default(true);
    $table->boolean('is_for_selected_epochs')->default(false);
    $table->boolean('is_for_all_ages')->default(true);
    $table->boolean('is_for_selected_ages')->default(false);
    $table->boolean('is_for_all_generations')->default(true);
    $table->boolean('is_for_selected_generations')->default(false);
    $table->boolean('is_for_all_periods')->default(true);
    $table->boolean('is_for_selected_periods')->default(false);
    $table->boolean('is_for_all_phases')->default(true);
    $table->boolean('is_for_selected_phases')->default(false);
    $table->boolean('is_for_all_epochs')->default(true);
    $table->boolean('is_for_selected_epochs')->default(false);
    $table->boolean('is_for_all_ages')->default(true);
    $table->boolean('is_for_selected_ages')->default(false);
    $table->boolean('is_for_all_generations')->default(true);
    $table->boolean('is_for_selected_generations')->default(false);
    $table->boolean('is_for_all_periods')->default(true);
    $table->boolean('is_for_selected_periods')->default(false);
    $table->boolean('is_for_all_phases')->default(true);
    $table->boolean('is_for_selected_phases')->default(false);
    $table->boolean('is_for_all_epochs')->default(true);
    $table->boolean('is_for_selected_epochs')->default(false);
    $table->boolean('is_for_all_ages')->default(true);
    $table->boolean('is_for_selected_ages')->default(false);
    $table->boolean('is_for_all_generations')->default(true);
    $table->boolean('is_for_selected_generations')->default(false);
*/