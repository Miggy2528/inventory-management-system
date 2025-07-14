<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample customers for testing
        $customers = [
            [
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
                'username' => 'johndoe',
                'password' => Hash::make('password123'),
                'phone' => '09123456789',
                'address' => '123 Main Street, Quezon City, Metro Manila',
                'status' => 'active',
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane.smith@example.com',
                'username' => 'janesmith',
                'password' => Hash::make('password123'),
                'phone' => '09187654321',
                'address' => '456 Oak Avenue, Makati City, Metro Manila',
                'status' => 'active',
            ],
            [
                'name' => 'Mike Johnson',
                'email' => 'mike.johnson@example.com',
                'username' => 'mikejohnson',
                'password' => Hash::make('password123'),
                'phone' => '09234567890',
                'address' => '789 Pine Road, Taguig City, Metro Manila',
                'status' => 'active',
            ],
        ];

        foreach ($customers as $customerData) {
            Customer::create($customerData);
        }

        $this->command->info('Sample customers created successfully!');
    }
} 