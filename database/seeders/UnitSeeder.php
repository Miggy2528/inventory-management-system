<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = collect([
            [
                'name' => 'Kilogram',
                'slug' => 'kilogram',
                'short_code' => 'kg'
            ],
            [
                'name' => 'Gram',
                'slug' => 'gram',
                'short_code' => 'g'
            ],
            [
                'name' => 'Piece',
                'slug' => 'piece',
                'short_code' => 'pc'
            ],
            [
                'name' => 'Pack',
                'slug' => 'pack',
                'short_code' => 'pk'
            ],
            [
                'name' => 'Box',
                'slug' => 'box',
                'short_code' => 'bx'
            ]
        ]);

        $units->each(function ($unit) {
            Unit::insert($unit);
        });
    }
}
