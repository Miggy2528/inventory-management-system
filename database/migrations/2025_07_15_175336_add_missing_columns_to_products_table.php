<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('price_per_kg', 10, 2)->nullable();
            $table->decimal('total_weight', 10, 2)->nullable();
            $table->date('processing_date')->nullable();
            $table->string('source')->nullable();
            $table->string('grade')->nullable();
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'price_per_kg',
                'total_weight',
                'processing_date',
                'source',
                'grade'
            ]);
        });
    }
};
