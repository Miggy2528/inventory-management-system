<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\MeatCut;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display the product catalog
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'unit', 'meatCut'])
            ->where('quantity', '>', 0);

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhereHas('category', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('meatCut', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Category filter
        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }

        // Sort options
        $sort = $request->get('sort', 'name');
        $direction = $request->get('direction', 'asc');
        
        switch ($sort) {
            case 'price':
                $query->orderBy('selling_price', $direction);
                break;
            case 'name':
            default:
                $query->orderBy('name', $direction);
                break;
        }

        $products = $query->paginate(12);
        $categories = Category::all();

        return view('customer.products.index', compact('products', 'categories'));
    }

    /**
     * Display a specific product
     */
    public function show(Product $product)
    {
        $product->load(['category', 'unit']);
        
        // Get related products
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('quantity', '>', 0)
            ->limit(4)
            ->get();

        return view('customer.products.show', compact('product', 'relatedProducts'));
    }

    /**
     * Display products by category
     */
    public function category(Category $category)
    {
        $products = Product::with(['category', 'unit', 'meatCut'])
            ->where('category_id', $category->id)
            ->where('quantity', '>', 0)
            ->paginate(12);

        $categories = Category::all();
   

        return view('customer.products.category', compact('products', 'category', 'categories'));
    }
} 