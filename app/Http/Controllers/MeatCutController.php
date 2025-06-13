<?php

namespace App\Http\Controllers;

use App\Models\MeatCut;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MeatCutController extends Controller
{
    public function index()
    {
        $meatCuts = MeatCut::orderBy('animal_type')->orderBy('name')->paginate(10);
        return view('meat-cuts.index', compact('meatCuts'));
    }

    public function create()
    {
        return view('meat-cuts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'animal_type' => 'required|string|max:255',
            'cut_type' => 'required|string|max:255',
            'description' => 'nullable|string',
            'default_price_per_kg' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'minimum_stock_level' => 'required|integer|min:0',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('meat-cuts', 'public');
            $validated['image_path'] = $path;
        }

        MeatCut::create($validated);

        return redirect()->route('meat-cuts.index')
            ->with('success', 'Meat cut created successfully.');
    }

    public function edit(MeatCut $meatCut)
    {
        return view('meat-cuts.edit', compact('meatCut'));
    }

    public function update(Request $request, MeatCut $meatCut)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'animal_type' => 'required|string|max:255',
            'cut_type' => 'required|string|max:255',
            'description' => 'nullable|string',
            'default_price_per_kg' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'is_available' => 'boolean',
            'minimum_stock_level' => 'required|integer|min:0',
        ]);

        if ($request->hasFile('image')) {
            if ($meatCut->image_path) {
                Storage::disk('public')->delete($meatCut->image_path);
            }
            $path = $request->file('image')->store('meat-cuts', 'public');
            $validated['image_path'] = $path;
        }

        $meatCut->update($validated);

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
} 