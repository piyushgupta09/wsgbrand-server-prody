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
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->string('sid')->unique();
            $table->string('category_name');
            $table->string('category_type');
            $table->string('name');
            $table->string('slug');
            $table->integer('price')->default(0);
            $table->longText('details')->nullable();
            $table->string('unit_name')->nullable();
            $table->string('unit_abbr')->nullable();
            $table->string('stock')->nullable();
            $table->json('stockItems')->nullable();
            $table->text('tags')->nullable();
            $table->foreignId('supplier_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
        });

        Schema::create('material_options', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // red
            $table->string('slug'); // red
            $table->string('code')->nullable(); // #ff0000
            $table->foreignId('material_id')->constrained()->onDelete('cascade');
            $table->unique(['material_id', 'slug']);
            $table->string('image')->nullable();
            $table->text('images')->nullable();
            $table->timestamps();
        });

        // What sizes are available in which product also how much quantity is required to make this size product of any color
        Schema::create('material_ranges', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('width'); // W48
            $table->string('length'); // L50
            $table->string('rate')->nullable(); // 100 - 120
            $table->string('source')->nullable(); // product sourcing info
            $table->string('quality')->nullable(); // product quality standard
            $table->string('other')->nullable(); // any other attribute
            $table->foreignId('material_id')->constrained()->onDelete('cascade');
            $table->unique(['material_id', 'width', 'length']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_ranges');
        Schema::dropIfExists('material_options');
        Schema::dropIfExists('materials');
    }
};
