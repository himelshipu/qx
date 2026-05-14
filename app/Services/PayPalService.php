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
        $this->client = new PayPalClient();
        $this->client->setApiContext();
    }

    /**
     * Create an approval link for payment
     */
    public function createApprovalLink(Order $order, OrderBrandPayment $brandPayment): string
    {
        try {
            $items = [];
            $description = "Payment for Order #{$order->order_number}";

            // Single item for order payment
            $items[] = [
                'name'        => "Order Payment",
                'sku'         => "ORDER-{$order->id}",
                'description' => $description,
                'price'       => (string) $brandPayment->amount,
                'quantity'    => 1,
                'currency'    => $brandPayment->currency ?: 'USD',
            ];

            $total = (string) $brandPayment->amount;

            $response = $this->client->setExpressCheckout(
                [
                    'items'      => $items,
                    'return_url' => route('frontend.paypal.success', $brandPayment),
                    'cancel_url' => route('frontend.paypal.cancel', ['order' => $order, 'brand_payment' => $brandPayment]),
                    'notify_url' => route('frontend.paypal.notify'),
                    'total'      => $total,
                    'currency'   => $brandPayment->currency ?: 'USD',
                    'invoice_id' => $brandPayment->id,
                ],
                []
            );

            if (!isset($response['TOKEN'])) {
                throw new Exception('Failed to get PayPal token');
            }

            // Store token for later verification
            $brandPayment->update([
                'payment_method' => 'paypal',
                'paypal_token'   => $response['TOKEN'],
            ]);

            $baseUrl = config('paypal.mode') === 'sandbox'
                ? 'https://www.sandbox.paypal.com/checkoutnow?token='
                : 'https://www.paypal.com/checkoutnow?token=';

            return $baseUrl . $response['TOKEN'];
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
     * Execute the approved payment
     */
    public function executeApprovedPayment(OrderBrandPayment $brandPayment, string $payerId): array
    {
        try {
            $response = $this->client->doExpressCheckoutPayment(
                [
                    'TOKEN'     => $brandPayment->paypal_token,
                    'PAYERID'   => $payerId,
                    'PAYMENTACTION' => 'Sale',
                    'AMT'       => (string) $brandPayment->amount,
                    'CURRENCYCODE' => $brandPayment->currency ?: 'USD',
                ],
                []
            );

            if ($response['ACK'] !== 'Success') {
                $errorMessage = $response['L_LONGMESSAGE0'] ?? 'Unknown error';
                throw new Exception("Payment execution failed: {$errorMessage}");
            }

            // Extract transaction details
            $transactionId = $response['TRANSACTIONID'] ?? null;
            $paymentStatus = $response['PAYMENTSTATUS'] ?? null;

            // Update brand payment with transaction details
            $updateData = [
                'reference_number' => $transactionId,
                'paypal_transaction_id' => $transactionId,
                'payment_method'   => 'paypal',
                'status'           => $paymentStatus === 'Completed' ? 'confirmed' : 'pending',
            ];

            if ($paymentStatus === 'Completed') {
                $updateData['confirmed_at'] = now();
                $updateData['submitted_at'] = now();
            }

            $brandPayment->update($updateData);

            return [
                'success'            => true,
                'transaction_id'     => $transactionId,
                'payment_status'     => $paymentStatus,
                'brand_payment_id'   => $brandPayment->id,
                'order_id'           => $brandPayment->order_id,
            ];
        } catch (Exception $e) {
            Log::error('PayPal payment execution failed', [
                'error'             => $e->getMessage(),
                'brand_payment_id'  => $brandPayment->id,
                'payer_id'          => $payerId,
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

            $response = $this->client->refundTransaction(
                [
                    'TRANSACTIONID' => $brandPayment->paypal_transaction_id,
                    'REFUNDTYPE'    => 'Full',
                ],
                []
            );

            if ($response['ACK'] !== 'Success') {
                $errorMessage = $response['L_LONGMESSAGE0'] ?? 'Unknown error';
                throw new Exception("Refund failed: {$errorMessage}");
            }

            return [
                'success'   => true,
                'refund_id' => $response['REFUNDTRANSACTIONID'] ?? null,
            ];
        } catch (Exception $e) {
            Log::error('PayPal refund failed', [
                'error'             => $e->getMessage(),
                'brand_payment_id'  => $brandPayment->id,
                'transaction_id'    => $brandPayment->paypal_transaction_id,
            ]);

            throw $e;
        }
    }

    /**
     * Verify transaction from PayPal IPN
     */
    public function verifyIpn(array $postData): bool
    {
        try {
            // Build verification request
            $verifyData = array_merge(['cmd' => '_notify-validate'], $postData);

            $mode = config('paypal.mode') === 'sandbox' ? 'sandbox' : 'live';
            $url = $mode === 'sandbox'
                ? 'https://www.sandbox.paypal.com/cgi-bin/webscr'
                : 'https://www.paypal.com/cgi-bin/webscr';

            $response = @fopen(
                $url . '?' . http_build_query($verifyData),
                'r'
            );

            if (!$response) {
                throw new Exception('Failed to connect to PayPal for IPN verification');
            }

            $responseStatus = stream_get_contents($response);
            fclose($response);

            return $responseStatus === 'VERIFIED';
        } catch (Exception $e) {
            Log::error('PayPal IPN verification failed', [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Process IPN notification
     */
    public function processIpnNotification(array $postData): void
    {
        try {
            $transactionId = $postData['txn_id'] ?? null;
            $invoiceId = $postData['invoice'] ?? null;
            $paymentStatus = $postData['payment_status'] ?? null;

            if (!$transactionId || !$invoiceId) {
                Log::warning('PayPal IPN missing transaction or invoice ID', $postData);
                return;
            }

            // Find the brand payment record
            $brandPayment = OrderBrandPayment::find($invoiceId);

            if (!$brandPayment) {
                Log::warning('PayPal IPN invoice not found', ['invoice_id' => $invoiceId]);
                return;
            }

            // Update payment status based on PayPal notification
            $statusMap = [
                'Completed' => 'confirmed',
                'Pending'   => 'pending',
                'Refunded'  => 'rejected',
                'Failed'    => 'rejected',
            ];

            $newStatus = $statusMap[$paymentStatus] ?? null;

            if ($newStatus && $brandPayment->status !== $newStatus) {
                $updateData = [
                    'paypal_transaction_id' => $transactionId,
                    'payment_method'        => 'paypal',
                    'status'                => $newStatus,
                ];

                if ($newStatus === 'confirmed') {
                    $updateData['confirmed_at'] = now();
                    $updateData['submitted_at'] = now();
                }

                $brandPayment->update($updateData);

                Log::info('PayPal IPN processed', [
                    'invoice_id' => $invoiceId,
                    'txn_id'     => $transactionId,
                    'status'     => $newStatus,
                ]);
            }
        } catch (Exception $e) {
            Log::error('PayPal IPN processing error', [
                'error' => $e->getMessage(),
                'data'  => $postData,
            ]);
        }
    }

    /**
     * Get payment details from PayPal
     */
    public function getTransactionDetails(string $transactionId): array
    {
        try {
            $response = $this->client->getTransactionDetails(
                [
                    'TRANSACTIONID' => $transactionId,
                ],
                []
            );

            if ($response['ACK'] !== 'Success') {
                throw new Exception("Failed to get transaction details");
            }

            return $response;
        } catch (Exception $e) {
            Log::error('PayPal transaction details retrieval failed', [
                'error'          => $e->getMessage(),
                'transaction_id' => $transactionId,
            ]);

            throw $e;
        }
    }
}
