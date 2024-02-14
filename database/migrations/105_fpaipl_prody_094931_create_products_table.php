<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * This migration creates the products table and its related tables.
 * 
 * products: Defines each product.
 * product_options: Defines available options(colors) for each product.
 * product_ranges: Defines available ranges(sizes) for each product.
 * product_materials: Associates each product with one or more materials. Diffrent materials required to make this product.
 * pomos: Associates each option(color) of a product (product_option_id) with a specific material option(color) (material_option_id).
 * pomocs: This table links the product range(size) (product_range_id) with .....complete this?
 * The quantity field in this table is meant to represent the quantity of material needed to produce given size of that product.
 * 
 * Re-write this based on updated information:?
 * 
 * With this setup, you are correctly tracking the consumption of each color fabric per size of each product. 
 * For example, you can check how much "red cotton" (a record in pro_opt_mat_opt) is used in the "small" size of a certain 
 * product (a record in product_ranges) by looking at the corresponding record in pomo_cpu. The quantity field in pomo_cpu 
 * will tell you how much of that "red cotton" is needed for the "small" size of that product.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create the products table
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('code')->unique();
            $table->longText('details')->nullable();

            $table->foreignId('brand_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('tax_id')->constrained()->onUpdate('cascade')->onDelete('cascade');

            $table->decimal('mrp', $precision = 8, $scale = 2)->default(0.00);
            $table->decimal('rate', $precision = 8, $scale = 2)->default(0.00);
            $table->integer('moq')->default(1);
            
            
            $table->string('status')->default('draft'); // to be deleted
            $table->boolean('active')->default(true); // decision locked
            $table->text('tags')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Which fabric is used in which product
        Schema::create('product_materials', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('grade')->default(0); // 0, 1, 2, 3, 4, 5
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('material_id')->constrained()->onDelete('cascade');
            $table->unique(['product_id', 'material_id', 'grade']);
            $table->timestamps();
            $table->softDeletes();
        });

        // What colors are available in which product
        Schema::create('product_options', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // red
            $table->string('slug'); // red
            $table->string('code')->nullable(); // #ff0000
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->unique(['product_id', 'slug']);
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // Which fabric color is used in which product color 
        // i.e. ProductOptionMaterialOption
        Schema::create('pomos', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('grade')->default(0);
            // red shirt
            $table->foreignId('product_option_id')->constrained()->onDelete('cascade');
            // red-cotton-striped
            $table->foreignId('material_option_id')->constrained()->onDelete('cascade');
            // Only one red-cotton-striped can be used in red shirt
            $table->unique(['product_option_id', 'material_option_id']);
            $table->foreignId('product_material_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // What sizes are available in which product also how much quantity is required to make this size product of any color
        Schema::create('product_ranges', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Small
            $table->string('slug'); // small
            $table->decimal('mrp', $precision = 8, $scale = 2)->default(0.00);
            $table->decimal('rate', $precision = 8, $scale = 2)->default(0.00);
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->unique(['product_id', 'slug']); // each product can have only one small size
            $table->boolean('active')->default(true); // enable/disable this size
            $table->timestamps();
            $table->softDeletes();
        });

        // How much fabric is required to make this size product of any color
        // i.e. ProductOptionMaterialRange
        Schema::create('pomrs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->float('cost')->default(0); 

            // To Make

            // a small size product (i.e. product-range)
            $table->foreignId('product_range_id')->constrained()->onDelete('cascade');

            // of this any color (i.e. product-option) we dont need to specify the color here

            // of this width (i.e. material-range)
            $table->foreignId('material_range_id')->constrained()->onDelete('cascade');

            // 2 (will be required to make this size product of any color) 
            $table->float('quantity')->default(0); // aka. FCPU (Fabric Consumption Per Unit)

            // meter (unit of the consumption)
            $table->string('unit')->nullable(); 

            // Saved for editing purpose
            $table->string('grade');
            $table->foreignId('product_material_id')->constrained()->onDelete('cascade');

            $table->unique(['product_range_id', 'material_range_id', 'name']);
            $table->timestamps();
        });
        
        DB::statement('ALTER TABLE products ADD FULLTEXT fulltext_index (tags)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pomrs');
        Schema::dropIfExists('product_ranges');
        Schema::dropIfExists('pomos');
        Schema::dropIfExists('product_options');
        Schema::dropIfExists('product_material');
        DB::statement('ALTER TABLE products DROP INDEX fulltext_index');
        Schema::dropIfExists('products');
    }
};
