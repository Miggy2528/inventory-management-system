<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\InventoryMovement;
use App\Enums\OrderStatus;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    /**
     * Display checkout page
     */
    public function index()
    {
        $cartItems = Cart::instance('customer')->content();

        if ($cartItems->isEmpty()) {
            return redirect()->route('customer.cart')->with('error', 'Your cart is empty!');
        }

        $cartTotal = Cart::instance('customer')->total();
        $cartSubtotal = Cart::instance('customer')->subtotal();
        $cartTax = Cart::instance('customer')->tax();
        $customer = auth()->user();

        return view('customer.checkout.index', compact('cartItems', 'cartTotal', 'cartSubtotal', 'cartTax', 'customer'));
    }

    /**
     * Place order
     */
    public function placeOrder(Request $request)
    {
        $request->validate([
            'payment_type' => 'required|in:cash,gcash,bank_transfer,card',
            'delivery_notes' => 'nullable|string|max:500',
            'delivery_address' => 'required|string|max:500',
            'contact_phone' => 'required|string|max:20',
            'gcash_reference' => 'nullable|string|max:100',
            'proof_of_payment' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
    
        $cartItems = Cart::instance('customer')->content();
    
        if ($cartItems->isEmpty()) {
            return back()->with('error', 'Your cart is empty!');
        }
    
        $customer = auth()->user();
    
        try {
            DB::beginTransaction();
    
            $totalProducts = $cartItems->sum('qty');
            $subTotal = Cart::instance('customer')->subtotal();
            $tax = Cart::instance('customer')->tax();
            $total = Cart::instance('customer')->total();
    
            // Handle proof_of_payment upload
            $proofOfPaymentPath = null;
            if ($request->payment_type === 'gcash' && $request->hasFile('proof_of_payment')) {
                $proofOfPaymentPath = $request->file('proof_of_payment')->store('gcash_receipts', 'public');
            }
    
            // Create order
            $order = Order::create([
                'customer_id' => $customer->id,
                'order_date' => now(),
                'order_status' => OrderStatus::PENDING,
                'total_products' => $totalProducts,
                'sub_total' => $subTotal,
                'vat' => $tax,
                'total' => $total,
                'invoice_no' => 'INV-' . strtoupper(Str::random(8)),
                'tracking_number' => 'TRK-' . strtoupper(Str::random(10)),
                'payment_type' => $request->payment_type,
                'pay' => 0,
                'due' => $total,
                'delivery_notes' => $request->delivery_notes,
                'delivery_address' => $request->delivery_address,
                'contact_phone' => $request->contact_phone,
                'estimated_delivery' => now()->addDays(3),
                'gcash_reference' => $request->gcash_reference,
                'proof_of_payment' => $proofOfPaymentPath,
            ]);
    
            // Save order details and update inventory
            foreach ($cartItems as $item) {
                OrderDetails::create([
                    'order_id' => $order->id,
                    'product_id' => $item->id,
                    'quantity' => $item->qty,
                    'unitcost' => $item->price,
                    'total' => $item->subtotal,
                ]);
    
                DB::table('products')
                    ->where('id', $item->id)
                    ->decrement('quantity', $item->qty);
    
                InventoryMovement::create([
                    'product_id' => $item->id,
                    'type' => 'out',
                    'quantity' => $item->qty,
                ]);
            }
    
            Cart::instance('customer')->destroy();
    
            DB::commit();
    
            return redirect()->route('customer.orders')
                ->with('success', 'Order placed successfully! Order #' . $order->invoice_no);
    
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to place order. ' . $e->getMessage());
        }
    }
}