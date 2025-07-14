<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\CustomerAuthLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;
use Carbon\Carbon;

class CustomerAuthService
{
    /**
     * Create a new customer account
     * 
     * @param array $data
     * @return array
     */
    public function createCustomerAccount(array $data): array
    {
        try {
            DB::beginTransaction();

            // Create customer
            $customer = Customer::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'username' => $data['username'],
                'password' => Hash::make($data['password']),
                'phone' => $data['phone'],
                'address' => $data['address'],
                'status' => 'active',
                'role' => 'customer',
            ]);

            // Log the account creation
            CustomerAuthLog::create([
                'customer_id' => $customer->id,
                'action' => 'account_created',
                'ip_address' => request()->ip() ?? 'unknown',
                'user_agent' => request()->userAgent() ?? 'unknown',
                'details' => [
                    'email' => $customer->email,
                    'username' => $customer->username,
                    'created_at' => now()->toDateTimeString(),
                ],
            ]);

            // Create token
            $token = $customer->createToken('customer-token')->plainTextToken;

            DB::commit();

            Log::info('Customer account created successfully', [
                'customer_id' => $customer->id,
                'email' => $customer->email,
                'username' => $customer->username,
            ]);

            return [
                'success' => true,
                'message' => 'Customer account created successfully',
                'customer' => $customer,
                'token' => $token,
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to create customer account', [
                'error' => $e->getMessage(),
                'data' => Arr::except($data, ['password', 'password_confirmation']),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to create customer account: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Login customer
     * 
     * @param string $emailOrUsername
     * @param string $password
     * @return array
     */
    public function loginCustomer(string $emailOrUsername, string $password): array
    {
        try {
            DB::beginTransaction();

            // Find customer by email or username
            $customer = Customer::where('email', $emailOrUsername)
                ->orWhere('username', $emailOrUsername)
                ->first();

            if (!$customer) {
                $this->logFailedLogin($emailOrUsername, 'Customer not found');
                return [
                    'success' => false,
                    'message' => 'Invalid credentials',
                ];
            }

            // Check password
            if (!Hash::check($password, $customer->password)) {
                $this->logFailedLogin($emailOrUsername, 'Invalid password', $customer->id);
                return [
                    'success' => false,
                    'message' => 'Invalid credentials',
                ];
            }

            // Check if account is active
            if (!$customer->isActive()) {
                $this->logFailedLogin($emailOrUsername, 'Account suspended', $customer->id);
                return [
                    'success' => false,
                    'message' => 'Your account has been suspended. Please contact support.',
                ];
            }

            // Create token
            $token = $customer->createToken('customer-token')->plainTextToken;

            // Log successful login
            CustomerAuthLog::create([
                'customer_id' => $customer->id,
                'action' => 'login_success',
                'ip_address' => request()->ip() ?? 'unknown',
                'user_agent' => request()->userAgent() ?? 'unknown',
                'details' => [
                    'login_method' => 'email_or_username',
                    'login_identifier' => $emailOrUsername,
                    'login_time' => now()->toDateTimeString(),
                ],
            ]);

            // Update last login time
            $customer->update([
                'last_login_at' => now(),
            ]);

            DB::commit();

            Log::info('Customer logged in successfully', [
                'customer_id' => $customer->id,
                'email' => $customer->email,
                'username' => $customer->username,
                'ip_address' => request()->ip() ?? 'unknown',
            ]);

            return [
                'success' => true,
                'message' => 'Login successful',
                'customer' => $customer,
                'token' => $token,
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to login customer', [
                'error' => $e->getMessage(),
                'email_or_username' => $emailOrUsername,
            ]);

            return [
                'success' => false,
                'message' => 'Login failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Logout customer
     * 
     * @param Customer $customer
     * @return array
     */
    public function logoutCustomer(Customer $customer): array
    {
        try {
            DB::beginTransaction();

            // Get current token
            $currentToken = $customer->currentAccessToken();

            // Log the logout
            CustomerAuthLog::create([
                'customer_id' => $customer->id,
                'action' => 'logout',
                'ip_address' => request()->ip() ?? 'unknown',
                'user_agent' => request()->userAgent() ?? 'unknown',
                'details' => [
                    'logout_time' => now()->toDateTimeString(),
                    'token_id' => $currentToken ? $currentToken->id : null,
                ],
            ]);

            // Delete current token
            if ($currentToken) {
                $currentToken->delete();
            }

            DB::commit();

            Log::info('Customer logged out successfully', [
                'customer_id' => $customer->id,
                'email' => $customer->email,
                'username' => $customer->username,
                'ip_address' => request()->ip() ?? 'unknown',
            ]);

            return [
                'success' => true,
                'message' => 'Logged out successfully',
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to logout customer', [
                'error' => $e->getMessage(),
                'customer_id' => $customer->id,
            ]);

            return [
                'success' => false,
                'message' => 'Logout failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Log failed login attempts
     * 
     * @param string $emailOrUsername
     * @param string $reason
     * @param int|null $customerId
     * @return void
     */
    private function logFailedLogin(string $emailOrUsername, string $reason, ?int $customerId = null): void
    {
        try {
            CustomerAuthLog::create([
                'customer_id' => $customerId,
                'action' => 'login_failed',
                'ip_address' => request()->ip() ?? 'unknown',
                'user_agent' => request()->userAgent() ?? 'unknown',
                'details' => [
                    'login_identifier' => $emailOrUsername,
                    'failure_reason' => $reason,
                    'attempt_time' => now()->toDateTimeString(),
                ],
            ]);

            Log::warning('Failed login attempt', [
                'email_or_username' => $emailOrUsername,
                'reason' => $reason,
                'ip_address' => request()->ip() ?? 'unknown',
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to log failed login attempt', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get customer authentication history
     * 
     * @param Customer $customer
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAuthHistory(Customer $customer, int $limit = 50)
    {
        return CustomerAuthLog::where('customer_id', $customer->id)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get failed login attempts for an IP address
     * 
     * @param string $ipAddress
     * @param int $minutes
     * @return int
     */
    public function getFailedLoginAttempts(string $ipAddress, int $minutes = 15): int
    {
        return CustomerAuthLog::where('ip_address', $ipAddress)
            ->where('action', 'login_failed')
            ->where('created_at', '>=', now()->subMinutes($minutes))
            ->count();
    }
} 