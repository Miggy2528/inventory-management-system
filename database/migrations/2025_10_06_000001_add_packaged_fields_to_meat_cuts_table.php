<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('meat_cuts', function (Blueprint $table) {
            if (!Schema::hasColumn('meat_cuts', 'is_packaged')) {
                $table->boolean('is_packaged')->default(false)->after('default_price_per_kg');
            }
            if (!Schema::hasColumn('meat_cuts', 'package_price')) {
                $table->decimal('package_price', 10, 2)->nullable()->after('is_packaged');
            }

            // Relax restrictions when using packaged products
            if (Schema::hasColumn('meat_cuts', 'animal_type')) {
                $table->string('animal_type')->nullable()->change();
            }
            if (Schema::hasColumn('meat_cuts', 'cut_type')) {
                $table->string('cut_type')->nullable()->change();
            }
            if (Schema::hasColumn('meat_cuts', 'default_price_per_kg')) {
                $table->decimal('default_price_per_kg', 10, 2)->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('meat_cuts', function (Blueprint $table) {
            if (Schema::hasColumn('meat_cuts', 'package_price')) {
                $table->dropColumn('package_price');
            }
            if (Schema::hasColumn('meat_cuts', 'is_packaged')) {
                $table->dropColumn('is_packaged');
            }

            // Revert nullable changes if necessary (best effort; may vary by DB)
            if (Schema::hasColumn('meat_cuts', 'animal_type')) {
                $table->string('animal_type')->nullable(false)->change();
            }
            if (Schema::hasColumn('meat_cuts', 'cut_type')) {
                $table->string('cut_type')->nullable(false)->change();
            }
            if (Schema::hasColumn('meat_cuts', 'default_price_per_kg')) {
                $table->decimal('default_price_per_kg', 10, 2)->nullable(false)->change();
            }
        });
    }
};


