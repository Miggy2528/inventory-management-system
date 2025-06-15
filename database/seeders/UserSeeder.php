<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = collect([
            [
                'name' => 'Admin',
                'username' => 'admin',
                'email' => 'admin@admin.com',
                'email_verified_at' => now(),
                'password' => bcrypt('password'),
                'role' => 'admin',
                'status' => 'active',
                'created_at' => now()
            ],
            [
                'name' => 'Staff',
                'username' => 'staff',
                'email' => 'staff@staff.com',
                'email_verified_at' => now(),
                'password' => bcrypt('password'),
                'role' => 'staff',
                'status' => 'active',
                'created_at' => now()
            ],
            [
                'name' => 'Customer',
                'username' => 'customer',
                'email' => 'customer@customer.com',
                'email_verified_at' => now(),
                'password' => bcrypt('password'),
                'role' => 'customer',
                'status' => 'active',
                'created_at' => now()
            ]
        ]);

        $users->each(function ($user){
            User::insert($user);
        });
    }
}
