<?php

namespace App\Http\Controllers\Order;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\OrderStoreRequest;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Product;
use Carbon\Carbon;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::latest()->get();

        return view('orders.index', [
            'orders' => $orders,
        ]);
    }

    public function create()
    {
        Cart::instance('order')->destroy();

        return view('orders.create', [
            'carts' => Cart::content(),
            'customers' => Customer::all(['id', 'name']),
            'products' => Product::with(['category', 'unit'])->get(),
        ]);
    }

    public function store(OrderStoreRequest $request)
    {
        DB::beginTransaction();

        try {
            // Handle proof of payment upload if exists
            $proofOfPaymentPath = null;
            if ($request->hasFile('gcash_receipt')) {
                $proofOfPaymentPath = $request->file('gcash_receipt')->store('proofs', 'public');
            }

            // Create the order
            $order = Order::create([
                'customer_id' => Auth::guard('web_customer')->id(),
                'gcash_reference' => $request->gcash_reference,
                'proof_of_payment' => $proofOfPaymentPath,
                'order_status' => OrderStatus::PENDING,
                'total_price' => Cart::instance('order')->subtotal(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create order details
            $contents = Cart::instance('order')->content();
            $oDetails = [];

            foreach ($contents as $content) {
                $oDetails[] = [
                    'order_id' => $order->id,
                    'product_id' => $content->id,
                    'quantity' => $content->qty,
                    'unitcost' => $content->price,
                    'total' => $content->subtotal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            OrderDetails::insert($oDetails);

            // Clear cart
            Cart::destroy();

            DB::commit();

            return redirect()
                ->route('orders.index')
                ->with('success', 'Order has been created!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to place order: ' . $e->getMessage());
        }
    }

    public function show(Order $order)
    {
        $order->loadMissing(['customer', 'details']);

        return view('orders.show', [
            'order' => $order,
        ]);
    }

    public function update(Order $order, Request $request)
    {
        $products = OrderDetails::where('order_id', $order->id)->get();

        foreach ($products as $product) {
            Product::where('id', $product->product_id)
                ->update(['quantity' => DB::raw('quantity - ' . $product->quantity)]);
        }

        $order->update([
            'order_status' => OrderStatus::COMPLETE,
        ]);

        return redirect()
            ->route('orders.complete')
            ->with('success', 'Order has been completed!');
    }

    public function destroy(Order $order)
    {
        $order->delete();
    }

    public function downloadInvoice($order)
    {
        $order = Order::with(['customer', 'details'])
            ->where('id', $order)
            ->first();

        return view('orders.print-invoice', [
            'order' => $order,
        ]);
    }

    /**
     * Show customer's orders (for authenticated customers)
     */
    public function myOrders()
    {
        $customer = Auth::guard('web_customer')->user();

        if (!$customer) {
            return redirect()->route('customer.login')->with('error', 'You must be logged in to view your orders.');
        }

        $orders = Order::where('customer_id', $customer->id)
            ->with(['details.product'])
            ->latest()
            ->get();

        return view('customer.orders', [
            'orders' => $orders,
            'customer' => $customer
        ]);
    }
}
