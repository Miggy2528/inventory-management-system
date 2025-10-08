<?php

namespace App\Http\Controllers;

use App\Models\PackagedProduct;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PackagedProductController extends Controller
{
    public function index(): View
    {
        $packagedProducts = PackagedProduct::orderBy('name')->paginate(15);

        return view('packaged-products.index', compact('packagedProducts'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
        ]);

        PackagedProduct::create($validated);

        return redirect()->route('packaged-products.index')
            ->with('success', 'Packaged product created successfully.');
    }

    public function edit(PackagedProduct $packaged_product): View
    {
        return view('packaged-products.edit', compact('packaged_product'));
    }

    public function update(Request $request, PackagedProduct $packaged_product): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
        ]);

        $packaged_product->update($validated);

        return redirect()->route('packaged-products.index')
            ->with('success', 'Packaged product updated successfully.');
    }

    public function destroy(PackagedProduct $packaged_product): RedirectResponse
    {
        $packaged_product->delete();

        return redirect()->route('packaged-products.index')
            ->with('success', 'Packaged product deleted successfully.');
    }
}


