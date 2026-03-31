# Stripe Integration - User Setup Checklist

## 🎯 Your Task: Complete These 3 Steps

### STEP 1: Get Stripe API Keys ⏱️ (2 minutes)

- [ ] Open https://dashboard.stripe.com/apikeys in browser
- [ ] You're in TEST mode (that's correct for now)
- [ ] Look for two boxes on the page:
  - [ ] Find "Publishable key" starting with `pk_test_`
  - [ ] Find "Secret key" starting with `sk_test_`
- [ ] Copy the Publishable key (hover, it will show copy button)
- [ ] Copy the Secret key (hover, it will show copy button)
- [ ] Note: Keep these safe! Don't share them or commit to git

### STEP 2: Update Your .env File ⏱️ (1 minute)

- [ ] Open `/var/www/rockies/.env` in your text editor
- [ ] Find the line: `APP_NAME=Rockies` (or similar)
- [ ] Go to the end of the file
- [ ] Add these two lines:

```
STRIPE_PUBLIC_KEY=pk_test_PASTE_YOUR_KEY_HERE
STRIPE_SECRET_KEY=sk_test_PASTE_YOUR_KEY_HERE
```

- [ ] Replace `pk_test_PASTE_YOUR_KEY_HERE` with your actual Publishable key
- [ ] Replace `sk_test_PASTE_YOUR_KEY_HERE` with your actual Secret key
- [ ] Save the file (Ctrl+S or Cmd+S)

**Example of what it should look like:**
```
STRIPE_PUBLIC_KEY=pk_test_51234567890abcdefghijklmnopqrstuvwxyz
STRIPE_SECRET_KEY=sk_test_abcdefghijklmnopqrstuvwxyz1234567890
```

### STEP 3: Install & Run Migration ⏱️ (2 minutes)

- [ ] Open Terminal/Command Prompt
- [ ] Navigate to your project:
  ```
  cd /var/www/rockies
  ```
- [ ] Install Stripe PHP package:
  ```
  composer require stripe/stripe-php
  ```
  (Wait for it to finish - should show "composer.lock" updated)

- [ ] Run database migration:
  ```
  php artisan migrate
  ```
  (You should see a message like "Migration table created successfully" or "Migrated")

---

## ✅ You're Done With Setup!

Now test it to make sure everything works.

---

## 🧪 Testing Your Integration

### Test The Payment System:

1. [ ] Go to your app in browser: `http://localhost:8000/dashboard/account`
2. [ ] Click the **"Payment"** tab (you should see "Payment Methods" section)
3. [ ] Click the **"+ Add Payment Card"** button (a modal/popup should open)
4. [ ] Enter a test card number: **4242 4242 4242 4242**
5. [ ] Expiry date: Use any **future month/year** (e.g., 12/26)
6. [ ] CVC: Enter any **3 digits** (e.g., 123)
7. [ ] Check: **"Set as default payment method"** checkbox (optional)
8. [ ] Click: **"Save Card"** button
9. [ ] Expected result: Card appears in list below as "VISA •••• 4242"

✅ If you see the card added successfully → Everything works!
❌ If you get an error → Check that Stripe keys are correct in .env

### Verify in Stripe Dashboard:

1. [ ] Go back to https://dashboard.stripe.com/
2. [ ] Click **"Customers"** in left menu
3. [ ] You should see a customer created (matching your email)
4. [ ] Click on that customer
5. [ ] You should see the payment method (card) listed

✅ If you see the customer and card → Full integration working!

---

## 📋 Common Issues & Solutions

### Issue: "Stripe keys not configured" error appears

**Solution:**
1. Double-check `.env` file has both keys spelled correctly
2. Keys should NOT have quotes around them
3. Save the file
4. Run: `php artisan config:clear`
5. Refresh browser page

### Issue: "Command 'composer' not found"

**Solution:**
1. Make sure you're in the `/var/www/rockies` directory
2. Check that Composer is installed: `composer --version`
3. If not installed, install from https://getcomposer.org/

### Issue: Migration error "column already exists"

**Solution:**
1. This is OK! It means the column was already created
2. Just try adding a card again - it should work

### Issue: Card form shows but nothing happens when I click "Save Card"

**Solution:**
1. Check browser console (F12 → Console tab)
2. Look for red error messages
3. Common issues:
   - Stripe keys not valid/configured
   - JavaScript not loading properly
   - Network connection issue
4. Try refreshing the page and trying again

---

## 🎓 What Just Happened (Optional Reading)

### The Payment Flow:
1. User fills out card form on your website
2. Stripe.js encrypts the card information
3. Stripe API tokenizes it (creates a `pm_*` token)
4. Only the token is sent to your server (not the card!)
5. Your server stores the token with Stripe
6. User's card is now saved securely

### Why This Is Secure:
- Your servers **never** see the card number
- Your servers **never** see the CVV
- Stripe handles all card encryption
- You're PCI-DSS compliant automatically
- Card data is protected at all times

---

## 📞 Need Help?

### Where to Look:
1. **Quick reference**: Read `STRIPE_QUICKSTART.md`
2. **Detailed setup**: Read `STRIPE_SETUP_GUIDE.md`
3. **Technical details**: Read `STRIPE_IMPLEMENTATION_SUMMARY.md`
4. **Validation**: Read `STRIPE_VALIDATION_REPORT.md`

### Troubleshooting:
1. Check that .env file exists in `/var/www/rockies/` directory
2. Verify Stripe keys are pasted correctly (no extra spaces)
3. Verify migration ran successfully
4. Check browser console (F12) for JavaScript errors

---

## 🎉 That's It!

You now have a complete, secure payment system!

Your users can:
- ✅ Add payment cards safely
- ✅ Set a default card
- ✅ Delete saved cards
- ✅ See card status (expired, expiring soon)

Everything is encrypted and PCI-DSS compliant.

---

## 🚀 Next Steps (When You're Ready)

After you've confirmed everything works:

1. **Set up payments** - Charge cards for orders/services
2. **Add subscriptions** - Recurring billing for memberships
3. **Configure webhooks** - Handle payment events
4. **Go to production** - Replace test keys with live keys
5. **Monitor payments** - Track all transactions in Stripe Dashboard

---

**Status**: ✅ Ready to start

You've got everything installed. Now just add your Stripe keys and run the migration!

Good luck! 🚀
