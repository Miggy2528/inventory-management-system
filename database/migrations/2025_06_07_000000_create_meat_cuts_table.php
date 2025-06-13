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
        Schema::create('meat_cuts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('animal_type'); // e.g., Beef, Pork, Lamb
            $table->string('cut_type'); // e.g., Prime, Choice, Select
            $table->text('description')->nullable();
            $table->decimal('default_price_per_kg', 10, 2);
            $table->string('image_path')->nullable();
            $table->boolean('is_available')->default(true);
            $table->integer('minimum_stock_level');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meat_cuts');
    }
}; 