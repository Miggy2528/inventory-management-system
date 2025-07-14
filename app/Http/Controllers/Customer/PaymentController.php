<?php

namespace App\Http\Controllers\Customer;

use App\Models\Order;
use App\Models\Payment;
use App\Models\CustomerNotification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    /**
     * Get customer's payments
     */
    public function index(Request $request): JsonResponse
    {
        $customer = $request->user();
        $perPage = $request->get('per_page', 15);
        $status = $request->get('status');

        $query = $customer->payments()->with(['order']);

        if ($status) {
            $query->where('status', $status);
        }

        $payments = $query->latest()->paginate($perPage);

        return response()->json($payments);
    }

    /**
     * Get specific payment details
     */
    public function show(Request $request, Payment $payment): JsonResponse
    {
        $customer = $request->user();

        // Ensure customer can only view their own payments
        if ($payment->customer_id !== $customer->id) {
            return response()->json(['message' => 'Payment not found'], 404);
        }

        $payment->load(['order', 'customer']);

        return response()->json($payment);
    }

    /**
     * Submit payment for an order
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:gcash,cash,bank_transfer,card',
            'reference_number' => 'nullable|string|max:255',
            'receipt_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
            'notes' => 'nullable|string|max:500',
        ]);

        $customer = $request->user();
        $order = Order::findOrFail($request->order_id);

        // Ensure customer can only pay for their own orders
        if ($order->customer_id !== $customer->id) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        // Check if order is still pending
        if (!$order->isPending()) {
            return response()->json([
                'message' => 'Cannot make payment for this order',
            ], 400);
        }

        // Check if payment amount is valid
        if ($request->amount > $order->remaining_balance) {
            return response()->json([
                'message' => 'Payment amount cannot exceed remaining balance',
            ], 400);
        }

        try {
            DB::beginTransaction();

            $paymentData = [
                'order_id' => $order->id,
                'customer_id' => $customer->id,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'status' => 'pending',
                'reference_number' => $request->reference_number,
                'notes' => $request->notes,
            ];

            // Handle receipt image upload
            if ($request->hasFile('receipt_image')) {
                $imagePath = $request->file('receipt_image')->store('payments/receipts', 'public');
                $paymentData['receipt_image'] = $imagePath;
            }

            $payment = Payment::create($paymentData);

            // Update order payment info
            $order->increment('pay', $request->amount);
            $order->decrement('due', $request->amount);

            // Create notification
            CustomerNotification::create([
                'customer_id' => $customer->id,
                'type' => 'payment_submitted',
                'title' => 'Payment Submitted',
                'message' => "Your payment of ₱{$request->amount} for order #{$order->invoice_no} has been submitted and is being reviewed.",
                'data' => [
                    'order_id' => $order->id,
                    'payment_id' => $payment->id,
                    'invoice_no' => $order->invoice_no,
                    'amount' => $request->amount,
                ],
            ]);

            DB::commit();

            $payment->load(['order', 'customer']);

            return response()->json([
                'message' => 'Payment submitted successfully',
                'payment' => $payment,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to submit payment: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update payment (for admin approval/rejection)
     */
    public function update(Request $request, Payment $payment): JsonResponse
    {
        $customer = $request->user();

        // Ensure customer can only update their own payments
        if ($payment->customer_id !== $customer->id) {
            return response()->json(['message' => 'Payment not found'], 404);
        }

        $request->validate([
            'status' => 'sometimes|in:pending,processing,completed,failed,cancelled',
            'notes' => 'nullable|string|max:500',
        ]);

        // Only allow customers to update notes, not status
        if ($request->has('status')) {
            return response()->json([
                'message' => 'You cannot update payment status',
            ], 403);
        }

        $payment->update($request->only(['notes']));

        return response()->json([
            'message' => 'Payment updated successfully',
            'payment' => $payment->fresh(),
        ]);
    }

    /**
     * Get payment methods available
     */
    public function paymentMethods(): JsonResponse
    {
        $methods = [
            [
                'id' => 'gcash',
                'name' => 'GCash',
                'description' => 'Pay using GCash mobile wallet',
                'instructions' => 'Send payment to our GCash number and upload the receipt',
                'account_number' => '09123456789', // Replace with actual GCash number
            ],
            [
                'id' => 'cash',
                'name' => 'Cash on Pickup',
                'description' => 'Pay with cash when you pick up your order',
                'instructions' => 'No advance payment required',
            ],
            [
                'id' => 'bank_transfer',
                'name' => 'Bank Transfer',
                'description' => 'Pay via bank transfer',
                'instructions' => 'Transfer to our bank account and upload the receipt',
                'account_details' => [
                    'bank_name' => 'Sample Bank',
                    'account_name' => 'ButcherPro',
                    'account_number' => '1234567890',
                ],
            ],
            [
                'id' => 'card',
                'name' => 'Credit/Debit Card',
                'description' => 'Pay using credit or debit card',
                'instructions' => 'Secure payment through our payment gateway',
            ],
        ];

        return response()->json($methods);
    }

    /**
     * Get payment statistics
     */
    public function statistics(Request $request): JsonResponse
    {
        $customer = $request->user();

        $stats = [
            'total_payments' => $customer->payments()->count(),
            'completed_payments' => $customer->payments()->completed()->count(),
            'pending_payments' => $customer->payments()->pending()->count(),
            'total_amount_paid' => $customer->payments()->completed()->sum('amount'),
            'payment_methods_used' => $customer->payments()
                ->selectRaw('payment_method, COUNT(*) as count')
                ->groupBy('payment_method')
                ->get(),
        ];

        return response()->json($stats);
    }

    /**
     * Download receipt image
     */
    public function downloadReceipt(Payment $payment): JsonResponse
    {
        $customer = Auth::user();

        // Ensure customer can only download their own receipts
        if ($payment->customer_id !== $customer->id) {
            return response()->json(['message' => 'Payment not found'], 404);
        }

        if (!$payment->receipt_image) {
            return response()->json(['message' => 'No receipt image available'], 404);
        }

        $path = Storage::disk('public')->path($payment->receipt_image);

        if (!file_exists($path)) {
            return response()->json(['message' => 'Receipt image not found'], 404);
        }

        return response()->json([
            'download_url' => Storage::disk('public')->url($payment->receipt_image),
        ]);
    }
} 