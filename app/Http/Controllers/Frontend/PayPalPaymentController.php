<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderBrandPayment;
use App\Services\PayPalService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PayPalPaymentController extends Controller
{
    protected ?PayPalService $payPalService = null;

    /**
     * Lazy load PayPal service
     */
    protected function paypal(): PayPalService
    {
        if ($this->payPalService === null) {
            $this->payPalService = new PayPalService();
        }
        return $this->payPalService;
    }

    /**
     * Initiate PayPal payment for brand
     */
    public function initiate(Request $request, Order $order): RedirectResponse
    {
        $user = Auth::user();

        // Verify user is brand owner of order
        if ($user->user_type !== 'brand' || (int) $order->buyer_user_id !== (int) $user->id) {
            return redirect()->back()->with('error', 'Unauthorized access');
        }

        try {
            $validated = $request->validate([
                'amount'       => ['required', 'numeric', 'min:0.01'],
                'brand_note'   => ['nullable', 'string', 'max:1000'],
            ]);

            // Create brand payment record first
            $brandPayment = DB::transaction(function () use ($order, $user, $validated) {
                return OrderBrandPayment::create([
                    'order_id'      => $order->id,
                    'brand_user_id' => $user->id,
                    'brand_id'      => $order->brand_id,
                    'amount'        => round((float) $validated['amount'], 2),
                    'currency'      => $order->currency ?: 'USD',
                    'payment_method'=> 'paypal',
                    'invoice_id'    => sprintf('PP-%s-%s', $order->order_number, now()->timestamp),
                    'brand_note'    => $validated['brand_note'] ?? null,
                    'status'        => 'pending',
                    'submitted_at'  => now(),
                ]);
            });

            // Get PayPal approval link
            $approvalLink = $this->paypal()->createApprovalLink($order, $brandPayment);

            return redirect($approvalLink);
        } catch (Exception $e) {
            Log::error('PayPal payment initiation failed', [
                'error'    => $e->getMessage(),
                'order_id' => $order->id,
                'user_id'  => $user->id,
            ]);

            return redirect()
                ->route('frontend.orders.show', $order)
                ->with('error', 'Failed to initiate PayPal payment: ' . $e->getMessage());
        }
    }

    /**
     * Handle PayPal success callback (modern Orders API flow)
     */
    public function success(Request $request, OrderBrandPayment $brandPayment): RedirectResponse
    {
        try {
            // PayPal Orders API returns token parameter, not PayerID
            $token = $request->query('token');

            if (!$token) {
                return redirect()
                    ->route('frontend.orders.show', $brandPayment->order)
                    ->with('error', 'PayPal approval was not completed');
            }

            // Capture the approved payment
            $result = $this->paypal()->captureApprovedPayment($brandPayment);

            // Notify admins about the payment
            $this->notifyAdminsAboutPayment($brandPayment);

            return redirect()
                ->route('frontend.orders.show', $brandPayment->order)
                ->with('success', "PayPal payment of {$brandPayment->currency} {$brandPayment->amount} has been submitted successfully!");
        } catch (Exception $e) {
            Log::error('PayPal payment success handler failed', [
                'error'             => $e->getMessage(),
                'brand_payment_id'  => $brandPayment->id,
            ]);

            return redirect()
                ->route('frontend.orders.show', $brandPayment->order)
                ->with('error', 'Failed to process PayPal payment: ' . $e->getMessage());
        }
    }

    /**
     * Handle PayPal cancel callback
     */
    public function cancel(Request $request): RedirectResponse
    {
        $order = Order::findOrFail($request->query('order'));
        $brandPayment = OrderBrandPayment::findOrFail($request->query('brand_payment'));

        // Delete the pending payment record
        $brandPayment->delete();

        return redirect()
            ->route('frontend.orders.show', $order)
            ->with('warning', 'PayPal payment was cancelled');
    }

    /**
     * Handle PayPal IPN notification (legacy - kept for future webhook support)
     */
    public function notify(Request $request)
    {
        // Log webhook for future processing if needed
        Log::info('PayPal webhook received', $request->all());

        return response('Webhook received', 200);
    }

    /**
     * Notify admins about PayPal payment submission
     */
    protected function notifyAdminsAboutPayment(OrderBrandPayment $brandPayment): void
    {
        $brandPayment->loadMissing(['order:id,order_number', 'brandUser:id,name,email']);

        $dashboardUserIds = \App\Models\User::query()
            ->whereIn('user_type', ['admin', 'moderator'])
            ->where('is_active', true)
            ->pluck('id');

        foreach ($dashboardUserIds as $dashboardUserId) {
            \App\Models\Notification::create([
                'user_id'         => (int) $dashboardUserId,
                'type'            => 'payment',
                'title'           => 'PayPal payment submitted',
                'body'            => sprintf(
                    'Brand payment for order %s was submitted via PayPal for $%s',
                    $brandPayment->order?->order_number ?? 'N/A',
                    number_format((float) $brandPayment->amount, 2)
                ),
                'data_json'       => [
                    'action_url'       => route('dashboard.orders.show', $brandPayment->order),
                    'order_id'         => $brandPayment->order_id,
                    'brand_payment_id' => $brandPayment->id,
                    'amount'           => (float) $brandPayment->amount,
                    'reference_number' => $brandPayment->paypal_transaction_id,
                    'payment_method'   => 'paypal',
                ],
                'notifiable_type' => OrderBrandPayment::class,
                'notifiable_id'   => $brandPayment->id,
                'is_read'         => false,
            ]);
        }

        // Notify brand user about submission
        \App\Models\Notification::create([
            'user_id'         => (int) $brandPayment->brand_user_id,
            'type'            => 'payment',
            'title'           => 'PayPal payment submitted for review',
            'body'            => sprintf(
                'Your PayPal payment for order %s is waiting for admin verification.',
                $brandPayment->order?->order_number ?? 'N/A'
            ),
            'data_json'       => [
                'action_url'       => route('frontend.orders.show', $brandPayment->order),
                'order_id'         => $brandPayment->order_id,
                'brand_payment_id' => $brandPayment->id,
            ],
            'notifiable_type' => OrderBrandPayment::class,
            'notifiable_id'   => $brandPayment->id,
            'is_read'         => false,
        ]);
    }
}
