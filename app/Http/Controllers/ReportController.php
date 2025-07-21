<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index()
    {
        return view('reports.index');
    }

    public function inventory()
    {
        $products = Product::with(['category', 'meatCut','inventoryMovements'])->get();

        // Initialize current stock to 0 if no inventory movements exist
        $products->each(function ($product) {
            if (!isset($product->current_stock)) {
                $product->current_stock = 0;
            }
        });

        $lowStockProducts = $products->filter(function ($product) {
            return $product->current_stock <= ($product->minimum_stock_level ?? 0);
        });

        $stockValue = $products->sum(function ($product) {
            return ($product->current_stock ?? 0) * ($product->price_per_kg ?? 0);
        });

        return view('reports.inventory', compact('products', 'lowStockProducts', 'stockValue'));
    }

    public function sales(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth());

        $sales = Order::where('order_status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('COALESCE(SUM(total), 0) as total_sales')
            )
            ->groupBy('date')
            ->get();

        // If no sales data, create an empty collection with the date range
        if ($sales->isEmpty()) {
            $period = Carbon::parse($startDate)->daysUntil($endDate);
            $sales = collect($period->map(function ($date) {
                return (object)[
                    'date' => $date->format('Y-m-d'),
                    'total_orders' => 0,
                    'total_sales' => 0
                ];
            }));
        }

        $totalSales = $sales->sum('total_sales');
        $totalOrders = $sales->sum('total_orders');
        $averageOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;

        return view('reports.sales', compact('sales', 'totalSales', 'totalOrders', 'averageOrderValue', 'startDate', 'endDate'));
    }

    public function purchases(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth());

        $purchases = DB::table('inventory_movements')
            ->where('type', 'in')
            ->where('reference_type', 'purchase')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('COALESCE(SUM(quantity), 0) as total_purchases')
            )
            ->groupBy('date')
            ->get();

        // If no purchase data, create an empty collection with the date range
        if ($purchases->isEmpty()) {
            $period = Carbon::parse($startDate)->daysUntil($endDate);
            $purchases = collect($period->map(function ($date) {
                return (object)[
                    'date' => $date->format('Y-m-d'),
                    'total_orders' => 0,
                    'total_purchases' => 0
                ];
            }));
        }

        $totalPurchases = $purchases->sum('total_purchases');
        $totalOrders = $purchases->sum('total_orders');
        $averageOrderValue = $totalOrders > 0 ? $totalPurchases / $totalOrders : 0;

        return view('reports.purchases', compact('purchases', 'totalPurchases', 'totalOrders', 'averageOrderValue', 'startDate', 'endDate'));
    }

    public function stockLevels()
    {
        $stockLevels = Product::with(['category', 'meatCut'])
            ->get()
            ->groupBy('category.name');

        // Initialize current stock to 0 if no inventory movements exist
        $stockLevels->each(function ($products) {
            $products->each(function ($product) {
                if (!isset($product->current_stock)) {
                    $product->current_stock = 0;
                }
            });
        });

        return view('reports.stock-levels', compact('stockLevels'));
    }

    public function exportInventory()
    {
        $products = Product::with(['category', 'meatCut'])
            ->get()
            ->map(function ($product) {
                return [
                    'Product Name' => $product->name,
                    'Category' => $product->category->name ?? 'N/A',
                    'Meat Cut' => $product->meatCut->name ?? 'N/A',
                    'Current Stock' => $product->current_stock,
                    'Unit Price' => number_format($product->price_per_kg, 2),
                    'Stock Value' => number_format($product->current_stock * $product->price_per_kg, 2),
                    'Status' => $product->current_stock <= 0 ? 'Out of Stock' : 
                              ($product->current_stock <= ($product->minimum_stock_level ?? 5) ? 'Low Stock' : 'In Stock')
                ];
            });

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="inventory-report-' . date('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($products) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for proper Excel encoding
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Add headers
            fputcsv($file, array_keys($products->first()));
            
            // Add data
            foreach ($products as $product) {
                fputcsv($file, $product);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportSales()
    {
        $startDate = request('start_date', Carbon::now()->startOfMonth());
        $endDate = request('end_date', Carbon::now()->endOfMonth());

        $sales = Order::where('order_status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('COALESCE(SUM(total), 0) as total_sales')
            )
            ->groupBy('date')
            ->get()
            ->map(function ($sale) {
                return [
                    'Date' => $sale->date,
                    'Total Orders' => $sale->total_orders,
                    'Total Sales' => number_format($sale->total_sales, 2),
                    'Average Order Value' => $sale->total_orders > 0 ? 
                        number_format($sale->total_sales / $sale->total_orders, 2) : '0.00'
                ];
            });

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="sales-report-' . date('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($sales) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for proper Excel encoding
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Add headers
            fputcsv($file, array_keys($sales->first()));
            
            // Add data
            foreach ($sales as $sale) {
                fputcsv($file, $sale);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
} 