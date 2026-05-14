# PayPal Payments - Admin Guide

## Overview

Brands can now pay for orders via PayPal (Express Checkout) or manual submission. All payments go through admin review before confirmation. PayPal payments are automatically verified through PayPal's IPN (Instant Payment Notification) webhook.

## Payment Status Lifecycle

### Manual Payments
```
Brand submits form → Pending (Review) → Confirmed/Rejected
```

### PayPal Payments
```
Brand clicks PayPal → Redirected to PayPal → Approves payment → 
Returns to system → Payment executed → Auto-submitted for review → 
Pending (Review) → Confirmed/Rejected
```

## Admin Dashboard

### Order Payment Review Section

Located at: Dashboard > Orders > [Order ID] > "Brand Payment Review" section

**What you see:**
- Payment status badge (Pending, Confirmed, Rejected)
- Payment amount and currency
- Payment method: Manual or PayPal badge
- Submission date/time
- For PayPal: Transaction ID and auto-confirmation status
- For Manual: Reference number and Invoice ID
- Brand notes (if provided)
- Admin notes (if previously reviewed)

## Reviewing Payments

### Confirming a Payment

1. Locate the payment in "Brand Payment Review" section
2. Enter optional admin note (e.g., "Verified with bank statement")
3. Click **"Confirm"** button
4. Payment status changes to "Confirmed"
5. Brand receives notification

### Rejecting a Payment

1. Locate the payment in "Brand Payment Review" section
2. Enter rejection reason in admin note field (required)
3. Click **"Reject"** button
4. Payment status changes to "Rejected"
5. Brand receives notification with reason

### Reviewing PayPal Payments

PayPal payments show additional information:
- **Payment Method Badge**: Blue "PayPal" badge
- **Transaction ID**: Unique identifier from PayPal
- **Auto-Status**: Payment may auto-confirm if PayPal webhook processed successfully

**Important:** PayPal payments with status "Confirmed" and a Transaction ID have been verified by PayPal. You can typically confirm these without additional verification.

**To verify a PayPal payment:**
1. Note the Transaction ID
2. Log into PayPal business account
3. Go to Reports > Transaction history
4. Search for the Transaction ID
5. Verify amount and brand email match
6. Return to dashboard and confirm

## Key Information to Check

### For Manual Payments
- Reference number or Invoice ID provided
- Amount matches order total or partial balance
- Brand notes provide context (invoice screenshot, etc.)
- Check PayPal payment queue if suspicious

### For PayPal Payments
- Transaction ID is present (15-character PayPal ID)
- Amount matches requested payment
- Status should auto-confirm if IPN webhook worked
- If still "Pending" with Transaction ID, manually confirm after verification

## Common Scenarios

### PayPal Payment Stuck in "Pending"

**Possible causes:**
- IPN webhook didn't fire (network issues)
- PayPal credentials misconfigured

**Resolution:**
1. Check the Transaction ID is present
2. Manually verify in PayPal account
3. Click Confirm to finalize

### Brand Claims Payment Sent But Status is Pending

**For Manual Payments:**
- Ask for transaction proof (screenshot, bank transfer receipt)
- If verified, manually confirm payment
- Add admin note explaining verification source

**For PayPal Payments:**
- Ask for PayPal transaction ID
- Search in PayPal transaction history
- If found and amounts match, manually confirm
- Add admin note with "Verified via PayPal: [TXN_ID]"

### Multiple Payment Submissions for Same Order

This is normal. Brands may submit multiple partial payments or retry PayPal.

**Best practices:**
- Confirm payments as submitted
- Track cumulative confirmed amount against order total
- Reject obvious duplicates with note
- Monitor "Balance Due" field (auto-calculated)

## Payment Totals

At top of "Brand Payment Review" section, you'll see:
- **Confirmed**: Total of all confirmed payments
- **Pending**: Total waiting for review
- **Balance**: Remaining amount due from brand (may be negative if overpaid)

## Notifications

When you confirm/reject payments:
- Brand receives email notification
- Admin notification sent to Admins & Moderators
- Notification includes amount, order number, and your admin note

## Order Payment States

Based on total submitted vs confirmed:

| State | Meaning | Action |
|-------|---------|--------|
| **Unpaid** | No payments submitted | Wait for brand to submit |
| **Awaiting Review** | Payments submitted, none confirmed | Review pending payments |
| **Partially Paid** | Some confirmed, balance remains | Confirmed portion, request remainder |
| **Underpaid** | All submitted confirmed, but less than total | Request additional payment |
| **Overpaid** | Confirmed amount exceeds order total | Mark as cleared or offer refund |
| **Cleared** | Confirmed amount equals order total | No further action needed |

## Payment Method Comparison

### Manual Payment
**Advantages:**
- No 3rd party dependencies
- Works with bank transfers, checks, wire transfers
- Lower fraud risk (you review each one)

**Disadvantages:**
- Requires manual verification
- Slower payment confirmation
- Requires admin review time

### PayPal Payment
**Advantages:**
- Instant payment confirmation
- PayPal verifies cardholder
- Less admin review time (auto-confirms completed payments)
- Transaction verification available

**Disadvantages:**
- PayPal fees apply (~3%)
- Dependent on PayPal's systems
- IPN webhook could fail to notify

## Troubleshooting

### Payment Not Appearing

1. Check if order is correct
2. Refresh page
3. Check order's payment tab specifically
4. Check if payment is for different order

### Can't Confirm Payment

- Check if you have "Orders" permission
- Verify not already confirmed/rejected
- Check for validation errors in form

### Brand Says They Paid via PayPal But No Transaction ID

1. Ask brand for PayPal receipt/confirmation email
2. Search PayPal for transaction
3. If real, check IPN webhook configuration
4. Can manually confirm based on PayPal verification

## Reporting

### View All Brand Payments

Database query (via Tinker):
```php
// View all payments
OrderBrandPayment::with(['order', 'brandUser'])->get();

// View PayPal payments only
OrderBrandPayment::where('payment_method', 'paypal')->get();

// View pending payments
OrderBrandPayment::where('status', 'pending')->get();

// View for specific order
OrderBrandPayment::where('order_id', $orderId)->get();
```

### Export Payment Report

Via dashboard: Reports > Payment Audit (if available)

## Security Notes

- All PayPal transactions verified with PayPal servers
- Transaction IDs are unique per payment
- Brand can only submit payments for their own orders
- All admin actions logged in audit trail
- Payments cannot be edited, only confirmed/rejected

## Need Help?

- PayPal credentials issue? Check: `config/paypal.php` and `.env`
- IPN webhook not working? Check: IPN URL in PayPal settings
- Refund needed? Contact administrator or PayPal directly
- Order dispute? Escalate to admin lead
