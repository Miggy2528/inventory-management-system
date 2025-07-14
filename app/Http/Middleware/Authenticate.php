<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }

        // Check which guard is being used and redirect accordingly
        $guard = $this->getGuard($request);
        
        if ($guard === 'web_customer') {
            return route('customer.login');
        }
        
        return route('login');
    }

    /**
     * Get the guard being used for the request
     */
    protected function getGuard(Request $request): string
    {
        // Check if the request is for customer routes
        if ($request->is('customer/*') || $request->is('my-*') || $request->is('cart*') || $request->is('checkout*')) {
            return 'web_customer';
        }
        
        return 'web';
    }
}
