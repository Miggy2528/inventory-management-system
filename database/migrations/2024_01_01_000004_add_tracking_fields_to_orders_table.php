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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('tracking_number')->nullable()->after('invoice_no');
            $table->text('cancellation_reason')->nullable()->after('due');
            $table->timestamp('cancelled_at')->nullable()->after('cancellation_reason');
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->after('cancelled_at');
            $table->timestamp('estimated_delivery')->nullable()->after('cancelled_by');
            $table->text('delivery_notes')->nullable()->after('estimated_delivery');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['cancelled_by']);
            $table->dropColumn(['tracking_number', 'cancellation_reason', 'cancelled_at', 'cancelled_by', 'estimated_delivery', 'delivery_notes']);
        });
    }
}; 