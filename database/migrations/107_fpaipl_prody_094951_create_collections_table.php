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
        Schema::create('collections', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('type');
            $table->boolean('active')->default(true);
            $table->tinyInteger('order')->default(0);
            $table->text('tags')->nullable();
            $table->text('info')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('collection_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collection_id')->constrained();
            $table->foreignId('product_id')->constrained();
            $table->foreignId('product_option_id')->constrained();
            $table->unique(['collection_id','product_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collection_product');
        Schema::dropIfExists('collections');
    }
};
