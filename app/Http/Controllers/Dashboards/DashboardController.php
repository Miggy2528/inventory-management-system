<?php

namespace App\Http\Controllers\Dashboards;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\MeatCut;
use App\Enums\OrderStatus;

class DashboardController extends Controller
{
    public function index()
    {
        // Orders statistics
        $orders = Order::count();
        $completedOrders = Order::where('order_status', OrderStatus::COMPLETE)
            ->count();
        $todayOrders = Order::whereDate('created_at', today())->count();

        // Products and Categories
        $products = Product::count();
        $categories = Category::count();

        // Meat-specific statistics
        $totalMeatCuts = MeatCut::count();
        $availableMeatCuts = MeatCut::where('is_available', true)
            ->where('quantity', '>', 0)
            ->count();
        $lowStockMeatCuts = MeatCut::whereColumn('quantity', '<=', 'minimum_stock_level')
            ->count();

        $meatByAnimalType = MeatCut::selectRaw('animal_type, COUNT(*) as count')
            ->groupBy('animal_type')
            ->get()
            ->pluck('count', 'animal_type')
            ->toArray();

        return view('dashboard', compact(
            'orders',
            'completedOrders',
            'todayOrders',
            'products',
            'categories',
            'totalMeatCuts',
            'availableMeatCuts',
            'lowStockMeatCuts',
            'meatByAnimalType'
        ));
    }
}
