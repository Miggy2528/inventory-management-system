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
        Schema::table('meat_cuts', function (Blueprint $table) {
            $table->integer('quantity')->default(0)->after('is_available');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meat_cuts', function (Blueprint $table) {
            $table->dropColumn('quantity');
        });
    }
}; 