<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CustomerWebAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $routeName = $request->route() ? $request->route()->getName() : 'no_route';
        $user = Auth::guard('web_customer')->user();
        Log::debug('[CustomerWebAuth] Route: ' . $routeName, [
            'authenticated' => Auth::guard('web_customer')->check(),
            'user_id' => $user ? $user->id : null,
            'verified' => $user ? $user->hasVerifiedEmail() : null,
        ]);
        // Check if user is authenticated via web_customer guard
        if (!Auth::guard('web_customer')->check()) {
            return redirect()->route('customer.login')
                ->with('error', 'Please login to continue.');
        }

        $user = Auth::guard('web_customer')->user();

        // Check if the authenticated user is a customer
        if (!$user instanceof \App\Models\Customer) {
            Auth::guard('web_customer')->logout();
            return redirect()->route('customer.login')
                ->with('error', 'Access denied. Customer account required.');
        }

        // Check if customer account is active
        if (!$user->isActive()) {
            Auth::guard('web_customer')->logout();
            return redirect()->route('customer.login')
                ->with('error', 'Your account has been suspended. Please contact support.');
        }

        // Email verification check removed - customers can access all features without verification
        return $next($request);
    }
} 