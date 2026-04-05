# Cart & Checkout System - Complete Testing Guide

## System Ready ✅

All cart functionality is now implemented and routes are registered. This guide walks through testing the complete purchase flow.

---

## Test Scenario: Brand Purchases Package from Creator

### Prerequisites

- Database seeded with Creators and Packages
- Laravel development server running on port 9000
- A user account logged in as a **Brand** user

---

### Step 1: Navigate to Creator Profile

1. Go to: `http://localhost:9000/creator/{slug}`
    - Replace `{slug}` with actual creator slug from database
    - Or find a creator via `/influencers` page

2. You should see:
    - Creator profile information
    - Package selection dropdown
    - **"Add to Cart" button** (this is the form submission point)
    - Package details (price, description)

---

### Step 2: Add Package to Cart

1. Select a package from the dropdown (if not already selected)
2. Click the **"Add to Cart"** button
3. **Expected Result:**
    - Form POSTs to `/cart/add` route
    - Success message appears: `"Package added to cart! View your cart"`
    - Redirected back to creator profile (or automatically)

**What Happens Behind the Scenes:**

- CartController@add() validates package_id
- Cart created/retrieved for current user
- CartItem added with quantity=1 and unit_price=package.base_price
- Cart totals updated

---

### Step 3: View Shopping Cart

1. Navigate to: `http://localhost:9000/cart`
2. **Expected Display:**
    - Cart item table showing:
        - Creator name
        - Package name
        - Unit price
        - Quantity (with input field)
        - Total per item
        - Remove button (✕)
    - Cart summary:
        - Subtotal
        - Tax (10%)
        - Shipping (Free)
        - **Total price**
    - **"Proceed to Checkout"** button
    - "Clear Cart" option
    - "You'll be able to message the creators after checkout" message

3. **Test Quantity Update (Optional):**
    - Change quantity in input field
    - Cart totals should update automatically
    - Verify PUT to `/cart/items/{cartItem}` works

4. **Test Remove Item (Optional):**
    - Click remove button (✕) on an item
    - Item deleted from cart
    - Totals recalculated
    - Verify DELETE to `/cart/items/{cartItem}` works

---

### Step 4: Proceed to Checkout

1. Click **"Proceed to Checkout"** button
2. **Expected Result:**
    - Form POSTs to `/cart/checkout`
    - Page displays checkout review showing:
        - Order review title
        - Item summary table with:
            - Creator name
            - Package name
            - Quantity
            - Unit price
            - Total per item
        - "What happens next?" information box explaining:
            - Your order will be confirmed
            - Creator will receive a message from you
            - You can communicate through messaging platform
        - **Total order amount**
        - **"Complete Order"** button

**What Happens Behind the Scenes:**

- CartController@checkout() loads cart items with package/creator details
- Returns checkout.blade.php view with order summary

---

### Step 5: Complete the Purchase

1. Click **"Complete Order"** button
2. **Expected Result:**
    - Form POSTs to `/cart/complete-checkout`
    - Success message: `"Order created! Check your conversations to message the creator"`
    - Redirected to `/cart` page (now empty)

**What Happens Behind the Scenes:**

- CartController@completeCheckout() processes order creation:
    1. For each item in cart:
        - Creates Order record with:
            - order_number: ORD-{uniqid}
            - buyer_user_id: current user
            - total_amount: unit_price × quantity
            - status: pending
            - placed_at: now()
        - Creates OrderItem record linking order to package
        - Auto-creates Conversation between buyer and creator
    2. Clears all items from cart
    3. Resets cart totals to 0

---

### Step 6: Verify Conversation Created

1. Navigate to: `http://localhost:9000/dashboard/conversations`
2. **Expected Display:**
    - New conversation should appear in list
    - Conversation shows:
        - Creator name
        - Package name
        - Purchase-related context

3. Click on conversation
4. **Expected Display:**
    - Chat interface
    - "Message input" box for brand user
    - System message explaining:
        - Messages go through moderator
        - Creator responses come to brand

---

## Advanced Testing Scenarios

### Scenario A: Add Multiple Packages

1. Add package from Creator A
2. Add package from Creator B
3. Go to cart
4. Should show 2 items
5. Proceed to checkout
6. Complete order
7. **Expected Result:**
    - 2 separate orders created (one per creator)
    - 2 separate conversations created

**Why:** Each order is created per cart item, allowing independent communication with each creator

---

### Scenario B: Duplicate Package Addition

1. Add package to cart
2. View cart - quantity shows 1
3. Go back to same creator profile
4. Click "Add to Cart" again for same package
5. **Expected Result:**
    - Quantity increments to 2 (not adding duplicate item)
    - Totals updated accordingly

**Why:** CartController@add() checks for existing items and increments quantity

---

### Scenario C: Cart Persistence

1. Add package to cart
2. Close browser / navigate away
3. Return to `/cart` in new session
4. **Expected Result:**
    - Cart items still there
    - Cart data persisted in database

**Why:** Cart is keyed by `buyer_user_id` and stored in database

---

### Scenario D: Checkout without clicking Review

1. Add package to cart
2. Manually navigate to `/cart/checkout` (POST request)
    - This won't work with GET - use browser DevTools to simulate POST
    - OR click "Proceed to Checkout" normally
3. You should see checkout review page (not checkout form)

**Note:** Cart system uses POST for cart.checkout (unusual but works) to immediately show review

---

## Database Verification

After completing a purchase, verify the database:

### Check Orders Created

```sql
SELECT * FROM orders WHERE buyer_user_id = {current_user_id};
```

**Expected columns:**

- order_number (ORD-{uniqid})
- buyer_user_id
- total_amount
- currency (USD)
- status (pending)
- placed_at (timestamp)

### Check Order Items

```sql
SELECT oi.*, p.name as package_name
FROM order_items oi
JOIN packages p ON oi.package_id = p.id
WHERE oi.order_id = {order_id};
```

**Expected columns:**

- order_id (foreign key)
- package_id (foreign key)
- quantity
- unit_price (from package.base_price at purchase time)
- total (unit_price × quantity)

### Check Conversations Created

```sql
SELECT c.*, u.name as creator_name, b.name as buyer_name
FROM conversations c
JOIN users u ON c.creator_id = u.id
JOIN users b ON c.buyer_user_id = b.id
WHERE c.buyer_user_id = {current_user_id};
```

**Expected columns:**

- id
- buyer_user_id
- creator_id
- moderator_user_id (may be null initially)
- name/context
- created_at

### Check Cart Cleared

```sql
SELECT * FROM carts WHERE buyer_user_id = {current_user_id};
SELECT * FROM cart_items WHERE cart_id = {cart_id};
```

**Expected after purchase:**

- Cart exists but total_items = 0, total_price = 0
- No cart_items rows

---

## Error Handling Tests

### Test 1: Invalid Package ID

1. Manually POST to `/cart/add` with `package_id=9999`
2. **Expected Result:**
    - Validation error: "The selected package_id is invalid"
    - Redirected back with error message

---

### Test 2: Unauthenticated Access

1. Log out
2. Try to access `/cart`
3. **Expected Result:**
    - Redirected to login page
    - After login, back to `/cart`

---

### Test 3: Empty Cart Checkout

1. Clear cart completely
2. Try to navigate to checkout
3. **Expected Result:**
    - Error message: "Your cart is empty"
    - Redirected to `/cart`

---

## Performance Metrics

After testing, you can verify performance:

### Database Queries (Laravel Debugbar)

- View cart should load items with package and creator data in minimal queries
- Checkout should prepare all data before showing view

### Response Times

- Add to cart: < 200ms
- View cart: < 200ms
- Proceed to checkout: < 200ms
- Complete order: < 500ms (creates multiple records)

---

## Security Checks ✅

- ✅ All cart routes require `auth` middleware
- ✅ Only authenticated users can add/view/modify carts
- ✅ Users can only modify their own cart (implicit through buyer_user_id)
- ✅ Package validation ensures only valid packages can be added
- ✅ Order creation includes proper user ID association

---

## Common Issues & Solutions

### Issue: "Add to Cart" button doesn't work

**Solution:**

- Verify form has action="{{ route('cart.add') }}"
- Verify form has @csrf token
- Verify method="POST"
- Check browser console for JavaScript errors
- Verify CartController is imported in routes

### Issue: Cart totals not updating

**Solution:**

- Check updateCartTotals() is being called after modifications
- Verify database cart record is updated
- Check that unit_price is numeric (using base_price)

### Issue: Conversation not created after purchase

**Solution:**

- Verify ConversationController::createForPackageOrder() method exists
- Check creator_id is correct in package record
- Verify moderator assignment logic if required

### Issue: "Order created" message but no order in database

**Solution:**

- Check Order model is using correct table name (orders)
- Verify OrderItem model and relationship
- Check database foreign key constraints

---

## Next Steps After Testing

Once the cart system is fully verified:

1. **Production Ready?**
    - Run full test suite
    - Load test with multiple concurrent users
    - Verify email notifications (if configured)
    - Test payment integration (if configured)

2. **User Acceptance Testing**
    - Test with actual brand users
    - Gather feedback on UX
    - Monitor for edge cases

3. **Analytics & Monitoring**
    - Track cart abandonment rates
    - Monitor purchase completion rates
    - Track average order value

4. **Future Enhancements**
    - Abandoned cart recovery
    - Coupon/discount codes
    - Saved cart/wishlists
    - One-click checkout
    - Payment processing integration

---

## Summary

The complete package purchase workflow is now implemented:

```
Creator Profile
  ↓ (Select package, click Add to Cart)
Cart Page
  ↓ (Review items, click Proceed to Checkout)
Checkout Review
  ↓ (Verify order details, click Complete Order)
Order Created + Conversation Initialized
  ↓ (Redirect to cart with success message)
Buyer Can Message Creator Through Conversation
```

All routes are registered, controllers are functional, and the database schema supports the complete flow.

**Status: READY FOR COMPREHENSIVE TESTING** ✅
