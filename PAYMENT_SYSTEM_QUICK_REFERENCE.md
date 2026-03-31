# Payment System - Developer Quick Reference

## Quick Start

### Display Payment Methods for User
```blade
@foreach($user->paymentMethods as $card)
    <div>{{ $card->display_name }} - Expires {{ $card->formatted_expiry }}</div>
@endforeach
```

### Get Default Card
```php
$card = $user->paymentMethods()->where('is_default', true)->first();
```

### Add a Card (From Form)
```php
// In Controller - form data already validated by StorePaymentMethodRequest
$card = Auth::user()->paymentMethods()->create($validated);
```

### Make Card Default
```php
$card->update(['is_default' => true]);
// Auto-hook handles unsetting other defaults
```

### Record Payment with Card
```php
$payment = Payment::create([
    'order_id' => $order->id,
    'payment_method_id' => $card->id,
    'amount' => $amount,
    'status' => 'captured'
]);
```

---

## Routes

### User-Facing Routes (requires auth)

| Method | Route | Name | Purpose |
|--------|-------|------|---------|
| POST | `/dashboard/payment-methods` | `payment-methods.store` | Add new card |
| POST | `/dashboard/payment-methods/{id}/set-default` | `payment-methods.set-default` | Set default |
| DELETE | `/dashboard/payment-methods/{id}` | `payment-methods.destroy` | Delete card |

### AJAX API Routes

| Method | Route | Name |
|--------|-------|------|
| GET | `/dashboard/payment-methods/api/list` | `payment-methods.api.list` |
| GET | `/dashboard/payment-methods/api/default` | `payment-methods.api.default` |

---

## Validation Rules

### Card Brand
```
Required, one of: visa, mastercard, amex, discover, diners, jcb, unionpay
```

### Last 4 Digits
```
Required, exactly 4 digits (0-9 only)
```

### Expiry
```
Month: 1-12
Year: Current year to +20 years
```

---

## Model Helpers

### Check Card Status
```php
if ($card->isExpired()) { }          // Past expiration
if ($card->isExpiringSoon()) { }     // Within 30 days
if ($card->isDefault()) { }          // Is default card
```

### Get Display Info
```php
$card->display_name          // "Visa ending in 4242"
$card->formatted_expiry      // "03/29"
$card->brand                 // "visa"
$card->last4                 // "4242"
```

---

## Query Scopes

```php
// Get all Stripe cards
$user->paymentMethods()->stripe()->get()

// Get all manual cards
$user->paymentMethods()->manual()->get()

// Get non-expired cards
$user->paymentMethods()->valid()->get()

// Order by default first
$user->paymentMethods()->orderBy('is_default', 'desc')->get()
```

---

## Payment Model

### Link Payment to Card
```php
$payment->paymentMethod    // Get the card used
$payment->paymentMethod->display_name
```

### Check Payment Status
```php
$payment->isSuccessful()    // authorized or captured
$payment->isPending()
$payment->isFailed()
$payment->markAsPaid()
```

### Query Payments
```php
Payment::successful()->get()
Payment::pending()->get()
Payment::failed()->get()
```

---

## Blade Template Helpers

### List User Cards
```blade
@forelse($user->paymentMethods as $card)
    <div>
        <strong>{{ $card->brand }}</strong> ending in {{ $card->last4 }}
        @if($card->isExpired())
            <span class="badge badge-danger">Expired</span>
        @elseif($card->isExpiringSoon())
            <span class="badge badge-warning">Expiring Soon</span>
        @endif
        @if($card->is_default)
            <span class="badge badge-primary">Default</span>
        @endif
    </div>
@empty
    <p>No payment methods found</p>
@endforelse
```

### Add Card Form
```blade
<form action="{{ route('dashboard.payment-methods.store') }}" method="POST">
    @csrf
    <select name="brand" required>
        <option value="visa">Visa</option>
        <option value="mastercard">Mastercard</option>
        <option value="amex">Amex</option>
    </select>
    
    <input type="text" name="last4" placeholder="4242" maxlength="4" required>
    
    <select name="expiry_month" required>
        @for($i = 1; $i <= 12; $i++)
            <option value="{{ $i }}">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
        @endfor
    </select>
    
    <select name="expiry_year" required>
        @for($i = 0; $i <= 20; $i++)
            <option value="{{ now()->year + $i }}">{{ now()->year + $i }}</option>
        @endfor
    </select>
    
    <label>
        <input type="checkbox" name="is_default"> Set as default
    </label>
    
    <button type="submit">Add Card</button>
</form>
```

---

## Billing Profile Notes

### Add Billing Info for Brand
```php
$brand->billingProfiles()->updateOrCreate(
    ['user_id' => $brand->id, 'user_type' => 'brand'],
    [
        'legal_company_name' => 'Company LLC',
        'vat_id' => 'US123456789',
        'billing_address' => '123 Main St',
        'billing_city' => 'San Francisco',
        'billing_country' => 'USA',
        'billing_postal_code' => '94102'
    ]
);
```

### Check Billing Profile Completeness
```php
$profile = $brand->billingProfiles()->first();
if ($profile->isComplete()) {
    // All required fields filled
}
```

---

## Security Checklist

- ✅ Never store full card numbers
- ✅ Never store CVV
- ✅ Always verify user owns the payment method
- ✅ Use encryption for provider tokens
- ✅ Validate expiry dates
- ✅ Check ownership before operations
- ✅ Use HTTPS for all payments
- ✅ Follow PCI DSS compliance

---

## Common Operations

### Checkout Flow
```php
// 1. Get user's default card
$card = Auth::user()->paymentMethods()->where('is_default', true)->first();

// 2. If no default, prompt for selection
if (!$card) {
    $cards = Auth::user()->paymentMethods;
    // Show card selection form
}

// 3. Process payment with selected card
$payment = processPayment($order, $card);

// 4. Record in database
$order->payments()->create([
    'payment_method_id' => $card->id,
    'amount' => $order->total,
    'status' => 'captured'
]);
```

### Account Settings Page
```php
// Show in Payment tab - already implemented in account.blade.php
// Users can:
// - Add new cards
// - View all saved cards
// - Set default card
// - Delete cards
```

---

## Troubleshooting

### Card not appearing in list
```php
// Check user relationship
$user->paymentMethods()->count()

// Check query
PaymentMethod::where('user_id', $user->id)->get()
```

### Default card not updating
```php
// Boot hook automatically handles this
// If manual update needed:
$user->paymentMethods()->update(['is_default' => false]);
$card->update(['is_default' => true]);
```

### Deletion cascading issues
```php
// Foreign key configured with nullOnDelete
// Payment records are preserved, just lose card reference
// This is intentional for audit trail
```

---

## Future Enhancements

1. **Stripe Integration**
   - Store payment_intent ID
   - Handle webhook events
   - Automatic card sync

2. **Payment History**
   - Transaction logs
   - Receipts
   - Refunds

3. **Analytics**
   - Payment failure tracking
   - Card usage metrics
   - Conversion optimization

4. **Testing**
   - Unit tests for PaymentMethod
   - Feature tests for checkout flow
   - Integration tests with Stripe

---

Last Updated: March 30, 2026
