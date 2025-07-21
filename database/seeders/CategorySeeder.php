<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = collect([
            [
                'id'    => 1,
                'name'  => 'Raw Meat',
                'slug'  => 'raw-meat',
                'created_at' => now()
            ],
            [
                'id'    => 2,
                'name'  => 'Processed Meat',
                'slug'  => 'processed-meat',
                'created_at' => now()
            ],
            [
                'id'    => 3,
                'name'  => 'Frozen Meat',
                'slug'  => 'frozen-meat',
                'created_at' => now()
            ]
        ]);

        $categories->each(function ($category) {
            Category::insert($category);
        });
    }
}
