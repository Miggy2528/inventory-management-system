<?php

namespace App\Http\Controllers\Customer;

use App\Models\Customer;
use App\Enums\OrderStatus;
use App\Services\CustomerAuthService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(CustomerAuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Customer registration
     */
    public function register(Request $request): JsonResponse
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
            return response()->json($result, 201);
        }

        return response()->json($result, 500);
    }

    /**
     * Customer login
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $result = $this->authService->loginCustomer($request->email, $request->password);

        if ($result['success']) {
            return response()->json($result);
        }

        throw ValidationException::withMessages([
            'email' => [$result['message']],
        ]);
    }

    /**
     * Customer logout
     */
    public function logout(Request $request): JsonResponse
    {
        $result = $this->authService->logoutCustomer($request->user());

        if ($result['success']) {
            return response()->json($result);
        }

        return response()->json($result, 500);
    }

    /**
     * Get customer profile
     */
    public function profile(Request $request): JsonResponse
    {
        $customer = $request->user();
        
        return response()->json([
            'customer' => $customer->load(['recentOrders', 'unreadNotifications']),
        ]);
    }

    /**
     * Update customer profile
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $customer = $request->user();

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20|unique:customers,phone,' . $customer->id,
            'address' => 'sometimes|string|max:500',
            'photo' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only(['name', 'phone', 'address']);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($customer->photo) {
                Storage::disk('public')->delete($customer->photo);
            }

            $photoPath = $request->file('photo')->store('customers/photos', 'public');
            $data['photo'] = $photoPath;
        }

        $customer->update($data);

        return response()->json([
            'message' => 'Profile updated successfully',
            'customer' => $customer->fresh(),
        ]);
    }

    /**
     * Change password
     */
    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $customer = $request->user();

        if (!Hash::check($request->current_password, $customer->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The current password is incorrect.'],
            ]);
        }

        $customer->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => 'Password changed successfully',
        ]);
    }

    /**
     * Get customer dashboard data
     */
    public function dashboard(Request $request): JsonResponse
    {
        $customer = $request->user();

        $dashboardData = [
            'customer' => $customer,
            'recent_orders' => $customer->recentOrders()->with('details.product')->get(),
            'pending_orders' => $customer->pendingOrders()->count(),
            'completed_orders' => $customer->completedOrders()->count(),
            'unread_notifications' => $customer->unreadNotifications()->count(),
            'total_spent' => $customer->completedOrders()->sum('total'),
        ];

        return response()->json($dashboardData);
    }

    /**
     * Get customer authentication history
     */
    public function authHistory(Request $request): JsonResponse
    {
        $customer = $request->user();
        $limit = $request->get('limit', 50);

        $authHistory = $this->authService->getAuthHistory($customer, $limit);

        return response()->json([
            'auth_history' => $authHistory,
        ]);
    }
} 