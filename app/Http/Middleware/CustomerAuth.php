<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CustomerAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated via Sanctum
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Unauthenticated. Please login to continue.',
                'error' => 'unauthenticated',
            ], 401);
        }

        $user = Auth::guard('sanctum')->user();

        // Check if the authenticated user is a customer
        if (!$user instanceof \App\Models\Customer) {
            return response()->json([
                'message' => 'Access denied. Customer account required.',
                'error' => 'forbidden',
            ], 403);
        }

        // Check if customer account is active
        if (!$user->isActive()) {
            return response()->json([
                'message' => 'Your account has been suspended. Please contact support.',
                'error' => 'account_suspended',
            ], 403);
        }

        return $next($request);
    }
} 