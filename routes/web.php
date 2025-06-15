<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\Order\OrderController;
use App\Http\Controllers\Order\DueOrderController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Purchase\PurchaseController;
use App\Http\Controllers\Order\OrderPendingController;
use App\Http\Controllers\Order\OrderCompleteController;
use App\Http\Controllers\Dashboards\DashboardController;
use App\Http\Controllers\Product\ProductExportController;
use App\Http\Controllers\Product\ProductImportController;
use App\Http\Controllers\MeatCutController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Staff\InventoryController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('php/', function () {
    return phpinfo();
});

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Admin and Staff Routes
    Route::middleware(['role:admin,staff'])->group(function () {
        // Product Management
        Route::resource('/products', ProductController::class);
        Route::get('/products/import', [ProductImportController::class, 'create'])->name('products.import.view');
        Route::post('/products/import', [ProductImportController::class, 'store'])->name('products.import.store');
        Route::get('/products/export', [ProductExportController::class, 'create'])->name('products.export.store');
        
        // Inventory Management
        Route::resource('/categories', CategoryController::class);
        Route::resource('/units', UnitController::class);
        Route::resource('/meat-cuts', MeatCutController::class);
        
        // Order Management
        Route::get('/orders/pending', OrderPendingController::class)->name('orders.pending');
        Route::get('/orders/complete', OrderCompleteController::class)->name('orders.complete');
        Route::post('/invoice/create', [InvoiceController::class, 'create'])->name('invoice.create');
    });

    // Admin Only Routes
    Route::middleware(['role:admin'])->group(function () {
        // User Management
        Route::resource('/users', UserController::class);
        Route::put('/user/change-password/{username}', [UserController::class, 'updatePassword'])->name('users.updatePassword');
        Route::put('/user/{user}/status', [UserController::class, 'updateStatus'])->name('users.updateStatus');
        
        // Supplier Management
        Route::resource('/suppliers', SupplierController::class);
    });

    // Customer Routes
    Route::middleware(['role:customer'])->group(function () {
        Route::get('/my-orders', [OrderController::class, 'myOrders'])->name('customer.orders');
        Route::get('/my-profile', [CustomerController::class, 'profile'])->name('customer.profile');
    });

    // Shared Routes (All Authenticated Users)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/profile/settings', [ProfileController::class, 'settings'])->name('profile.settings');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('/customers', CustomerController::class);

    // Route Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/orders/store', [OrderController::class, 'store'])->name('orders.store');

    // SHOW ORDER
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::put('/orders/update/{order}', [OrderController::class, 'update'])->name('orders.update');

    // DUES
    Route::get('/due/orders/', [DueOrderController::class, 'index'])->name('due.index');
    Route::get('/due/order/view/{order}', [DueOrderController::class, 'show'])->name('due.show');
    Route::get('/due/order/edit/{order}', [DueOrderController::class, 'edit'])->name('due.edit');
    Route::put('/due/order/update/{order}', [DueOrderController::class, 'update'])->name('due.update');

    // TODO: Remove from OrderController
    Route::get('/orders/details/{order_id}/download', [OrderController::class, 'downloadInvoice'])->name('order.downloadInvoice');

    // Route Purchases
    Route::get('/purchases/approved', [PurchaseController::class, 'approvedPurchases'])->name('purchases.approvedPurchases');
    Route::get('/purchases/report', [PurchaseController::class, 'dailyPurchaseReport'])->name('purchases.dailyPurchaseReport');
    Route::get('/purchases/report/export', [PurchaseController::class, 'getPurchaseReport'])->name('purchases.getPurchaseReport');
    Route::post('/purchases/report/export', [PurchaseController::class, 'exportPurchaseReport'])->name('purchases.exportPurchaseReport');

    Route::get('/purchases', [PurchaseController::class, 'index'])->name('purchases.index');
    Route::get('/purchases/create', [PurchaseController::class, 'create'])->name('purchases.create');
    Route::post('/purchases', [PurchaseController::class, 'store'])->name('purchases.store');

    Route::get('/purchases/{purchase}', [PurchaseController::class, 'show'])->name('purchases.show');
    Route::get('/purchases/{purchase}/edit', [PurchaseController::class, 'edit'])->name('purchases.edit');
    Route::put('/purchases/{purchase}/edit', [PurchaseController::class, 'update'])->name('purchases.update');
    Route::delete('/purchases/{purchase}', [PurchaseController::class, 'destroy'])->name('purchases.delete');

    // Supplier Management Routes
    Route::resource('suppliers', SupplierController::class);
    Route::post('suppliers/{supplier}/assign-products', [SupplierController::class, 'assignProducts'])->name('suppliers.assign-products');
    Route::patch('suppliers/{supplier}/deactivate', [SupplierController::class, 'deactivate'])->name('suppliers.deactivate');

    // Reports Routes
    Route::middleware(['auth', 'role:admin'])->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/inventory', [ReportController::class, 'inventory'])->name('reports.inventory');
        Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
        Route::get('/reports/purchases', [ReportController::class, 'purchases'])->name('reports.purchases');
        Route::get('/reports/stock-levels', [ReportController::class, 'stockLevels'])->name('reports.stock-levels');
        Route::get('/reports/export-inventory', [ReportController::class, 'exportInventory'])->name('reports.export-inventory');
        Route::get('/reports/export-sales', [ReportController::class, 'exportSales'])->name('reports.export-sales');
    });

    // Staff Inventory Management Routes
    Route::middleware(['auth', 'role:staff'])->prefix('staff/inventory')->name('staff.inventory.')->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('index');
        Route::get('/create', [InventoryController::class, 'create'])->name('create');
        Route::post('/', [InventoryController::class, 'store'])->name('store');
        Route::get('/{movement}/edit', [InventoryController::class, 'edit'])->name('edit');
        Route::put('/{movement}', [InventoryController::class, 'update'])->name('update');
        Route::delete('/{movement}', [InventoryController::class, 'destroy'])->name('destroy');
        Route::get('/reorder', [InventoryController::class, 'reorder'])->name('reorder');
        Route::get('/follow-up', [InventoryController::class, 'followUp'])->name('follow-up');
        Route::get('/discard', [InventoryController::class, 'discard'])->name('discard');
        Route::put('/{movement}/receive', [InventoryController::class, 'receive'])->name('receive');
        Route::put('/{product}/discard', [InventoryController::class, 'markAsDiscarded'])->name('discard');
    });

    Route::middleware(['auth', 'role:staff'])->prefix('staff')->name('staff.')->group(function () {
        Route::get('products', [\App\Http\Controllers\Staff\InventoryController::class, 'products'])->name('products.index');
    });
});

require __DIR__.'/auth.php';

Route::get('test/', function (){
//    return view('test');
    return view('orders.create');
});
