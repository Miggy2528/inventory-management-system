<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends \Illuminate\Database\Migrations\Migration {
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Make sure doctrine/dbal is installed to allow column change
            $table->string('order_status')->change();
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->integer('order_status')->change(); // Or original type
        });
    }
};
