<?php

namespace App\Services;

use App\Models\PaymentMethod;
use App\Models\User;
use Stripe\StripeClient;
use Stripe\Exception\ApiErrorException;

class PaymentMethodService
{
    protected StripeClient $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('stripe.secret_key'));
    }

    /**
     * Create a payment method from Stripe token
     */
    public function createFromStripeToken(User $user, string $paymentMethodId, bool $isDefault = false): ?PaymentMethod
    {
        try {
            // Retrieve the payment method from Stripe
            $stripePaymentMethod = $this->stripe->paymentMethods->retrieve($paymentMethodId);

            if (!$stripePaymentMethod) {
                throw new \Exception('Invalid payment method token');
            }

            // Extract card details
            $cardData = $stripePaymentMethod->card;

            // If this is marked as default, unset others
            if ($isDefault) {
                $user->paymentMethods()->update(['is_default' => false]);
            }

            // Create or update payment method in our database
            $paymentMethod = $user->paymentMethods()->create([
                'provider' => 'stripe',
                'provider_payment_method_id' => $stripePaymentMethod->id,
                'last4' => $cardData->last4,
                'brand' => $cardData->brand,
                'expiry_month' => $cardData->exp_month,
                'expiry_year' => $cardData->exp_year,
                'is_default' => $isDefault || !$user->paymentMethods()->exists(),
            ]);

            return $paymentMethod;
        } catch (ApiErrorException $e) {
            \Log::error('Stripe API Error:', [
                'message' => $e->getMessage(),
                'code' => $e->getStripeCode(),
                'http_status' => $e->getHttpStatus(),
            ]);
            return null;
        } catch (\Exception $e) {
            \Log::error('Payment Method Creation Error:', [
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Get user's default payment method
     */
    public function getDefaultPaymentMethod(User $user): ?PaymentMethod
    {
        return $user->paymentMethods()
            ->where('is_default', true)
            ->first();
    }

    /**
     * Set payment method as default
     */
    public function setAsDefault(PaymentMethod $paymentMethod): bool
    {
        try {
            // Unset all others for this user
            $paymentMethod->user->paymentMethods()
                ->where('id', '!=', $paymentMethod->id)
                ->update(['is_default' => false]);

            // Set this as default
            $paymentMethod->update(['is_default' => true]);

            return true;
        } catch (\Exception $e) {
            \Log::error('Set Default Payment Method Error:', [
                'message' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Delete payment method from Stripe and database
     */
    public function deletePaymentMethod(PaymentMethod $paymentMethod): bool
    {
        try {
            // Delete from Stripe if it exists
            if ($paymentMethod->provider_payment_method_id) {
                $this->stripe->paymentMethods->detach($paymentMethod->provider_payment_method_id);
            }

            // If this was the default, set another as default
            $wasDefault = $paymentMethod->is_default;
            $paymentMethod->delete();

            if ($wasDefault) {
                $newDefault = $paymentMethod->user->paymentMethods()->first();
                if ($newDefault) {
                    $newDefault->update(['is_default' => true]);
                }
            }

            return true;
        } catch (ApiErrorException $e) {
            \Log::error('Stripe Deletion Error:', [
                'message' => $e->getMessage(),
            ]);
            // Still delete from database even if Stripe fails
            $paymentMethod->delete();
            return true;
        }
    }

    /**
     * Process a payment using saved card
     */
    public function processPayment(PaymentMethod $paymentMethod, int $amountInCents, string $description = ''): ?object
    {
        try {
            $payment = $this->stripe->paymentIntents->create([
                'amount' => $amountInCents,
                'currency' => strtolower(config('stripe.default_currency')),
                'payment_method' => $paymentMethod->provider_payment_method_id,
                'customer' => $this->getOrCreateStripeCustomer($paymentMethod->user),
                'confirm' => true,
                'description' => $description,
            ]);

            return $payment;
        } catch (ApiErrorException $e) {
            \Log::error('Stripe Payment Error:', [
                'message' => $e->getMessage(),
                'code' => $e->getStripeCode(),
            ]);
            return null;
        }
    }

    /**
     * Get or create Stripe customer
     */
    public function getOrCreateStripeCustomer(User $user): string
    {
        try {
            // Check if user has stripe_customer_id
            if ($user->stripe_customer_id) {
                return $user->stripe_customer_id;
            }

            // Create new Stripe customer
            $customer = $this->stripe->customers->create([
                'email' => $user->email,
                'name' => $user->name,
                'phone' => $user->phone,
            ]);

            // Save customer ID
            $user->update(['stripe_customer_id' => $customer->id]);

            return $customer->id;
        } catch (ApiErrorException $e) {
            \Log::error('Stripe Customer Creation Error:', [
                'message' => $e->getMessage(),
            ]);
            return '';
        }
    }

    /**
     * Validate Stripe configuration
     */
    public static function isConfigured(): bool
    {
        return !empty(config('stripe.public_key')) && !empty(config('stripe.secret_key'));
    }
}
