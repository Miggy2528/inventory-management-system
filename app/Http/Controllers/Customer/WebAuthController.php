<?php

namespace App\Http\Controllers\Customer;

use App\Models\Customer;
use App\Services\CustomerAuthService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;

class WebAuthController extends Controller
{
    protected $authService;

    public function __construct(CustomerAuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Display customer registration view
     */
    public function showRegistrationForm()
    {
        return view('auth.customer.register');
    }

    /**
     * Handle customer registration
     */
    public function register(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:customers',
            'username' => 'required|string|max:255|unique:customers',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|max:20|unique:customers',
            'address' => 'required|string|max:500',
        ]);

        $result = $this->authService->createCustomerAccount($request->all());

        if ($result['success']) {
            // Log in the customer after registration using web_customer guard
            Auth::guard('web_customer')->login($result['customer']);
            $request->session()->regenerate();
            $request->session()->save(); // Force session save
            
            // Remove email verification step: always redirect to dashboard
            return redirect()->route('customer.dashboard')
                ->with('success', 'Account created successfully!');
        }

        return back()->withErrors(['error' => $result['message']]);
    }

    /**
     * Display customer login view
     */
    public function showLoginForm()
    {
        return view('auth.customer.login');
    }

    /**
     * Handle customer login
     */
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $result = $this->authService->loginCustomer($request->login, $request->password);

        if ($result['success']) {
            // Log in the customer for web session using web_customer guard
            Auth::guard('web_customer')->login($result['customer']);
            $request->session()->regenerate();

            return redirect()->route('customer.dashboard')
                ->with('success', 'Welcome back!');
        }

        throw ValidationException::withMessages([
            'login' => [$result['message']],
        ]);
    }

    /**
     * Handle customer logout
     */
    public function logout(Request $request): RedirectResponse
    {
        $customer = $request->user();
        
        if ($customer instanceof Customer) {
            $this->authService->logoutCustomer($customer);
        }

        Auth::guard('web_customer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
} 