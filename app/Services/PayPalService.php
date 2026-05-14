<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderBrandPayment;
use Exception;
use Illuminate\Support\Facades\Log;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PayPalService
{
    protected PayPalClient $client;

    public function __construct()
    {
        $this->client = new PayPalClient(config('paypal'));
    }

    /**
     * Create an order and get approval link
     */
    public function createApprovalLink(Order $order, OrderBrandPayment $brandPayment): string
    {
        try {
            // Get access token first
            $this->client->getAccessToken();

            $description = "Payment for Order #{$order->order_number}";

            // Create order using modern PayPal Orders API v2
            $orderData = [
                'intent'         => 'CAPTURE',
                'purchase_units' => [
                    [
                        'reference_id' => (string) $brandPayment->id,
                        'amount'       => [
                            'currency_code' => $brandPayment->currency ?: 'USD',
                            'value'         => (string) $brandPayment->amount,
                        ],
                        'description'  => $description,
                    ],
                ],
                'payment_source' => [
                    'paypal' => [
                        'experience_context' => [
                            'return_url' => route('frontend.paypal.success', $brandPayment),
                            'cancel_url' => route('frontend.paypal.cancel', ['order' => $order, 'brand_payment' => $brandPayment]),
                            'user_action' => 'PAY_NOW',
                            'brand_name' => config('app.name'),
                        ],
                    ],
                ],
            ];

            $response = $this->client->createOrder($orderData);

            // Normalize response to array for easier handling (vendor may return objects)
            if (! is_array($response)) {
                $response = json_decode(json_encode($response), true);
            }

            if (empty($response['id'])) {
                Log::error('PayPal createOrder returned unexpected response', ['response' => $response]);
                throw new Exception('Failed to create PayPal order: ' . json_encode($response));
            }

            // Store PayPal order ID
            $brandPayment->update([
                'payment_method'     => 'paypal',
                'paypal_order_id'    => $response['id'],
            ]);

            // Find the approval link — support multiple rel values returned by PayPal
            $approvalLink = null;
            $linkRels = ['approve', 'payer-action', 'payer_action', 'approve_url', 'approval_url'];

            if (! empty($response['links']) && is_array($response['links'])) {
                foreach ($response['links'] as $link) {
                    $rel = $link['rel'] ?? $link['relation'] ?? null;
                    $href = $link['href'] ?? $link['url'] ?? null;

                    if (! $rel || ! $href) {
                        continue;
                    }

                    if (in_array($rel, $linkRels, true)) {
                        $approvalLink = $href;
                        break;
                    }
                }
            }

            if (! $approvalLink) {
                Log::error('No approval link found in PayPal createOrder response', ['response' => $response]);
                throw new Exception('No approval link found in PayPal response');
            }

            return $approvalLink;
        } catch (Exception $e) {
            Log::error('PayPal approval link creation failed', [
                'error'         => $e->getMessage(),
                'order_id'      => $order->id,
                'brand_payment' => $brandPayment->id,
            ]);

            throw $e;
        }
    }

    /**
     * Capture the approved payment
     */
    public function captureApprovedPayment(OrderBrandPayment $brandPayment): array
    {
        try {
            if (!$brandPayment->paypal_order_id) {
                throw new Exception('No PayPal order ID found');
            }

            // Get access token
            $this->client->getAccessToken();

            // Capture the order payment
            $response = $this->client->capturePaymentOrder($brandPayment->paypal_order_id);

            if (empty($response['id'])) {
                throw new Exception('Failed to capture PayPal payment: ' . json_encode($response));
            }

            // Extract transaction details from the captured order
            $status = $response['status'] ?? null;
            $transactionId = null;

            if (!empty($response['purchase_units'][0]['payments']['captures'][0]['id'])) {
                $transactionId = $response['purchase_units'][0]['payments']['captures'][0]['id'];
            }

            // Update brand payment with transaction details
            $updateData = [
                'reference_number'      => $transactionId ?? $response['id'],
                'paypal_transaction_id' => $transactionId,
                'payment_method'        => 'paypal',
                'status'                => ($status === 'COMPLETED') ? 'confirmed' : 'pending',
            ];

            if ($status === 'COMPLETED') {
                $updateData['confirmed_at'] = now();
            }

            $brandPayment->update($updateData);

            return [
                'success'            => true,
                'transaction_id'     => $transactionId,
                'payment_status'     => $status,
                'brand_payment_id'   => $brandPayment->id,
                'order_id'           => $brandPayment->order_id,
            ];
        } catch (Exception $e) {
            Log::error('PayPal payment capture failed', [
                'error'             => $e->getMessage(),
                'brand_payment_id'  => $brandPayment->id,
            ]);

            throw $e;
        }
    }

    /**
     * Refund a PayPal transaction
     */
    public function refundTransaction(OrderBrandPayment $brandPayment): array
    {
        try {
            if (!$brandPayment->paypal_transaction_id) {
                throw new Exception('No PayPal transaction ID found for refund');
            }

            // Get access token
            $this->client->getAccessToken();

            // Refund the captured payment
            // The PayPal SDK requires: capture_id, invoice_id, amount, note
            $captureId = $brandPayment->paypal_transaction_id;
            $invoiceId = $brandPayment->reference_number ?? ('BP-' . $brandPayment->id);
            $amount = (float) $brandPayment->amount;
            $note = 'Refund for Order ' . ($brandPayment->order?->order_number ?? $brandPayment->order_id);

            $response = $this->client->refundCapturedPayment($captureId, $invoiceId, $amount, $note);

            if (empty($response['id'])) {
                throw new Exception('Failed to refund PayPal payment: ' . json_encode($response));
            }

            // Update brand payment status
            $brandPayment->update([
                'status' => 'refunded',
                'refunded_at' => now(),
            ]);

            return [
                'success'      => true,
                'refund_id'    => $response['id'],
                'status'       => $response['status'] ?? null,
            ];
        } catch (Exception $e) {
            Log::error('PayPal refund failed', [
                'error'             => $e->getMessage(),
                'brand_payment_id'  => $brandPayment->id,
            ]);

            throw $e;
        }
    }
}
