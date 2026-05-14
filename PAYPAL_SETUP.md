## PayPal Integration for Order Payments - Setup Guide

### System Overview

This system allows brands to pay for orders using either manual payment submission or direct PayPal payment. The integration is built using the `srmklive/paypal` Laravel package and provides:

- **Express Checkout Flow** - Brands are redirected to PayPal to complete payments
- **IPN Webhook Support** - Real-time payment status updates from PayPal
- **Admin Review System** - All payments (manual and PayPal) are reviewed by admins
- **Transaction Tracking** - Full audit trail of payment attempts

### Architecture

#### 1. Payment Methods
- **Manual Payment**: Brand submits payment details (reference/invoice number) for admin verification
- **PayPal Payment**: Brand pays directly via PayPal, with status auto-confirmation

#### 2. Payment Flow

##### Manual Payment Flow:
```
Brand fills form → Submit → Pending status → Admin reviews → Confirmed/Rejected
```

##### PayPal Payment Flow:
```
Brand selects PayPal → Create payment record → Redirect to PayPal → 
Approve payment → PayPal callback → Execute payment → Submit for review → 
Admin reviews → Confirmed/Rejected
```

#### 3. Database Schema

The `order_brand_payments` table now includes:
- `payment_method` (string) - 'manual' or 'paypal'
- `paypal_token` (text) - PayPal Express Checkout token
- `paypal_transaction_id` (string) - PayPal transaction ID (unique)

### Configuration

#### Environment Variables (.env)

```env
# PayPal Configuration
PAYPAL_MODE=sandbox  # sandbox or live
PAYPAL_SANDBOX_CLIENT_ID=your_sandbox_client_id
PAYPAL_SANDBOX_CLIENT_SECRET=your_sandbox_secret
PAYPAL_LIVE_CLIENT_ID=your_live_client_id
PAYPAL_LIVE_CLIENT_SECRET=your_live_secret
PAYPAL_CURRENCY=USD
PAYPAL_NOTIFY_URL=http://qx.local/paypal/notify
```

#### Getting PayPal Credentials

1. Go to [PayPal Developer Dashboard](https://developer.paypal.com)
2. Create a sandbox/live app
3. Copy Client ID and Secret
4. Add to .env

#### Configure IPN Webhook in PayPal

1. Log into PayPal business account
2. Go to Settings → Notifications → Update IPN
3. Set IPN URL to: `http://qx.local/paypal/notify`
4. Leave all checkboxes selected to receive all notifications

### Components

#### 1. PayPalService (`app/Services/PayPalService.php`)

Core service handling all PayPal operations:

**Methods:**
- `createApprovalLink(Order $order, OrderBrandPayment $brandPayment): string`
  - Creates PayPal Express Checkout approval URL
  - Stores token for verification
  - Returns redirect URL

- `executeApprovedPayment(OrderBrandPayment $brandPayment, string $payerId): array`
  - Executes the approved payment
  - Updates payment record with transaction details
  - Returns success/transaction info

- `refundTransaction(OrderBrandPayment $brandPayment): array`
  - Issues full refund for a PayPal transaction
  - Updates payment status

- `verifyIpn(array $postData): bool`
  - Verifies IPN message authenticity with PayPal
  - Returns true if verified

- `processIpnNotification(array $postData): void`
  - Processes incoming IPN notifications
  - Updates payment status based on PayPal updates
  - Logs all transactions

- `getTransactionDetails(string $transactionId): array`
  - Retrieves full transaction details from PayPal

#### 2. PayPalPaymentController (`app/Http/Controllers/Frontend/PayPalPaymentController.php`)

Handles payment flow:

**Routes:**
- `POST /paypal/pay/{order}` - Initiates payment
  - Validates amount
  - Creates OrderBrandPayment record
  - Redirects to PayPal

- `GET /paypal/success/{brandPayment}` - Success callback
  - Captures PayerID from PayPal
  - Executes approved payment
  - Notifies admins and brand

- `GET /paypal/cancel` - Cancellation
  - Deletes pending payment record
  - Redirects back to order

- `POST /paypal/notify` - IPN webhook
  - Receives PayPal IPN notifications
  - Verifies authenticity
  - Updates payment status

#### 3. View Changes

Updated `/resources/views/frontend/orders/partials/payment-section.blade.php`:

**Features:**
- Tab interface for payment method selection
- Manual payment form (existing)
- PayPal payment form (new)
- Shows payment method badge on submitted payments
- Displays PayPal transaction ID when available

### Usage

#### For Brands

1. Navigate to order detail page
2. In "Order Payment" section, choose payment method:
   - **Manual Payment**: Enter amount, reference/invoice number, optional note → Submit
   - **PayPal**: Enter amount, optional note → Click "Pay with PayPal"
3. If PayPal:
   - Redirected to PayPal login
   - Review and approve payment
   - Automatically submitted for admin review
4. View submission status and admin decisions

#### For Admins

Same existing workflow:
1. View pending payments in order dashboard
2. Confirm payment (system auto-confirms PayPal "Completed" payments)
3. Reject with note if needed
4. Brand notified of decision

### Payment Status Flow

```
Manual Payment:
pending → (admin review) → confirmed/rejected

PayPal Payment:
pending → (payment approved) → auto-confirmed OR
pending → (admin review) → confirmed/rejected
```

### Error Handling

**PayPal Service Errors:**
- Logs all errors to `storage/logs/laravel.log`
- User-friendly error messages
- Automatic payment record cleanup on cancellation
- Failed payments stay in "pending" for admin review/retry

**IPN Errors:**
- Invalid requests return 400
- Verification failures logged but don't block
- Transaction not found logged as warning
- Status updates idempotent (safe to process twice)

### Security

1. **Authentication**: All routes require authenticated brand user
2. **Authorization**: Brands can only pay for their orders
3. **PayPal Verification**: IPN messages verified with PayPal servers
4. **Transaction IDs**: Unique database constraints prevent duplicates
5. **CSRF Protection**: All forms include CSRF tokens
6. **Amount Validation**: Validates positive amounts before PayPal redirect

### Testing

#### Manual Testing (Sandbox)

1. Set `PAYPAL_MODE=sandbox` in .env
2. Use sandbox client credentials
3. Create test order as brand user
4. Click "Pay with PayPal"
5. Use test account credentials on PayPal
6. Complete payment flow
7. Check notification in admin dashboard

#### Sample Sandbox Accounts
- **Buyer**: Use any sandbox account or create test account
- **Seller**: Log into Business account to receive payments

### Webhook Testing

Use PayPal IPN Simulator in Developer Dashboard:
1. Tools → IPN → Send IPN Message
2. Select transaction type (Web Accept, Payment Refund, etc.)
3. Edit values as needed
4. Send to: `http://qx.local/paypal/notify`
5. Check logs to confirm processing

### Monitoring

**Key Logs:**
- Payment initiation: `app/Services/PayPalService.php::createApprovalLink()`
- Payment execution: `app/Services/PayPalService.php::executeApprovedPayment()`
- IPN processing: `app/Services/PayPalService.php::processIpnNotification()`

**Database Queries:**
```sql
-- View all PayPal payments
SELECT * FROM order_brand_payments WHERE payment_method = 'paypal';

-- View failed PayPal transactions
SELECT * FROM order_brand_payments WHERE payment_method = 'paypal' AND status = 'pending' AND paypal_transaction_id IS NULL;

-- Check payment status by order
SELECT * FROM order_brand_payments WHERE order_id = ? ORDER BY created_at DESC;
```

### Troubleshooting

#### Payment redirects to PayPal but nothing happens
- Check `PAYPAL_MODE` and credentials in .env
- Verify credentials are correct in PayPal Developer Dashboard
- Check `storage/logs/laravel.log` for errors

#### IPN notifications not being processed
- Verify IPN URL in PayPal account settings
- Check webhook endpoint accessibility from PayPal servers
- Test with PayPal IPN Simulator
- Check logs for "IPN verification failed"

#### Transaction ID not stored
- Ensure srmklive/paypal package is updated
- Check PayPal API response in logs
- Verify PayPal API credentials are valid

#### Payment stuck in "pending"
- Check admin notification was sent (might be in spam)
- Manually process with admin review interface
- Check OrderBrandPayment record in database

### Future Enhancements

1. **Partial Refunds** - Allow refunding specific amounts
2. **Payment Plans** - Multi-installment PayPal payments
3. **Currency Handling** - Dynamic currency conversion
4. **Webhook Retry** - Automatic IPN retry on failure
5. **Reconciliation Report** - Daily PayPal vs system reconciliation
6. **Payment Analytics** - Dashboard charts for payment trends

### Related Files

- Model: `app/Models/OrderBrandPayment.php`
- Controller: `app/Http/Controllers/Frontend/OrderController.php` (existing manual payment)
- Views: `resources/views/frontend/orders/partials/payment-section.blade.php`
- Config: `config/paypal.php`
- Routes: `routes/web.php`
- Migration: `database/migrations/2026_05_14_000001_add_paypal_fields_to_order_brand_payments.php`
