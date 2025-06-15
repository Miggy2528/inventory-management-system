<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index()
    {
        $inventoryMovements = InventoryMovement::with(['product'])
            ->latest()
            ->paginate(10);

        return view('staff.inventory.index', compact('inventoryMovements'));
    }

    public function create()
    {
        $products = Product::all();
        $suppliers = Supplier::where('status', 'active')->get();

        return view('staff.inventory.create', compact('products', 'suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:1',
            'type' => 'required|in:in,out',
            'reference_type' => 'required|in:order,purchase,adjustment',
            'reference_id' => 'required_if:reference_type,order,purchase|nullable|integer',
            'notes' => 'nullable|string|max:255'
        ]);

        DB::transaction(function () use ($validated) {
            $movement = InventoryMovement::create($validated);

            // Update product stock
            $product = Product::find($validated['product_id']);
            if ($validated['type'] === 'in') {
                $product->increment('quantity', $validated['quantity']);
            } else {
                $product->decrement('quantity', $validated['quantity']);
            }
        });

        return redirect()
            ->route('staff.inventory.index')
            ->with('success', 'Inventory movement recorded successfully.');
    }

    public function edit(InventoryMovement $movement)
    {
        // Only allow editing within 24 hours
        if ($movement->created_at->diffInHours(now()) > 24) {
            return redirect()
                ->route('staff.inventory.index')
                ->with('error', 'Cannot edit inventory movements older than 24 hours.');
        }

        $products = Product::all();
        $suppliers = Supplier::where('status', 'active')->get();

        return view('staff.inventory.edit', compact('movement', 'products', 'suppliers'));
    }

    public function update(Request $request, InventoryMovement $movement)
    {
        // Only allow editing within 24 hours
        if ($movement->created_at->diffInHours(now()) > 24) {
            return redirect()
                ->route('staff.inventory.index')
                ->with('error', 'Cannot edit inventory movements older than 24 hours.');
        }

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:1',
            'type' => 'required|in:in,out',
            'reference_type' => 'required|in:order,purchase,adjustment',
            'reference_id' => 'required_if:reference_type,order,purchase|nullable|integer',
            'notes' => 'nullable|string|max:255'
        ]);

        DB::transaction(function () use ($validated, $movement) {
            // Revert the old movement
            $oldProduct = Product::find($movement->product_id);
            if ($movement->type === 'in') {
                $oldProduct->decrement('quantity', $movement->quantity);
            } else {
                $oldProduct->increment('quantity', $movement->quantity);
            }

            // Apply the new movement
            $movement->update($validated);
            $newProduct = Product::find($validated['product_id']);
            if ($validated['type'] === 'in') {
                $newProduct->increment('quantity', $validated['quantity']);
            } else {
                $newProduct->decrement('quantity', $validated['quantity']);
            }
        });

        return redirect()
            ->route('staff.inventory.index')
            ->with('success', 'Inventory movement updated successfully.');
    }

    public function destroy(InventoryMovement $movement)
    {
        // Only allow deletion within 24 hours
        if ($movement->created_at->diffInHours(now()) > 24) {
            return redirect()
                ->route('staff.inventory.index')
                ->with('error', 'Cannot delete inventory movements older than 24 hours.');
        }

        DB::transaction(function () use ($movement) {
            // Revert the movement
            $product = Product::find($movement->product_id);
            if ($movement->type === 'in') {
                $product->decrement('quantity', $movement->quantity);
            } else {
                $product->increment('quantity', $movement->quantity);
            }

            $movement->delete();
        });

        return redirect()
            ->route('staff.inventory.index')
            ->with('success', 'Inventory movement deleted successfully.');
    }

    public function reorder()
    {
        $lowStockProducts = Product::where('quantity', '<=', DB::raw('minimum_stock_level'))
            ->with(['category', 'meatCut'])
            ->get();

        return view('staff.inventory.reorder', compact('lowStockProducts'));
    }

    public function followUp()
    {
        $pendingDeliveries = InventoryMovement::where('type', 'in')
            ->where('reference_type', 'purchase')
            ->whereNull('received_at')
            ->with(['product', 'supplier'])
            ->get();

        return view('staff.inventory.follow-up', compact('pendingDeliveries'));
    }

    public function discard()
    {
        $expiredProducts = Product::where('expiration_date', '<', now())
            ->orWhere('status', 'damaged')
            ->with(['category', 'meatCut'])
            ->get();

        return view('staff.inventory.discard', compact('expiredProducts'));
    }

    public function products()
    {
        $meatCuts = \App\Models\MeatCut::orderBy('name')->get();
        return view('staff.products.index', compact('meatCuts'));
    }
} 