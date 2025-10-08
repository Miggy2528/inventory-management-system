<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'price_per_package')) {
                $table->decimal('price_per_package', 10, 2)->nullable()->after('price_per_kg');
            }
            if (!Schema::hasColumn('products', 'is_sold_by_package')) {
                $table->boolean('is_sold_by_package')->default(false)->after('price_per_package');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'is_sold_by_package')) {
                $table->dropColumn('is_sold_by_package');
            }
            if (Schema::hasColumn('products', 'price_per_package')) {
                $table->dropColumn('price_per_package');
            }
        });
    }
};


