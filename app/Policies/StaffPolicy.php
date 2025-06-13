<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\InventoryMovement;
use Illuminate\Auth\Access\HandlesAuthorization;

class StaffPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->role === 'staff' || $user->role === 'admin';
    }

    // Inventory Management
    public function manageInventory(User $user)
    {
        return $user->role === 'staff' || $user->role === 'admin';
    }

    public function createInventoryMovement(User $user)
    {
        return $user->role === 'staff' || $user->role === 'admin';
    }

    public function updateInventoryMovement(User $user, InventoryMovement $movement)
    {
        return ($user->role === 'staff' || $user->role === 'admin') && 
               $movement->reference_type === 'order' && 
               $movement->created_at->diffInHours(now()) <= 24;
    }

    public function deleteInventoryMovement(User $user, InventoryMovement $movement)
    {
        return ($user->role === 'staff' || $user->role === 'admin') && 
               $movement->reference_type === 'order' && 
               $movement->created_at->diffInHours(now()) <= 24;
    }

    // Order Management
    public function viewOrders(User $user)
    {
        return $user->role === 'staff' || $user->role === 'admin';
    }

    public function updateOrderStatus(User $user, Order $order)
    {
        return $user->role === 'staff' || $user->role === 'admin';
    }

    public function receivePayment(User $user, Order $order)
    {
        return $user->role === 'staff' || $user->role === 'admin';
    }

    // Product Management
    public function manageProducts(User $user)
    {
        return $user->role === 'staff' || $user->role === 'admin';
    }

    public function createProduct(User $user)
    {
        return $user->role === 'staff' || $user->role === 'admin';
    }

    public function updateProduct(User $user, Product $product)
    {
        return $user->role === 'staff' || $user->role === 'admin';
    }

    public function deleteProduct(User $user, Product $product)
    {
        return $user->role === 'staff' || $user->role === 'admin';
    }

    // Reports
    public function viewReports(User $user)
    {
        return $user->role === 'staff' || $user->role === 'admin';
    }

    public function generateReports(User $user)
    {
        return $user->role === 'staff' || $user->role === 'admin';
    }

    // Restricted Actions
    public function manageUsers(User $user)
    {
        return $user->role === 'admin';
    }

    public function manageRoles(User $user)
    {
        return $user->role === 'admin';
    }

    public function deactivateSupplier(User $user, Supplier $supplier)
    {
        return $user->role === 'admin';
    }

    public function accessAdminSettings(User $user)
    {
        return $user->role === 'admin';
    }
} 