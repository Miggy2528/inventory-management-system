<?php

namespace App\Console\Commands;

use App\Services\CustomerAuthService;
use Illuminate\Console\Command;

class CustomerAuthTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'customer:auth-test {action} {--email=} {--password=} {--name=} {--username=} {--phone=} {--address=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test customer authentication functions (register, login, logout)';

    protected $authService;

    public function __construct(CustomerAuthService $authService)
    {
        parent::__construct();
        $this->authService = $authService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');

        switch ($action) {
            case 'register':
                $this->testRegister();
                break;
            case 'login':
                $this->testLogin();
                break;
            case 'logout':
                $this->testLogout();
                break;
            default:
                $this->error('Invalid action. Use: register, login, or logout');
                return 1;
        }

        return 0;
    }

    /**
     * Test customer registration
     */
    private function testRegister()
    {
        $this->info('Testing Customer Registration...');

        $data = [
            'name' => $this->option('name') ?: 'Test Customer',
            'email' => $this->option('email') ?: 'test' . time() . '@example.com',
            'username' => $this->option('username') ?: 'testuser' . time(),
            'password' => $this->option('password') ?: 'password123',
            'password_confirmation' => $this->option('password') ?: 'password123',
            'phone' => $this->option('phone') ?: '09123456789',
            'address' => $this->option('address') ?: 'Test Address',
        ];

        $this->info('Registration data:');
        $this->table(['Field', 'Value'], [
            ['Name', $data['name']],
            ['Email', $data['email']],
            ['Username', $data['username']],
            ['Phone', $data['phone']],
            ['Address', $data['address']],
        ]);

        $result = $this->authService->createCustomerAccount($data);

        if ($result['success']) {
            $this->info('✅ Registration successful!');
            $this->info('Customer ID: ' . $result['customer']->id);
            $this->info('Token: ' . substr($result['token'], 0, 50) . '...');
            
            // Store token for logout test
            file_put_contents(storage_path('app/test_customer_token.txt'), $result['token']);
            file_put_contents(storage_path('app/test_customer_id.txt'), $result['customer']->id);
            
        } else {
            $this->error('❌ Registration failed: ' . $result['message']);
        }
    }

    /**
     * Test customer login
     */
    private function testLogin()
    {
        $this->info('Testing Customer Login...');

        $email = $this->option('email') ?: $this->ask('Enter email or username');
        $password = $this->option('password') ?: $this->secret('Enter password');

        $result = $this->authService->loginCustomer($email, $password);

        if ($result['success']) {
            $this->info('✅ Login successful!');
            $this->info('Customer ID: ' . $result['customer']->id);
            $this->info('Token: ' . substr($result['token'], 0, 50) . '...');
            
            // Store token for logout test
            file_put_contents(storage_path('app/test_customer_token.txt'), $result['token']);
            file_put_contents(storage_path('app/test_customer_id.txt'), $result['customer']->id);
            
        } else {
            $this->error('❌ Login failed: ' . $result['message']);
        }
    }

    /**
     * Test customer logout
     */
    private function testLogout()
    {
        $this->info('Testing Customer Logout...');

        $tokenFile = storage_path('app/test_customer_token.txt');
        $customerIdFile = storage_path('app/test_customer_id.txt');

        if (!file_exists($tokenFile) || !file_exists($customerIdFile)) {
            $this->error('❌ No stored token found. Please login first.');
            return;
        }

        $token = file_get_contents($tokenFile);
        $customerId = file_get_contents($customerIdFile);

        // Find customer by ID
        $customer = \App\Models\Customer::find($customerId);
        
        if (!$customer) {
            $this->error('❌ Customer not found.');
            return;
        }

        // Set the token for the request
        request()->headers->set('Authorization', 'Bearer ' . $token);

        $result = $this->authService->logoutCustomer($customer);

        if ($result['success']) {
            $this->info('✅ Logout successful!');
            
            // Clean up stored files
            unlink($tokenFile);
            unlink($customerIdFile);
            
        } else {
            $this->error('❌ Logout failed: ' . $result['message']);
        }
    }
} 