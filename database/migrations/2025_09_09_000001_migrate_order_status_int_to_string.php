<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ensure column is string to support string-backed enum cast
        if (Schema::hasColumn('orders', 'order_status')) {
            // Use raw SQL to avoid doctrine/dbal requirement for change()
            $connectionDriver = DB::getDriverName();
            if ($connectionDriver === 'mysql') {
                DB::statement('ALTER TABLE `orders` MODIFY `order_status` VARCHAR(20) NOT NULL');
            } elseif ($connectionDriver === 'pgsql') {
                DB::statement('ALTER TABLE orders ALTER COLUMN order_status TYPE VARCHAR(20)');
                DB::statement('ALTER TABLE orders ALTER COLUMN order_status SET NOT NULL');
            } else {
                // Fallback to Schema change for other drivers
                Schema::table('orders', function (Blueprint $table) {
                    $table->string('order_status')->nullable(false)->change();
                });
            }
        }

        // Migrate existing numeric values to string values expected by App\Enums\OrderStatus
        // 0 -> 'pending', 1 -> 'complete', anything else -> 'pending'
        DB::table('orders')
            ->where('order_status', '0')
            ->update(['order_status' => 'pending']);

        DB::table('orders')
            ->where('order_status', '1')
            ->update(['order_status' => 'complete']);

        // Handle NULLs or unexpected values like '2', '', etc.
        DB::table('orders')
            ->whereNotIn('order_status', ['pending', 'complete', 'cancelled'])
            ->orWhereNull('order_status')
            ->update(['order_status' => 'pending']);
    }

    public function down(): void
    {
        // Best-effort revert to integers (pending -> 0, complete -> 1)
        if (Schema::hasColumn('orders', 'order_status')) {
            // Normalize to integers first
            DB::table('orders')
                ->where('order_status', 'pending')
                ->update(['order_status' => 0]);

            DB::table('orders')
                ->where('order_status', 'complete')
                ->update(['order_status' => 1]);

            DB::table('orders')
                ->where('order_status', 'cancelled')
                ->update(['order_status' => 0]);

            Schema::table('orders', function (Blueprint $table) {
                $table->tinyInteger('order_status')->change();
            });
        }
    }
};


