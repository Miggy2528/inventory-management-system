<?php

namespace App\Helpers;

use App\Models\Customer;
use App\Services\CustomerAuthService;
use Illuminate\Support\Facades\Hash;

class CustomerAuthHelper
{
    protected static $authService;

    /**
     * Get the auth service instance
     */
    protected static function getAuthService()
    {
        if (!self::$authService) {
            self::$authService = app(CustomerAuthService::class);
        }
        return self::$authService;
    }

    /**
     * Create a customer account (simple function)
     * 
     * @param array $data
     * @return array
     */
    public static function createCustomerAccount(array $data): array
    {
        return self::getAuthService()->createCustomerAccount($data);
    }

    /**
     * Login customer (simple function)
     * 
     * @param string $emailOrUsername
     * @param string $password
     * @return array
     */
    public static function loginCustomer(string $emailOrUsername, string $password): array
    {
        return self::getAuthService()->loginCustomer($emailOrUsername, $password);
    }

    /**
     * Logout customer (simple function)
     * 
     * @param Customer $customer
     * @return array
     */
    public static function logoutCustomer(Customer $customer): array
    {
        return self::getAuthService()->logoutCustomer($customer);
    }

    /**
     * Quick customer creation without service (for simple use cases)
     * 
     * @param array $data
     * @return Customer|null
     */
    public static function quickCreateCustomer(array $data): ?Customer
    {
        try {
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

            return $customer;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Quick customer login without service (for simple use cases)
     * 
     * @param string $emailOrUsername
     * @param string $password
     * @return Customer|null
     */
    public static function quickLoginCustomer(string $emailOrUsername, string $password): ?Customer
    {
        $customer = Customer::where('email', $emailOrUsername)
            ->orWhere('username', $emailOrUsername)
            ->first();

        if ($customer && Hash::check($password, $customer->password) && $customer->isActive()) {
            return $customer;
        }

        return null;
    }

    /**
     * Get customer by email or username
     * 
     * @param string $emailOrUsername
     * @return Customer|null
     */
    public static function getCustomerByEmailOrUsername(string $emailOrUsername): ?Customer
    {
        return Customer::where('email', $emailOrUsername)
            ->orWhere('username', $emailOrUsername)
            ->first();
    }

    /**
     * Check if customer exists
     * 
     * @param string $emailOrUsername
     * @return bool
     */
    public static function customerExists(string $emailOrUsername): bool
    {
        return Customer::where('email', $emailOrUsername)
            ->orWhere('username', $emailOrUsername)
            ->exists();
    }

    /**
     * Validate customer credentials
     * 
     * @param string $emailOrUsername
     * @param string $password
     * @return bool
     */
    public static function validateCredentials(string $emailOrUsername, string $password): bool
    {
        $customer = self::getCustomerByEmailOrUsername($emailOrUsername);
        
        return $customer && Hash::check($password, $customer->password) && $customer->isActive();
    }
} 