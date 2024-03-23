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
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('wsg_id')->nullable();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('tagline')->nullable();
            $table->text('description')->nullable();
            $table->string('website')->nullable();
            $table->string('email')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('contact_person')->nullable();
            $table->boolean('active')->default(1);
            $table->text('tags')->nullable();
            $table->json('images')->nullable();
            $table->timestamps();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('wsg_id')->nullable();
            $table->unsignedBigInteger('wsg_parent_id')->nullable();
            $table->foreignId('parent_id')
                  ->nullable()
                  ->constrained('categories')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('info')->nullable();
            $table->text('tags')->nullable();
            $table->integer('order')->default(0);
            // it must be child of root for display
            $table->boolean('display')->default(false);
            $table->boolean('active')->default(true);
            $table->json('images')->nullable();
            $table->timestamps();
        });

        Schema::create('collections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('wsg_id')->nullable();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('type');
            $table->boolean('active')->default(true);
            $table->tinyInteger('order')->default(0);
            $table->text('tags')->nullable();
            $table->text('info')->nullable();
            $table->json('images')->nullable();
            $table->timestamps();
        });

        Schema::create('taxes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('wsg_id')->nullable();
            $table->string('name');
            $table->text('tags')->nullable();
            $table->string('hsncode')->unique();
            $table->text('description')->nullable();
            $table->decimal('gstrate', 8, 2)->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('monaal_id')->nullable();
            $table->string('name')->nullable();
            $table->string('names')->nullable();
            $table->string('abbr')->nullable();
            $table->string('abbrs')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
        Schema::dropIfExists('taxes');
        Schema::dropIfExists('brands');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('collections');
    }
};
