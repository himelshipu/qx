# Quick Start - PayPal Integration Setup

## 1️⃣ Configuration (5 minutes)

Add these to your `.env` file:

```env
PAYPAL_MODE=sandbox
PAYPAL_SANDBOX_CLIENT_ID=your_sandbox_client_id
PAYPAL_SANDBOX_CLIENT_SECRET=your_sandbox_secret
PAYPAL_CURRENCY=USD
PAYPAL_NOTIFY_URL=http://qx.local/paypal/notify
```

**Get credentials:**
1. Go to [PayPal Developer Dashboard](https://developer.paypal.com)
2. Create App > My Apps & Credentials > Sandbox
3. Create app > Copy Client ID and Secret

## 2️⃣ PayPal Account Setup (5 minutes)

1. Log into PayPal Business Account
2. Go to: Settings > Notifications > IPN
3. Click "Update" and enter: `http://qx.local/paypal/notify`
4. Enable all event types
5. Save

## 3️⃣ Test & Go (10 minutes)

### Test Brand Payment
1. Log in as brand
2. Go to any order
3. Click "Order Payment" tab
4. Select "PayPal" tab
5. Enter amount → Click "Pay with PayPal"
6. Use PayPal sandbox test account
7. Approve payment
8. Return to order (automatic redirect)
9. Should show: "Payment submitted successfully!"

### Verify Admin Dashboard
1. Log in as admin
2. Go to: Dashboard > Orders
3. Open order with payment
4. Scroll to "Brand Payment Review"
5. Should see payment with "PayPal" badge and Transaction ID
6. Click "Confirm" to approve

## 4️⃣ Production Deployment (15 minutes)

1. **Update Credentials:**
   ```env
   PAYPAL_MODE=live
   PAYPAL_LIVE_CLIENT_ID=your_live_id
   PAYPAL_LIVE_CLIENT_SECRET=your_live_secret
   PAYPAL_NOTIFY_URL=https://qx.com/paypal/notify
   ```

2. **Update PayPal Account:**
   - Change IPN URL to production domain
   - Test IPN with production URL

3. **Test Full Flow:**
   - Create test order
   - Test PayPal payment with live account
   - Verify admin confirmation works
   - Monitor logs for errors

## 📊 What Users See

### Brands
- **New Payment Option**: PayPal tab in order payment section
- **Simple Form**: Amount pre-filled, optional note
- **Secure Redirect**: PayPal handles card details
- **Instant Confirmation**: "Payment submitted successfully!" message
- **Status Tracking**: Watch payment status in submission list

### Admins
- **PayPal Badge**: Easy to identify PayPal payments
- **Transaction ID**: For verification purposes
- **Auto-Confirmation**: PayPal payments marked confirmed if verified
- **Manual Review**: Can still confirm/reject for extra verification
- **Audit Trail**: All payment details recorded

## 🔍 Key Files

| File | Purpose |
|------|---------|
| `app/Services/PayPalService.php` | Core PayPal logic |
| `app/Http/Controllers/Frontend/PayPalPaymentController.php` | Payment flow controller |
| `resources/views/frontend/orders/partials/payment-section.blade.php` | Brand UI (tabbed) |
| `resources/views/backend/pages/orders/show.blade.php` | Admin UI (shows transaction ID) |
| `config/paypal.php` | PayPal configuration |
| `database/migrations/2026_05_14_000001_add_paypal_fields_to_order_brand_payments.php` | Database schema |

## 🆘 Troubleshooting

### "PayPal payment not working"
- [ ] Check `.env` has PAYPAL_MODE and credentials
- [ ] Verify credentials are correct in PayPal Developer Dashboard
- [ ] Check `storage/logs/laravel.log` for errors

### "IPN not processing"
- [ ] Verify IPN URL in PayPal account settings
- [ ] Test with PayPal IPN Simulator
- [ ] Check if firewall allows PayPal to reach URL

### "Payment stuck in pending"
- [ ] Manually confirm in admin dashboard
- [ ] Check PayPal transaction history for transaction ID
- [ ] Review logs for IPN errors

## 📞 Support Files

- **PAYPAL_SETUP.md** - Complete documentation
- **PAYPAL_ADMIN_GUIDE.md** - Admin user manual
- **PAYPAL_IMPLEMENTATION.md** - Technical details

## ✅ Checklist

- [ ] Add PayPal credentials to .env
- [ ] Test with sandbox account
- [ ] Configure IPN webhook
- [ ] Test brand payment flow
- [ ] Test admin confirmation
- [ ] Deploy to production
- [ ] Update production credentials
- [ ] Test on production
- [ ] Monitor logs
- [ ] Train team on payment review

---

**Questions?** See full documentation in PAYPAL_SETUP.md
