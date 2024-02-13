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
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // meter
            $table->string('names')->nullable(); // meters
            $table->string('abbr')->nullable(); // mtr
            $table->string('abbrs')->nullable(); // mtrs
            $table->boolean('active')->default(true);
            $table->text('tags')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('taxes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('hsncode')->nullable();
            $table->float('gstrate')->default(0.05);
            $table->boolean('active')->default(true);
            $table->text('tags')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('sid')->unique();
            $table->string('name');
            $table->string('address')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('type')->default('material-supplier'); // material-supplier, service-provider, product-supplier
            $table->json('apis')->nullable();
            $table->longText('details')->nullable();
            $table->boolean('active')->default(1);
            $table->text('tags')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
        Schema::dropIfExists('taxations');
        Schema::dropIfExists('suppliers');
    }
};
