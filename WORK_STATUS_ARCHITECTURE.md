# Work Status & Order Architecture

## Overview

The system handles two types of orders: **Package Orders** and **Campaign Orders**. Each type has its own status tracking mechanism.

---

## 1. PACKAGE ORDERS

### Data Model   

```
Parent Order (parent_order_id = null, campaign_id = null)
├── OrderItem 1 (influencer_id, package_id, status)
├── OrderItem 2 (influencer_id, package_id, status)
└── Child Orders (one per influencer, parent_order_id = parent_id)
    └── OrderItems (same items, references to parent)

Status Location: order_items.status
Status Values: pending, accepted, in_progress, delivered, approved, rejected, completed, cancelled
```

### Status Update Path

- **Admin Dashboard**: `/dashboard/orders/{order}`
- **Button**: "Save Item Stage" for each item
- **Service**: `Backend/OrderController@updateOrderItemStatus()`
- **Database**: Updates `order_items.status`

### Who Sees What

- **Admin**: Full order with all items, can update each item status
- **Brand**: Sees order summary, can approve/reject items when status = 'delivered'
- **Influencer**: Sees own items, can update item status (select from dropdown)

---

## 2. CAMPAIGN ORDERS

### Data Model

```
Master Order (parent_order_id = null, campaign_id = id)
├── SubOrder 1 (influencer_id, campaign_influencer_id, status)
├── SubOrder 2 (influencer_id, campaign_influencer_id, status)
└── No OrderItems (this is the key difference!)

SubOrder includes:
- order_id (links to master order)
- influencer_id
- campaign_influencer_id
- status (SINGLE status for entire influencer's work)
- amount, currency, payout fields
- deliverables (JSON or relation)

Status Location: sub_orders.status
Status Values: SAME as PackageOrder (pending, accepted, in_progress, on_review, completed, cancelled)
```

### Status Update Path

- **Admin Dashboard**: `/dashboard/orders/{order}`
- **Section**: "Influencers (Campaign Order)" cards
- **Button**: "Save Work Status" dropdown
- **Service**: `Backend/OrderController@updateSubOrderStatus()`
- **Database**: Updates `sub_orders.status`

### Who Sees What

#### BRAND View (`/campaigns/{campaign}`)

- **Section**: "Influencer Work Progress"
    - Shows progress bar for each influencer
    - Status: Built from `SubOrder.status`
    - Source: Fresh query via `Frontend/CampaignController`

- **Section**: "Deliverables Checklist" (new)
    - Shows deliverables for each influencer
    - Status: From OrderDeliverable records linked via sub_order_id
    - Can approve/reject deliverables

#### INFLUENCER Account (`/campaigns/{campaign}`)

- **Section**: "Influencer Work Progress"
    - Shows their progress as read-only
    - Status: From SubOrder (same as brand sees)
    - Updates when admin changes SubOrder status

#### ADMIN Dashboard (`/dashboard/orders/{order}`)

- **Section**: "Influencers (Campaign Order)"
    - Shows SubOrder card for each influencer
    - Can update status via dropdown
    - Can mark as paid
    - Can approve/reject deliverables

---

## 3. STATUS SYNCHRONIZATION

### The Fix

The CampaignController now loads SubOrders **fresh from database** on every page load:

```php
// OLD (cached data)
$campaign->orders()->with(['subOrders', ...])->get();

// NEW (fresh data)
Order::where('campaign_id', $campaign->id)
    ->with(['subOrders' => fn($q) => $q->select(...)])
    ->get();
```

This ensures:

1. Admin updates SubOrder status in dashboard
2. Influencer/Brand refreshes campaign page
3. Latest status is displayed immediately (not cached)

### Status Flow for Campaign Orders

```
Admin Updates        Brand Sees          Influencer Sees     Notes
─────────────────────────────────────────────────────────────────
pending           → "Order Pending"    → Same              Initial state
accepted          → "Accepted"         → Same              Influencer approved
in_progress       → "In Progress"      → Same              Work ongoing
on_review         → "On Review"        → Same              Deliverables submitted
completed         → "Completed"        → Same              Work finished & paid
```

---

## 4. DELIVERABLES TRACKING

### Database Structure

```
order_deliverables table
├── order_item_id (nullable) - for package orders
├── sub_order_id (nullable)  - for campaign orders
├── deliverable_type: image, video, document, link, other
├── file_path (nullable)
├── external_url (nullable)
├── status: submitted, approved, changes_requested, rejected
└── uploaded_by_user_id
```

### Influencer Submits Deliverable

- Influencer views: `/orders/{order}`
- Opens "Submit Deliverables" form
- Selects type, uploads file or enters URL
- Stored as OrderDeliverable with `sub_order_id`

### Admin Approves/Rejects

- Admin views: `/dashboard/orders/{order}`
- Sees deliverables in SubOrder card
- Buttons: "Approve", "Changes", "Reject"
- Updates OrderDeliverable.status

### Brand Reviews

- Brand views: `/campaigns/{campaign}`
- Section: "Deliverables Checklist"
- Shows all deliverables with status
- Can see count of approved vs total

---

## 5. CLEAR WORKFLOW EXAMPLE

**Scenario: Admin Updates Campaign Order Status**

1. **Admin does**:
    - Visits `/dashboard/orders/25`
    - Finds "Influencers" section
    - Clicks "Save Work Status" dropdown for influencer
    - Selects "In Progress"
    - Clicks "Save Work Status"

2. **Backend updates**:
    - SubOrder record status: pending → in_progress
    - Timestamp updated_at is refreshed

3. **Brand refreshes** `/campaigns/1`:
    - CampaignController queries fresh SubOrders
    - Finds SubOrder with influencer_id: status = in_progress
    - Progress block shows "60% - In Progress"

4. **Influencer refreshes** `/campaigns/1` (read-only):
    - Same CampaignController query
    - Same SubOrder status = in_progress
    - Sees same "60% - In Progress"

**Result**: All users see synchronized status ✅

---

## 6. FIELD REFERENCE

### SubOrder Status Values

| Status      | Admin Updates Via | Admin Label                    | Progress % | Brand/Influencer Label |
| ----------- | ----------------- | ------------------------------ | ---------- | ---------------------- |
| pending     | Dropdown          | Pending                        | 15%        | Order Pending          |
| accepted    | Dropdown          | Accepted - influencer approved | 35%        | Accepted               |
| in_progress | Dropdown          | In Progress                    | 60%        | In Progress            |
| on_review   | Dropdown          | On Review                      | 80%        | On Review              |
| completed   | Dropdown          | Completed - work finished      | 100%       | Completed              |
| cancelled   | Dropdown          | Cancelled                      | 0%         | Cancelled              |

### OrderDeliverable Status Values

| Status            | Who Sets              | Admin Action                       | Notes              |
| ----------------- | --------------------- | ---------------------------------- | ------------------ |
| submitted         | Influencer (via form) | Can approve/request changes/reject | Initial submission |
| approved          | Admin (button click)  | Brand can see it approved          | Work accepted      |
| changes_requested | Admin (button click)  | Influencer notified, can resubmit  | Needs revision     |
| rejected          | Admin (button click)  | Delivery not accepted              | Declined work      |

---

## 7. TROUBLESHOOTING

### "Status Not Updating in Campaign View"

- **Check**: Is influencer/brand refreshing the page?
- **Fix**: Clear browser cache: `Ctrl+Shift+Delete` or `Cmd+Shift+Delete`
- **Root Cause**: May have had cached view before fix was applied

### "Influencer Sees Different Status Than Brand"

- **Check**: Reload both pages fresh
- **Fix**: This should not happen with current code
- **Report**: If still occurring, check database directly:
    ```sql
    SELECT id, influencer_id, status, updated_at FROM sub_orders
    WHERE order_id = 25;
    ```

### "Admin Can't Update Status"

- **Check**: Is there a transition rule preventing it?
- **Check**: Is there a "Force Transition" checkbox that needs to be checked?
- **Database**: Verify sub_orders record exists and is not soft-deleted

---

## 8. FILES TO KNOW

| File                                                         | Purpose                                       |
| ------------------------------------------------------------ | --------------------------------------------- |
| `app/Http/Controllers/Backend/OrderController.php`           | Admin dashboard logic, status updates         |
| `app/Http/Controllers/Frontend/CampaignController.php`       | Campaign view, work progress building         |
| `app/Http/Controllers/Frontend/OrderController.php`          | Influencer order view, deliverable submission |
| `app/Models/SubOrder.php`                                    | Campaign order model, relationships           |
| `app/Models/OrderDeliverable.php`                            | Deliverable model, linked to SubOrder         |
| `resources/views/backend/pages/orders/show.blade.php`        | Admin dashboard view                          |
| `resources/views/frontend/campaigns/designed-show.blade.php` | Campaign view for brand/influencer            |
| `resources/views/frontend/orders/show.blade.php`             | Influencer order view                         |

---

## 9. KEY TAKEAWAY

**ONE status per influencer per campaign order (SubOrder.status)**

NOT:

- Multiple OrderItems with separate statuses (that's for packages)
- Mixed status from different tables

THIS is why admin updating OrderItem status was confusing - **Campaign orders don't use OrderItems at all!**
