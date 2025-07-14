<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use App\Models\Customer;

class CustomerAuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Configure Sanctum to use Customer model for customer authentication
        Auth::viaRequest('customer', function ($request) {
            if ($request->bearerToken()) {
                $token = \Laravel\Sanctum\PersonalAccessToken::findToken($request->bearerToken());
                
                if ($token && $token->tokenable instanceof Customer) {
                    return $token->tokenable;
                }
            }
            
            return null;
        });
    }
} 