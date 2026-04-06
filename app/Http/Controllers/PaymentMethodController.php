<?php

namespace App\Http\Controllers;

use App\Http\Requests\Web\StorePaymentMethodRequest;
use App\Models\PaymentMethod;
use App\Services\PaymentMethodService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentMethodController extends Controller
{
    public function __construct(protected PaymentMethodService $paymentService)
    {
    }

    private function redirectToAccount(Request $request, string $fallbackTab = 'payment')
    {
        $user = Auth::user();
        $tab  = $request->input('tab', $request->query('tab', $fallbackTab));

        return redirect()
            ->route('frontend.account.edit', ['slug' => $user->slug, 'tab' => $tab])
            ->with('success', session('success'))
            ->with('tab', $tab);
    }

    /**
     * Verify ownership of payment method
     */
    private function verifyOwnership(PaymentMethod $paymentMethod): void
    {
        if ($paymentMethod->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this payment method');
        }
    }

    /**
     * Display user's payment methods
     */
    public function index(Request $request)
    {
        $user           = Auth::user();
        $paymentMethods = $user->paymentMethods()
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('frontend.pages.account', [
            'user'           => $user,
            'brand'          => $user->brand,
            'paymentMethods' => $paymentMethods,
            'tab'            => 'payment'
        ]);
    }

    /**
     * Store a new payment method using Stripe token
     */
    public function store(StorePaymentMethodRequest $request)
    {
        $user      = Auth::user();
        $validated = $request->validated();

        // Check if Stripe is properly configured
        if (!PaymentMethodService::isConfigured()) {
            return $this->redirectToAccount($request)
                ->with('error', 'Payment processing is not configured. Please contact support.');
        }

        // Create payment method from Stripe token
        $paymentMethod = $this->paymentService->createFromStripeToken(
            $user,
            $validated['stripe_payment_method_id'],
            $validated['is_default'] ?? false
        );

        if (!$paymentMethod) {
            return $this->redirectToAccount($request)
                ->with('error', 'Failed to add payment method. Please check your card details and try again.');
        }

        return $this->redirectToAccount($request)
            ->with('success', 'Payment method added successfully');
    }

    /**
     * Set a payment method as default
     */
    public function setDefault(Request $request, PaymentMethod $paymentMethod)
    {
        $this->verifyOwnership($paymentMethod);

        $this->paymentService->setAsDefault($paymentMethod);

        return $this->redirectToAccount($request)
            ->with('success', 'Default payment method updated');
    }

    /**
     * Delete a payment method
     */
    public function destroy(Request $request, PaymentMethod $paymentMethod)
    {
        $this->verifyOwnership($paymentMethod);

        $this->paymentService->deletePaymentMethod($paymentMethod);

        return $this->redirectToAccount($request)
            ->with('success', 'Payment method deleted successfully');
    }

    /**
     * Get payment methods as JSON (for AJAX requests)
     */
    public function getJson(Request $request)
    {
        $user           = Auth::user();
        $paymentMethods = $user->paymentMethods()
            ->orderBy('is_default', 'desc')
            ->select(['id', 'last4', 'brand', 'expiry_month', 'expiry_year', 'is_default'])
            ->get()
            ->map(function ($method) {
                return [
                    'id'               => $method->id,
                    'display'          => $method->display_name,
                    'last4'            => $method->last4,
                    'brand'            => strtoupper($method->brand),
                    'expiry'           => $method->formatted_expiry,
                    'is_default'       => $method->is_default,
                    'is_expired'       => $method->isExpired(),
                    'is_expiring_soon' => $method->isExpiringSoon()
                ];
            });

        return response()->json($paymentMethods);
    }

    /**
     * Get default payment method as JSON
     */
    public function getDefaultJson(Request $request)
    {
        $user          = Auth::user();
        $defaultMethod = $user->paymentMethods()->where('is_default', true)->first();

        if (!$defaultMethod) {
            return response()->json(['error' => 'No default payment method found'], 404);
        }

        return response()->json([
            'id'      => $defaultMethod->id,
            'display' => $defaultMethod->display_name,
            'last4'   => $defaultMethod->last4,
            'brand'   => strtoupper($defaultMethod->brand),
            'expiry'  => $defaultMethod->formatted_expiry
        ]);
    }
}
