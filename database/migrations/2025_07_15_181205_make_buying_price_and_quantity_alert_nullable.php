<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('buying_price')->nullable()->change();
            $table->integer('quantity_alert')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('buying_price')->nullable(false)->change();
            $table->integer('quantity_alert')->nullable(false)->change();
        });
    }
};
