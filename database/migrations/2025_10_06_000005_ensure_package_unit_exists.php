<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!DB::table('units')->where('name', 'Package')->exists()) {
            DB::table('units')->insert([
                'name' => 'Package',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('units')->where('name', 'Package')->delete();
    }
};


