<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class TestAuthGuards extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:auth-guards';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test authentication guards to ensure proper separation';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Authentication Guards...');
        $this->newLine();

        // Test 1: Check if customers exist
        $this->info('1. Checking Customer Records:');
        $customers = Customer::all(['id', 'name', 'email', 'role']);
        if ($customers->count() > 0) {
            $this->table(['ID', 'Name', 'Email', 'Role'], $customers->toArray());
        } else {
            $this->warn('No customers found in database.');
        }
        $this->newLine();

        // Test 2: Check if users exist
        $this->info('2. Checking User Records:');
        $users = User::all(['id', 'name', 'email', 'role']);
        if ($users->count() > 0) {
            $this->table(['ID', 'Name', 'Email', 'Role'], $users->toArray());
        } else {
            $this->warn('No users found in database.');
        }
        $this->newLine();

        // Test 3: Test guard configurations
        $this->info('3. Testing Guard Configurations:');
        
        // Test web guard (should use User model)
        $this->line('Web Guard Provider: ' . config('auth.guards.web.provider'));
        $this->line('Web Guard Model: ' . config('auth.providers.users.model'));
        
        // Test web_customer guard (should use Customer model)
        $this->line('Web Customer Guard Provider: ' . config('auth.guards.web_customer.provider'));
        $this->line('Web Customer Guard Model: ' . config('auth.providers.customers.model'));
        
        // Test customer guard (should use Customer model)
        $this->line('Customer Guard Provider: ' . config('auth.guards.customer.provider'));
        $this->line('Customer Guard Model: ' . config('auth.providers.customers.model'));
        
        $this->newLine();

        // Test 4: Verify model instances
        $this->info('4. Testing Model Instances:');
        
        if ($customers->count() > 0) {
            $customer = $customers->first();
            $this->line('Customer Instance: ' . get_class($customer));
            $this->line('Customer Role: ' . ($customer->role ?? 'No role field'));
            $this->line('Customer isActive(): ' . ($customer->isActive() ? 'Yes' : 'No'));
        }
        
        if ($users->count() > 0) {
            $user = $users->first();
            $this->line('User Instance: ' . get_class($user));
            $this->line('User Role: ' . ($user->role ?? 'No role field'));
            $this->line('User isActive(): ' . ($user->isActive() ? 'Yes' : 'No'));
        }
        
        $this->newLine();

        $this->info('✅ Authentication guard test completed!');
        $this->info('Customers should use web_customer guard and have role="customer"');
        $this->info('Admin/Staff should use web guard and have role="admin" or "staff"');
    }
} 