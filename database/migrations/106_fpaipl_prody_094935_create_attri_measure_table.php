<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attrikeys', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Color
            $table->string('slug'); // color
            $table->text('detail')->nullable(); // red, blue, green
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('attrivals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attrikey_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->text('value'); // red, blue, green
            $table->text('detail')->nullable(); // red, blue, green
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('product_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('attrikey_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('attrival_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->unique(['product_id', 'attrikey_id', 'attrival_id']);
            $table->timestamps();
        });

        Schema::create('measurekeys', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Shoulder, Chest, Waist, Hip, Length, Sleeve
            $table->string('slug'); // shoulder, chest, waist, hip, length, sleeve
            $table->string('unit'); // cm, inch
            $table->text('detail')->nullable(); // red, blue, green
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('measurevals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('measurekey_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->text('value'); // 10, 20, 30
            $table->text('detail')->nullable(); // red, blue, green
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('product_measurements', function (Blueprint $table) {
            $table->id();
            // Ensures referential integrity for the product_id
            $table->foreignId('product_id')
                  ->constrained()
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            
            // Ensures referential integrity for the measurekey_id
            $table->foreignId('measurekey_id')
                  ->constrained()
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            
            // Ensures referential integrity for the measureval_id
            $table->foreignId('measureval_id')
                  ->constrained()
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            
            // Enforces the unique combination of product_id, measurekey_id, and measureval_id
            $table->unique(['product_id', 'measurekey_id', 'measureval_id'], 'product_measurement_keyval');
            
            $table->timestamps();
        });
            
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_measurements');
        Schema::dropIfExists('measurevals');
        Schema::dropIfExists('measurekeys');
        Schema::dropIfExists('product_attributes');
        Schema::dropIfExists('attrivals');
        Schema::dropIfExists('attrikeys');
    }
};
