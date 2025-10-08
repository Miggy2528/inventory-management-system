<?php

namespace App\Http\Controllers;

use App\Models\MeatCut;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MeatCutController extends Controller
{
    public function index()
    {
        $meatCuts = MeatCut::orderByDesc('created_at')->orderBy('name')->paginate(10);
        return view('meat-cuts.index', compact('meatCuts'));
    }

    public function create()
    {
        return view('meat-cuts.create');
    }

    public function store(Request $request)
    {
        $isPackaged = (bool) $request->get('is_packaged');

        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'minimum_stock_level' => 'required|integer|min:0',
            'is_packaged' => 'sometimes|boolean',
        ];

        if ($isPackaged) {
            $rules['animal_type'] = 'nullable|string|max:255';
            $rules['cut_type'] = 'nullable|string|max:255';
            $rules['default_price_per_kg'] = 'nullable|numeric|min:0';
            $rules['package_price'] = 'required|numeric|min:0';
        } else {
            $rules['animal_type'] = 'required|string|max:255';
            $rules['cut_type'] = 'required|string|max:255';
            $rules['default_price_per_kg'] = 'required|numeric|min:0';
            $rules['package_price'] = 'nullable|numeric|min:0';
        }

        $validated = $request->validate($rules);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('meat-cuts', 'public');
            $validated['image_path'] = $path;
        }

        $meatCut = MeatCut::create($validated);

        // Resolve default unit based on packaging
        $unitId = $meatCut->is_packaged
            ? Unit::firstOrCreate(['name' => 'Package'], ['slug' => Str::slug('Package')])->id
            : Unit::firstOrCreate(['name' => 'Kilogram'], ['slug' => Str::slug('Kilogram')])->id;

        // Ensure a corresponding Product exists for this MeatCut so it shows in Products area
        Product::updateOrCreate(
            ['meat_cut_id' => $meatCut->id],
            [
                'name' => $meatCut->name,
                'slug' => Str::slug($meatCut->name),
                'meat_cut_id' => $meatCut->id,
                // Mirror the current meat cut quantity so quantity shows in product list
                'quantity' => $meatCut->quantity ?? 0,
                'unit_id' => $unitId,
                'category_id' => \App\Models\Category::firstOrCreate(['name' => 'Meat Products'], ['slug' => \Illuminate\Support\Str::slug('Meat Products')])->id,
                'price_per_kg' => $meatCut->is_packaged ? null : ($meatCut->default_price_per_kg ?? 0),
                'price_per_package' => $meatCut->is_packaged ? ($meatCut->package_price ?? 0) : null,
                'is_sold_by_package' => $meatCut->is_packaged ? true : false,
                'selling_price' => $meatCut->is_packaged
                    ? ($meatCut->package_price ?? 0)
                    : ($meatCut->default_price_per_kg ?? 0),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Also update any existing products with this meat_cut_id to ensure they have correct values
        Product::where('meat_cut_id', $meatCut->id)->update([
            'quantity' => $meatCut->quantity ?? 0,
            'unit_id' => $unitId,
            'is_sold_by_package' => $meatCut->is_packaged ? true : false,
            'price_per_kg' => $meatCut->is_packaged ? null : ($meatCut->default_price_per_kg ?? 0),
            'price_per_package' => $meatCut->is_packaged ? ($meatCut->package_price ?? 0) : null,
            'selling_price' => $meatCut->is_packaged
                ? ($meatCut->package_price ?? 0)
                : ($meatCut->default_price_per_kg ?? 0),
        ]);

        return redirect()->route('meat-cuts.index')
            ->with('success', 'Meat cut created successfully.');
    }

    public function edit(MeatCut $meatCut)
    {
        return view('meat-cuts.edit', compact('meatCut'));
    }

    public function update(Request $request, MeatCut $meatCut)
    {
        $isPackaged = (bool) $request->get('is_packaged', $meatCut->is_packaged);

        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'is_available' => 'boolean',
            'minimum_stock_level' => 'required|integer|min:0',
            'is_packaged' => 'sometimes|boolean',
        ];

        if ($isPackaged) {
            $rules['animal_type'] = 'nullable|string|max:255';
            $rules['cut_type'] = 'nullable|string|max:255';
            $rules['default_price_per_kg'] = 'nullable|numeric|min:0';
            $rules['package_price'] = 'required|numeric|min:0';
        } else {
            $rules['animal_type'] = 'required|string|max:255';
            $rules['cut_type'] = 'required|string|max:255';
            $rules['default_price_per_kg'] = 'required|numeric|min:0';
            $rules['package_price'] = 'nullable|numeric|min:0';
        }

        $validated = $request->validate($rules);

        if ($request->hasFile('image')) {
            if ($meatCut->image_path) {
                Storage::disk('public')->delete($meatCut->image_path);
            }
            $path = $request->file('image')->store('meat-cuts', 'public');
            $validated['image_path'] = $path;
        }

        $meatCut->update($validated);

        // Resolve unit based on packaging
        $unitId = $meatCut->is_packaged
            ? Unit::firstOrCreate(['name' => 'Package'], ['slug' => Str::slug('Package')])->id
            : Unit::firstOrCreate(['name' => 'Kilogram'], ['slug' => Str::slug('Kilogram')])->id;

        // Keep corresponding Product in sync
        Product::updateOrCreate(
            ['meat_cut_id' => $meatCut->id],
            [
                'name' => $meatCut->name,
                'slug' => Str::slug($meatCut->name),
                'meat_cut_id' => $meatCut->id,
                // Keep quantity mirrored so it's visible in product listings
                'quantity' => $meatCut->quantity ?? 0,
                'unit_id' => $unitId,
                'category_id' => \App\Models\Category::firstOrCreate(['name' => 'Meat Products'], ['slug' => \Illuminate\Support\Str::slug('Meat Products')])->id,
                'price_per_kg' => $meatCut->is_packaged ? null : ($meatCut->default_price_per_kg ?? null),
                'price_per_package' => $meatCut->is_packaged ? ($meatCut->package_price ?? null) : null,
                'is_sold_by_package' => $meatCut->is_packaged ? true : false,
                'selling_price' => $meatCut->is_packaged
                    ? ($meatCut->package_price ?? 0)
                    : ($meatCut->default_price_per_kg ?? 0),
            ]
        );

        // Also update any existing products with this meat_cut_id to ensure they have correct values
        Product::where('meat_cut_id', $meatCut->id)->update([
            'quantity' => $meatCut->quantity ?? 0,
            'unit_id' => $unitId,
            'is_sold_by_package' => $meatCut->is_packaged ? true : false,
            'price_per_kg' => $meatCut->is_packaged ? null : ($meatCut->default_price_per_kg ?? null),
            'price_per_package' => $meatCut->is_packaged ? ($meatCut->package_price ?? null) : null,
            'selling_price' => $meatCut->is_packaged
                ? ($meatCut->package_price ?? 0)
                : ($meatCut->default_price_per_kg ?? 0),
        ]);

        return redirect()->route('meat-cuts.index')
            ->with('success', 'Meat cut updated successfully.');
    }

    public function destroy(MeatCut $meatCut)
    {
        if ($meatCut->image_path) {
            Storage::disk('public')->delete($meatCut->image_path);
        }

        $meatCut->delete();

        return redirect()->route('meat-cuts.index')
            ->with('success', 'Meat cut deleted successfully.');
    }

    public function updateQuantity(Request $request, MeatCut $meatCut)
    {
        $request->validate([
            'quantity' => 'required|integer|min:0'
        ]);

        $meatCut->update([
            'quantity' => $request->quantity,
            'is_available' => $request->quantity > 0
        ]);

        // Also update the corresponding product
        Product::where('meat_cut_id', $meatCut->id)->update([
            'quantity' => $request->quantity
        ]);

        return redirect()->back()
            ->with('success', 'Quantity updated successfully.');
    }
} 