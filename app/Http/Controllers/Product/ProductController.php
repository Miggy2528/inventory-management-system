<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use App\Models\MeatCut;
use Illuminate\Http\Request;
use Picqer\Barcode\BarcodeGeneratorHTML;

class ProductController extends Controller
{
    public function index()
    {
        $meatCuts = MeatCut::orderBy('name')->get();
        return view('products.index', [
            'meatCuts' => $meatCuts,
        ]);
    }

    public function create(Request $request)
    {
        $categories = Category::all(['id', 'name']);
        $units = Unit::all(['id', 'name']);
        $meatCuts = MeatCut::all(['id', 'name']);

        if ($request->has('category')) {
            $categories = Category::whereSlug($request->get('category'))->get();
        }

        if ($request->has('unit')) {
            $units = Unit::whereSlug($request->get('unit'))->get();
        }

        return view('products.create', [
            'categories' => $categories,
            'units' => $units,
            'meatCuts' => $meatCuts,
        ]);
    }

    public function store(StoreProductRequest $request)
    {
        $existingProduct = Product::where('code', $request->get('code'))->first();
        
        if ($existingProduct) {
            $newCode = $this->generateUniqueCode();
            
            $request->merge(['code' => $newCode]);
        }

        try {
            $isSoldByPackage = $request->boolean('is_sold_by_package');
            
            // Debug: Log all form data
            \Log::info('=== PRODUCT CREATION DEBUG ===');
            \Log::info('is_sold_by_package: ' . ($isSoldByPackage ? 'true' : 'false'));
            \Log::info('price_per_package: ' . $request->get('price_per_package'));
            \Log::info('price_per_kg: ' . $request->get('price_per_kg'));
            \Log::info('All form data:', $request->all());
            
            $productData = [
                'meat_cut_id' => $request->get('meat_cut_id'),
                'name' => $request->get('name'),
                'code' => $request->get('code'),
                'category_id' => $request->get('category_id'),
                'unit_id' => $isSoldByPackage
                    ? \App\Models\Unit::where('name', 'like', '%package%')->value('id')
                    : $request->get('unit_id'),
                'quantity' => 0,
                'is_sold_by_package' => $isSoldByPackage,
                'total_weight' => $request->get('total_weight'),
                'storage_location' => $request->get('storage_location'),
                'expiration_date' => $request->get('expiration_date'),
                'source' => $request->get('source'),
                'notes' => $request->get('notes'),
                'buying_price' => $request->get('buying_price'),
                'quantity_alert' => $request->get('quantity_alert'),
            ];

            if ($isSoldByPackage) {
                // Package-specific fields
                $productData['price_per_package'] = $request->get('price_per_package') ?: 999.99; // Test value
                $productData['selling_price'] = $request->get('price_per_package') ?: 999.99; // Test value
            } else {
                // KG-specific fields
                $productData['unit_id'] = $request->get('unit_id');
                $productData['price_per_kg'] = $request->get('price_per_kg') ?: 888.88; // Test value
                $productData['selling_price'] = $request->get('price_per_kg') ?: 888.88; // Test value
            }

            $product = Product::create($productData);
            
            // Debug: Log what was actually saved
            \Log::info('=== PRODUCT CREATED ===');
            \Log::info('Product ID: ' . $product->id);
            \Log::info('is_sold_by_package: ' . ($product->is_sold_by_package ? 'true' : 'false'));
            \Log::info('price_per_package: ' . $product->price_per_package);
            \Log::info('price_per_kg: ' . $product->price_per_kg);
            \Log::info('selling_price: ' . $product->selling_price);

            /**
             * Handle image upload
             */
            if ($request->hasFile('product_image')) {
                $file = $request->file('product_image');
                $filename = hexdec(uniqid()) . '.' . $file->getClientOriginalExtension();

                // Validate file before uploading
                if ($file->isValid()) {
                    $file->storeAs('products/', $filename, 'public');
                    $product->update([
                        'product_image' => $filename
                    ]);
                } else {
                    return back()->withErrors(['product_image' => 'Invalid image file']);
                }
            }

            return redirect()
                ->back()
                ->with('success', 'Product created! Code: ' . $product->code . ' | Package: ' . ($product->is_sold_by_package ? 'YES' : 'NO') . ' | Selling Price: ' . $product->selling_price . ' | Price per Package: ' . $product->price_per_package . ' | Price per KG: ' . $product->price_per_kg);

        } catch (\Exception $e) {
            // Handle any unexpected errors
            return back()->withErrors(['error' => 'Something went wrong while creating the product']);
        }
    }

    // Helper method to generate a unique product code
    private function generateUniqueCode()
    {
        do {
            $code = 'PC' . strtoupper(uniqid());
        } while (Product::where('code', $code)->exists()); 

        return $code;
    }

    public function show(Product $product)
    {
        // Generate a barcode
        $generator = new BarcodeGeneratorHTML();

        $barcode = $generator->getBarcode($product->code, $generator::TYPE_CODE_128);

        return view('products.show', [
            'product' => $product,
            'barcode' => $barcode,
        ]);
    }

    public function edit(Product $product)
    {
        return view('products.edit', [
            'categories' => Category::all(),
            'units' => Unit::all(),
            'product' => $product
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $product->update($request->except('product_image'));

        if ($request->hasFile('product_image')) {

            // Delete old image if exists
            if ($product->product_image) {
                \Storage::disk('public')->delete('products/' . $product->product_image);
            }

            // Prepare new image
            $file = $request->file('product_image');
            $fileName = hexdec(uniqid()) . '.' . $file->getClientOriginalExtension();

            // Store new image to public storage
            $file->storeAs('products/', $fileName, 'public');

            // Save new image name to database
            $product->update([
                'product_image' => $fileName
            ]);
        }

        return redirect()
            ->route('products.index')
            ->with('success', 'Product has been updated!');
    }

    public function destroy(Product $product)
    {
        /**
         * Delete photo if exists.
         */
        if ($product->product_image) {
            \Storage::disk('public')->delete('products/' . $product->product_image);
        }

        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('success', 'Product has been deleted!');
    }
}
