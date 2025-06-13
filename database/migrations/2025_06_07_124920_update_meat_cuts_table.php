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
            // Drop old columns if they exist
            if (Schema::hasColumn('meat_cuts', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('meat_cuts', 'price_per_kg')) {
                $table->dropColumn('price_per_kg');
            }

            // Add new columns if they don't exist
            if (!Schema::hasColumn('meat_cuts', 'cut_type')) {
                $table->string('cut_type')->after('animal_type');
            }
            if (!Schema::hasColumn('meat_cuts', 'default_price_per_kg')) {
                $table->decimal('default_price_per_kg', 10, 2)->after('cut_type');
            }
            if (!Schema::hasColumn('meat_cuts', 'is_available')) {
                $table->boolean('is_available')->default(true)->after('quantity');
            }
            if (!Schema::hasColumn('meat_cuts', 'minimum_stock_level')) {
                $table->integer('minimum_stock_level')->default(10)->after('is_available');
            }
            if (!Schema::hasColumn('meat_cuts', 'image_path')) {
                $table->string('image_path')->nullable()->after('minimum_stock_level');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meat_cuts', function (Blueprint $table) {
            // Remove new columns if they exist
            $columns = [
                'cut_type',
                'default_price_per_kg',
                'is_available',
                'minimum_stock_level',
                'image_path'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('meat_cuts', $column)) {
                    $table->dropColumn($column);
                }
            }

            // Restore old columns if they don't exist
            if (!Schema::hasColumn('meat_cuts', 'price_per_kg')) {
                $table->decimal('price_per_kg', 10, 2);
            }
            if (!Schema::hasColumn('meat_cuts', 'status')) {
                $table->string('status');
            }
        });
    }
};
