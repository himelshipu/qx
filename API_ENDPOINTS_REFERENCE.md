# API Endpoints Reference - Order Management

## Complete Endpoint Mapping

### 🌐 FRONTEND ENDPOINTS (Customer-Facing)

#### Orders Management
| Method | Endpoint | Route Name | Purpose | Auth |
|--------|----------|-----------|---------|------|
| GET | `/orders` | `frontend.orders.index` | List user's orders | Brand/Creator |
| GET | `/orders/{order}` | `frontend.orders.show` | View order details | Brand/Creator |

**Authorization Rules:**
- **Brand Users:** See orders they placed (`buyer_user_id === user.id`)
- **Creator Users:** See orders containing their items (`items.creator_id === user.creator.id`)

---

### 🎛️ ADMIN DASHBOARD ENDPOINTS

#### Orders Management
| Method | Endpoint | Route Name | Purpose | Controller Method |
|--------|----------|-----------|---------|-------------------|
| GET | `/dashboard/orders` | `dashboard.orders.index` | List all orders | `Backend\OrderController@index` |
| GET | `/dashboard/orders/{order}` | `dashboard.orders.show` | View order details | `Backend\OrderController@show` |
| **PUT** | `/dashboard/orders/{order}/status` | `dashboard.orders.update-status` | **✅ NEW** Update order status | `Backend\OrderController@updateStatus` |

#### Order Items Management
| Method | Endpoint | Route Name | Purpose | Controller Method |
|--------|----------|-----------|---------|-------------------|
| **PUT** | `/dashboard/order-items/{orderItem}/status` | `dashboard.order-items.update-status` | Update item status | `Backend\OrderController@updateOrderItemStatus` |
| **POST** | `/dashboard/order-items/{orderItem}/mark-paid` | `dashboard.order-items.mark-paid` | Mark item as paid | `Backend\OrderController@markOrderItemPaid` |

#### Sub-Orders Management (Campaign Orders)
| Method | Endpoint | Route Name | Purpose | Controller Method |
|--------|----------|-----------|---------|-------------------|
| PUT | `/dashboard/sub-orders/{subOrder}/status` | `dashboard.sub-orders.update-status` | Update sub-order status | `Backend\OrderController@updateSubOrderStatus` |
| POST | `/dashboard/sub-orders/{subOrder}/mark-paid` | `dashboard.sub-orders.mark-paid` | Mark sub-order as paid | `Backend\OrderController@markSubOrderPaid` |

#### Campaign Orders (Workflow A)
| Method | Endpoint | Route Name | Purpose | Controller Method |
|--------|----------|-----------|---------|-------------------|
| POST | `/dashboard/orders/create-from-campaign` | `dashboard.orders.create-from-campaign` | Create master order from campaign | `Backend\OrderController@createFromCampaign` |

---

## Request/Response Examples

### 1. Update Order Status (PUT)

**Endpoint:**
```
PUT /dashboard/orders/{order}/status
```

**Request Body:**
```json
{
  "status": "in_progress"
}
```

**Valid Status Values:**
- `pending`
- `accepted`
- `in-progress` (also accepts `in_progress`)
- `completed`
- `cancelled`

**Response:**
```
302 Redirect to /dashboard/orders/{order}
Session Flash: "Order status updated successfully"
```

**What Updates:**
```php
$order->status = 'in_progress';          // Status column
$order->accepted_at = now();             // If status = 'accepted' (first time only)
$order->completed_at = now();            // If status = 'completed'
$order->cancelled_at = now();            // If status = 'cancelled'
```

---

### 2. Update Item Status (PUT)

**Endpoint:**
```
PUT /dashboard/order-items/{orderItem}/status
```

**Request Body:**
```json
{
  "status": "in_progress"
}
```

**Valid Status Values:**
- `pending`
- `accepted`
- `in_progress`
- `delivered`
- `completed`
- `approved` (legacy)
- `rejected` (legacy)
- `cancelled`

**Response:**
```
302 Redirect to /dashboard/orders/{order}
Session Flash: "Order item status updated successfully"
```

**What Updates:**
```php
$orderItem->status = 'in_progress';  // Status column only
```

---

### 3. Mark Item as Paid (POST)

**Endpoint:**
```
POST /dashboard/order-items/{orderItem}/mark-paid
```

**Request Body:**
```
No body required (just CSRF token)
```

**Response:**
```
302 Redirect to /dashboard/orders/{order}
Session Flash: "Order item marked as paid"
```

**What Updates:**
```php
$orderItem->paid_at = now();  // Timestamp of payment
```

**After Update:**
- "Mark as Paid" button disappears from UI
- Item shows as "Paid" in UI
- Can only be undone by direct database update

---

### 4. List Orders (GET)

**Endpoint:**
```
GET /dashboard/orders
GET /dashboard/orders?q=ORD-123&status=pending
```

**Query Parameters:**
- `q` (optional): Search by order number, buyer name, email, brand name
- `status` (optional): Filter by status (`all`, `pending`, `completed`, etc.)

**Response:**
```
Returns: view('backend.pages.orders.index')
With: 
  - $orders (paginated, 15 per page)
  - $stats (total, pending, completed, revenue)
  - $search (search term used)
  - $status (filter used)
```

---

### 5. View Order Details (GET)

**Endpoint:**
```
GET /dashboard/orders/{order}
```

**Response:**
```
Returns: view('backend.pages.orders.show')
With:
  - $order (eager-loaded with relationships)
    - buyer (name, email, phone)
    - brand (brand_name)
    - campaign (title, status)
    - items (all order items with creators)
    - payments (payment history)
```

**Eager-Loaded Relationships:**
```php
'buyer:id,name,email,phone',
'brand:id,brand_name',
'campaign:id,title,status',
'items:id,order_id,creator_id,package_id,title,quantity,unit_price,line_total,status,due_date,paid_at',
'items.creator:id,user_id,display_name',
'items.creator.user:id,name',
'items.package:id,name,base_price,currency',
'payments:id,order_id,status,amount,currency,payment_provider,paid_at,created_at'
```

---

### 6. View Customer Order (GET)

**Endpoint (Frontend):**
```
GET /orders/{order}
```

**Authorization:**
```php
// Brand: Must be the buyer
if ($order->buyer_user_id !== $user->id) {
    abort(403, 'Unauthorized');
}

// Creator: Must have items in order
$hasItems = $order->items()
    ->where('creator_id', $user->creator->id)
    ->exists();

if (!$hasItems) {
    abort(403, 'Unauthorized');
}
```

**Response:**
```
Returns: view('frontend.orders.show')
With:
  - $order (eager-loaded with items)
    - buyer info
    - items with creators
    - timeline data
    - conversations link
```

---

## CSRF Protection

All state-changing endpoints are CSRF-protected:
- ✅ POST requests require CSRF token
- ✅ PUT requests require CSRF token
- ✅ All forms include `@csrf`

**Example:**
```blade
<form action="{{ route('dashboard.orders.update-status', $order) }}" method="POST">
    @csrf
    @method('PUT')
    <select name="status">...</select>
    <button type="submit">Update Status</button>
</form>
```

---

## Error Handling

### Validation Errors
```
422 Unprocessable Content
{
  "errors": {
    "status": ["The status field must be one of: pending, accepted, ...]"
  }
}
```

### Authorization Errors
```
403 Forbidden - Customer cannot access other's order
```

### Not Found
```
404 Not Found - Order/Item doesn't exist
```

---

## Rate Limiting

No explicit rate limiting applied. Consider adding:
```php
// Suggested: Limit status updates per user per minute
Route::put('/dashboard/orders/{order}/status', ...)
    ->middleware('throttle:30,1');  // 30 updates per minute
```

---

## Testing with cURL

### Update Order Status
```bash
curl -X PUT "http://rockies.local/dashboard/orders/27/status" \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: $(csrf-token)" \
  -d '{"status": "in_progress"}'
```

### List Orders with Filter
```bash
curl "http://rockies.local/dashboard/orders?q=ORD&status=pending" \
  -H "Cookie: XSRF-TOKEN=...; session=..."
```

### View Order Details
```bash
curl "http://rockies.local/dashboard/orders/27" \
  -H "Cookie: XSRF-TOKEN=...; session=..."
```

---

## Integration Notes

### Vue/Alpine.js Integration
```javascript
// Update order status
async function updateOrderStatus(orderId, status) {
  const response = await fetch(`/dashboard/orders/${orderId}/status`, {
    method: 'PUT',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify({ status })
  });
  
  if (response.ok) {
    window.location.reload();  // Or update UI
  }
}
```

### JavaScript Form Submission
```javascript
document.querySelectorAll('form[data-auto-submit]').forEach(form => {
  form.addEventListener('change', function() {
    this.submit();  // Auto-submit on select change
  });
});
```

---

## Performance Considerations

### Query Optimization
All endpoints use eager loading to prevent N+1 queries:
- Order list: 1 query + relationships
- Order show: 1 query with all relationships pre-loaded
- Frontend order show: 2 queries (order + items with relationships)

### Caching Opportunities
- Order statuses change infrequently → Can cache for 5-10 minutes
- Order lists can be cached per user (10 minutes)
- Order details should be fresh (no cache)

### Pagination
- Default: 15 orders per page
- Configurable in controller: `$orders->paginate(15)`

---

## Status Codes Summary

| Code | Scenario |
|------|----------|
| 200 | GET request successful |
| 302 | POST/PUT successful (redirect with flash message) |
| 403 | Not authorized (wrong user, wrong role) |
| 404 | Resource not found (order doesn't exist) |
| 422 | Validation failed (invalid status value) |

---

## Related Documentation

- **ORDER_PAGES_COMPLETE.md** - Feature documentation for order pages
- **ORDER_PAGES_COMPARISON.md** - Public vs admin page differences
- **ORDER_JOURNEY_FIX.md** - Authorization and route fixes
- **ORDER_PAGES_IMPLEMENTATION.md** - Technical specifications

---

## Changelog

### Version 1.0 (Current)
✅ All endpoints functional
✅ Authorization working
✅ Payment tracking
✅ Status management
✅ Frontend and admin flows complete

### Potential Enhancements
- [ ] Rate limiting
- [ ] Order history/audit log
- [ ] Bulk status updates
- [ ] Email notifications on status change
- [ ] Webhook support for integrations
- [ ] API rate limiting per user
- [ ] GraphQL endpoint (alternative to REST)
