<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentsController extends Controller
{
    public function index()
    {
        // Get all payments with relationships
        $payments = Payment::with(['order.brand', 'order.campaign', 'paymentMethod'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Payment statistics
        $totalPayments      = Payment::count();
        $successfulPayments = Payment::whereIn('status', ['authorized', 'captured'])->count();
        $pendingPayments    = Payment::where('status', 'pending')->count();
        $failedPayments     = Payment::where('status', 'failed')->count();

        // Payment amounts
        $totalAmount    = Payment::sum('amount') ?? 0;
        $capturedAmount = Payment::whereIn('status', ['authorized', 'captured'])->sum('amount') ?? 0;
        $pendingAmount  = Payment::where('status', 'pending')->sum('amount') ?? 0;
        $failedAmount   = Payment::where('status', 'failed')->sum('amount') ?? 0;

        // Payment status distribution
        $paymentsByStatus = Payment::selectRaw('status, COUNT(*) as count, SUM(amount) as total')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        // Recent payments (last 7 days)
        $recentPayments = Payment::where('created_at', '>=', now()->subDays(7))
            ->count();

        // Payment methods breakdown
        $paymentsByMethod = Payment::with('paymentMethod')
            ->selectRaw('payment_method_id, COUNT(*) as count, SUM(amount) as total')
            ->groupBy('payment_method_id')
            ->get();

        // Top brands by payment volume
        $topBrandsByPayment = Order::join('payments', 'orders.id', '=', 'payments.order_id')
            ->join('brands', 'orders.brand_id', '=', 'brands.id')
            ->selectRaw('brands.id, brands.brand_name, COUNT(payments.id) as payment_count, SUM(payments.amount) as total_amount')
            ->groupBy('brands.id', 'brands.brand_name')
            ->orderBy('total_amount', 'desc')
            ->limit(5)
            ->get();

        return view('backend.pages.payments.index', compact(
            'payments',
            'totalPayments',
            'successfulPayments',
            'pendingPayments',
            'failedPayments',
            'totalAmount',
            'capturedAmount',
            'pendingAmount',
            'failedAmount',
            'paymentsByStatus',
            'recentPayments',
            'paymentsByMethod',
            'topBrandsByPayment'
        ));
    }

    public function show(Payment $payment)
    {
        $payment->load(['order.brand', 'order.campaign', 'order.items', 'paymentMethod']);

        return response()->json([
            'payment'     => [
                'id'                => $payment->id,
                'order_id'          => $payment->order_id,
                'brand'             => $payment->order?->brand->brand_name,
                'campaign'          => $payment->order?->campaign->name ?? 'N/A',
                'amount'            => $payment->amount,
                'status'            => $payment->status,
                'payment_method'    => $payment->paymentMethod?->type ?? 'Unknown',
                'stripe_id'         => $payment->stripe_transaction_id,
                'payment_intent_id' => $payment->payment_intent_id,
                'created_at'        => $payment->created_at->format('M d, Y H:i'),
                'can_retry'         => $payment->status === 'failed' || $payment->status === 'pending',
                'can_refund'        => in_array($payment->status, ['authorized', 'captured'])
            ],
            'order_items' => $payment->order?->items->map(function ($item) {
                return [
                    'id'          => $item->id,
                    'description' => $item->description,
                    'amount'      => $item->amount,
                    'status'      => $item->status
                ];
            })
        ]);
    }

    public function refund(Request $request, Payment $payment)
    {
        // Validate user can refund this payment
        if (!in_array($payment->status, ['authorized', 'captured'])) {
            return response()->json([
                'success' => false,
                'message' => 'Only authorized or captured payments can be refunded'
            ], 400);
        }

        $validated = $request->validate([
            'amount' => 'nullable|numeric|min:0.01',
            'reason' => 'nullable|string'
        ]);

        $refundAmount = $validated['amount'] ?? $payment->amount;

        // Update payment status
        $payment->update([
            'status' => $refundAmount >= $payment->amount ? 'refunded' : 'partially_refunded'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Refund processed successfully',
            'payment' => [
                'id'     => $payment->id,
                'status' => $payment->status,
                'amount' => $refundAmount
            ]
        ]);
    }

    public function retry(Request $request, Payment $payment)
    {
        // Validate user can retry this payment
        if ($payment->status !== 'failed' && $payment->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only failed or pending payments can be retried'
            ], 400);
        }

        // Update payment status to processing
        $payment->update([
            'status' => 'processing'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment retry initiated. Please wait for processing.',
            'payment' => [
                'id'     => $payment->id,
                'status' => $payment->status
            ]
        ]);
    }
}
