<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SupplierController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Supplier::class, 'supplier');
    }

    public function index()
    {
        $suppliers = Supplier::latest()->paginate(10);
        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:suppliers',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'shopname' => 'required|string|max:255',
            'type' => 'required|string|in:wholesale,retail,both',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string'
        ]);

        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $filename = Str::slug($request->name) . '-' . time() . '.' . $photo->getClientOriginalExtension();
            $photo->storeAs('public/suppliers', $filename);
            $validated['photo'] = $filename;
        }

        Supplier::create($validated);

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier created successfully.');
    }

    public function show(Supplier $supplier)
    {
        $products = $supplier->products;
        return view('suppliers.show', compact('supplier', 'products'));
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:suppliers,email,' . $supplier->id,
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'shopname' => 'required|string|max:255',
            'type' => 'required|string|in:wholesale,retail,both',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string'
        ]);

        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($supplier->photo) {
                Storage::delete('public/suppliers/' . $supplier->photo);
            }
            
            $photo = $request->file('photo');
            $filename = Str::slug($request->name) . '-' . time() . '.' . $photo->getClientOriginalExtension();
            $photo->storeAs('public/suppliers', $filename);
            $validated['photo'] = $filename;
        }

        $supplier->update($validated);

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier)
    {
        if ($supplier->photo) {
            Storage::delete('public/suppliers/' . $supplier->photo);
        }
        
        $supplier->delete();

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier deleted successfully.');
    }

    public function assignProducts(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id'
        ]);

        $supplier->products()->sync($validated['product_ids']);

        return redirect()->route('suppliers.show', $supplier)
            ->with('success', 'Products assigned successfully.');
    }

    public function deactivate(Supplier $supplier)
    {
        $supplier->update(['status' => 'inactive']);

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier deactivated successfully.');
    }
}
