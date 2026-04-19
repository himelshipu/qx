# Campaign Order System Reference (Complete Implementation Guide)

> **Last Updated:** April 19, 2026
> **Status:** Complete Reference for Multi-Perspective Campaign Order Workflow
> **Pattern Basis:** Brand System Reference + Packages System Reference

---

## Overview

This document defines the complete Campaign Order system implementation pattern used across three perspectives:

- **Admin Dashboard**: Campaign order management, approval, completion, payout tracking
- **Brand Dashboard**: Order placement, price negotiation, work progress monitoring, approval & review
- **Influencer Dashboard**: Order acceptance, price negotiation, deliverable submission, completion

Campaign orders differ from package orders:

- **Campaigns** are brand-owned; **Packages** are influencer-owned (global/admin-created)
- **Both sides negotiate price** during application → approval → agreement flow
- **Work progress tracking** via SubOrder + OrderDeliverable entities
- **Review system** supports brand-to-influencer and influencer-to-brand reviews

---

## 1) File and Folder Structure

### 1.1 Backend Core Structure (Campaign Orders)

```text
app/
  Http/
    Controllers/
      Backend/
        CampaignOrderController.php          # Admin view: index, show, bulk operations
      Frontend/
        CampaignOrderController.php          # Brand + Influencer unified view
    Requests/
      Backend/
        CampaignOrder/
          UpdateCampaignOrderRequest.php     # Status updates, payout marking
      Frontend/
        CampaignOrder/
          AcceptCampaignOrderRequest.php     # Influencer accept price/counter
          SubmitDeliverableRequest.php       # Deliverable submission
          ApproveDeliverableRequest.php      # Brand approval of work

  Models/
    SubOrder.php                             # Campaign Order entity (extends from Order)
    OrderDeliverable.php                     # Work progress items
    Review.php                               # Brand-to-Influencer & Influencer-to-Brand reviews

  Repositories/
    Contracts/
      CampaignOrderRepositoryInterface.php
    Eloquent/
      EloquentCampaignOrderRepository.php

  Services/
    Admin/
      CampaignOrderService.php               # Business rules for admin operations
    Frontend/
      CampaignOrderService.php               # Neutral service for brand/influencer

  Policies/
    CampaignOrderPolicy.php                  # Authorization for order access
```

### 1.2 Frontend Admin Structure (Campaign Orders)

```text
resources/
  views/
    backend/
      pages/
        orders/
          _results.blade.php                 # Table rows partial (orders list)
          index.blade.php                    # Order listing with filters
          show.blade.php                     # Order detail & work progress view

    frontend/
      orders/
        campaign/                            # Campaign-specific order views
          brand-index.blade.php              # Brand sees ALL orders for their brand
          brand-show.blade.php               # Brand order detail (compact)
          influencer-index.blade.php         # Influencer sees orders assigned to them
          influencer-show.blade.php          # Influencer order detail (expanded)

        partials/                            # Reusable components
          _campaign-order-header.blade.php   # Order #, status, timeline
          _work-progress-section.blade.php   # Deliverables list + submission
          _price-negotiation.blade.php       # Price offer → accept/counter flow
          _approval-section.blade.php        # Brand approval UI
          _review-section.blade.php          # Review submission/display

  js/
    admin/
      campaign-orders-dashboard.js           # Filter, search, status toggle, bulk ops
    frontend/
      campaign-order-detail.js               # Deliverable upload, review form
```

### 1.3 Route Pattern

#### Admin Dashboard Routes

```text
dashboard.orders.index                 GET     /admin/orders
dashboard.orders.table                POST    /admin/orders/table
dashboard.orders.show                 GET     /admin/orders/{order}
dashboard.orders.update-status        PATCH   /admin/orders/{order}/status
dashboard.orders.mark-payout          POST    /admin/orders/{order}/mark-payout
dashboard.orders.bulk-mark-complete   POST    /admin/orders/bulk/mark-complete
dashboard.orders.bulk-mark-payout     POST    /admin/orders/bulk/mark-payout
```

#### Frontend Order Routes (Unified Brand + Influencer)

```text
orders.index                           GET     /orders
orders.show                            GET     /orders/{order}
orders.accept                          POST    /orders/{order}/accept
orders.accept-price                    POST    /orders/{order}/accept-price
orders.counter-offer                   POST    /orders/{order}/counter-offer
orders.submit-deliverable              POST    /orders/{order}/submit-deliverable
orders.approve-deliverable             PATCH   /orders/{order}/deliverables/{deliverable}/approve
orders.request-changes-deliverable     PATCH   /orders/{order}/deliverables/{deliverable}/request-changes
orders.reject-deliverable              PATCH   /orders/{order}/deliverables/{deliverable}/reject
orders.submit-review                   POST    /orders/{order}/reviews
orders.mark-complete                   PATCH   /orders/{order}/mark-complete
```

---

## 2) Data Schema Reference

### 2.1 Orders Table (Master Order Entity)

```sql
orders
├── id (PK)
├── order_number (UNIQUE) → "ORD-2026-0001"
├── buyer_user_id (FK → users) → Brand user
├── brand_id (FK → brands)
├── campaign_id (FK → campaigns) → NULL for package orders, set for campaign orders
├── status: enum[pending, accepted, in_progress, delivered, completed, cancelled, refunded]
├── accepted_by_user_id (FK → users) → Who approved the order
├── accepted_for_influencer_id (FK → influencers) → NULL for master, set for child orders
├── parent_order_id (FK → orders) → NULL for master orders
├── subtotal, service_fee, tax_amount, total_amount (DECIMAL)
├── currency (CHAR 3) → "USD"
├── placed_at, accepted_at, completed_at, cancelled_at (TIMESTAMPS)
└── timestamps
```

**For Campaign Orders Specifically:**

- `campaign_id` is set (not NULL)
- `parent_order_id` is NULL (master) or set to parent order ID (child)
- Child orders have `accepted_for_influencer_id` pointing to specific influencer

### 2.2 SubOrders Table (Campaign Order Item Per Influencer)

```sql
sub_orders
├── id (PK)
├── order_id (FK → orders)
├── campaign_influencer_id (FK → campaign_influencers)
├── influencer_id (FK → influencers) → Denormalized for query speed
├── status: enum[pending, accepted, in_progress, on_review, completed, cancelled]
├── deliverables (LONGTEXT JSON) → Array of deliverable specs
├── amount (DECIMAL) → Agreed price for THIS influencer's work
├── currency (CHAR 3) → "USD"
├── accepted_at, completed_at, cancelled_at (TIMESTAMPS) → Phase markers
├── paid_at (TIMESTAMP) → Payout completion
└── timestamps
```

### 2.3 OrderDeliverables Table (Work Progress Items)

```sql
order_deliverables
├── id (PK)
├── order_item_id (FK → order_items)
├── uploaded_by_user_id (FK → users) → Who submitted (influencer)
├── deliverable_type: enum[image, video, document, link, other]
├── file_path (STRING) → S3/local path
├── external_url (STRING) → For video uploads, etc.
├── notes (TEXT) → Submission notes
├── status: enum[submitted, approved, changes_requested, rejected]
└── timestamps
```

**For Campaign Orders:**

- One OrderDeliverable per campaign requirement
- Influencer submits files with notes
- Brand reviews (approve / request changes / reject)
- Status drives order progress to "on_review" → "completed"

### 2.4 Reviews Table (Post-Completion Feedback)

```sql
reviews
├── id (PK)
├── order_item_id (FK → order_items) → Nullable, campaign orders reference differently
├── influencer_id (FK → influencers)
├── brand_id (FK → brands)
├── reviewer_type: enum[brand, influencer] → Who left the review
├── reviewee_type: enum[brand, influencer] → Who was reviewed
├── rating (TINYINT 1-5)
├── title (STRING)
├── comment (TEXT)
└── timestamps
```

---

## 3) Complete User Journey (From All Three Perspectives)

### 3.1 Admin Perspective: Campaign Order Lifecycle

#### Step 1: View Campaign Orders (Admin Dashboard)

**Route:** `GET /admin/orders?type=campaign`

**Controller:** `Backend\CampaignOrderController::index()`

**Flow:**

1. Admin navigates to Orders → sees filter options (status, type, search)
2. Filters applied:
    - Status filter: pending, accepted, in_progress, delivered, on_review, completed, cancelled
    - Type filter: campaign (vs. package)
    - Search: order #, brand name, campaign title, influencer name
3. Table shows:
    - Order # | Brand | Campaign | Influencer | Status | Amount | Placed | Actions
4. AJAX `POST /admin/orders/table` refreshes table without full page reload

**Service Calls:**

- `CampaignOrderService::buildAdminIndex()` → Returns paginated orders with stats
- `CampaignOrderService::getStatusOptions()` → Available status enums
- `CampaignOrderService::getTypeOptions()` → [campaign, package]

#### Step 2: Review Order Details

**Route:** `GET /admin/orders/{order}`

**Controller:** `Backend\CampaignOrderController::show()`

**View:**

- Order header: #, Brand, Campaign, placed_at, current_status
- Master order breakdown:
    - Subtotal, Service Fee (%), Tax (%), Total
    - Payment status: Pending/Paid
- Child orders table:
    - Per-influencer breakdown
    - Influencer | Amount | Status | Deliverables | Payout Status

**If ORDER STATUS = pending:**

- Show: "⏳ Waiting for Brand to Accept"
- Button: "☑ Accept Order" → Sets status to `accepted`, records `accepted_at`

**If ORDER STATUS = accepted:**

- Show: "⏳ Influencers confirming deliverables"
- Per sub-order: Status of each influencer's acceptance
- Disable further actions until all influencers accept

**If ORDER STATUS = in_progress:**

- Show: "⏳ Work In Progress"
- Work Progress Tab:
    - List all OrderDeliverables grouped by influencer
    - Each deliverable shows: Type, submitted_at, status
    - Brand submission review status

**If ORDER STATUS = on_review:**

- Show: "✅ Work submitted, under review"
- Review Tab: Approval workflow

**If ORDER STATUS = completed:**

- Show: "✅ Order Complete"
- Final amounts paid to each influencer
- Payout status: Pending / Completed

#### Step 3: Mark Order Complete (Admin)

**Route:** `PATCH /admin/orders/{order}/status`

**Controller:** `Backend\CampaignOrderController::updateStatus()`

**Only if:**

- All sub-orders have `status = completed`
- ALL deliverables approved

**Action:**

- Update master `orders.status = completed`
- Update master `orders.completed_at = now()`

#### Step 4: Mark Payout Complete

**Route:** `POST /admin/orders/{order}/mark-payout`

**Controller:** `Backend\CampaignOrderController::markPayout()`

**Form:**

- Per-influencer payout markings:
    - Reference # (bank transfer ID, etc.)
    - Amount paid (pre-filled from agreed_amount)
    - Date paid
    - Notes

**Action:**

- For each sub_order, update: `paid_at`, `payout_reference`, `payout_note`
- Update master order `status = paid` (if applicable)
- Send notification to influencer: "Payment received: $XXX"

---

### 3.2 Brand Perspective: Order Placement Through Completion

#### Step 1: Browse & Select Campaign

**Route:** `GET /campaigns` → Frontend campaign listing

**Flow:**

- Brand searches/filters campaigns
- Brand clicks campaign → `GET /campaigns/{campaign}/view`
- Brand sees campaign details + influencer applications

#### Step 2: View Approved Influencers & Propose Price

**Route:** `GET /campaigns/{campaign}/influencers`

**View:** `backend/pages/campaigns/influencers/index.blade.php`

**For each approved influencer:**

- Influencer name | Status (approved/rejected/pending price) | Proposed Rate (input) | Action
- Brand enters price → Clicks "Approve & Propose Rate"
- Sends conversation message: "We'd like to work with you on {campaign} at ${amount}. Can you confirm?"

**Status becomes:** `price_proposed`

#### Step 3: Brand Creates Order

**Route:** `POST /campaigns/{campaign}/create-order`

**Controller:** `Backend\CampaignController::createOrder()` or `Backend\CampaignOrderController::storeFromCampaign()`

**Only if:**

- All approved influencers have `agreed_amount` set (both sides agreed on price)

**Action:**

1. Create master Order:
    - `order_number = "ORD-" . date . random`
    - `buyer_user_id = brand_user_id`
    - `brand_id = brand_id`
    - `campaign_id = campaign_id`
    - `status = pending` (awaiting influencer acceptance)
    - Calculate `subtotal = SUM(agreed_amounts)`, apply service_fee, tax
    - `placed_at = now()`

2. For each approved influencer with agreed_amount:
    - Create SubOrder:
        - `order_id = master order id`
        - `campaign_influencer_id = assignment id`
        - `influencer_id = influencer id`
        - `status = pending`
        - `amount = agreed_amount`
        - `deliverables = campaign deliverable specs (JSON)`

3. For each OrderItem in campaign:
    - Create OrderDeliverable entries (one per item)
    - `status = submitted` (pending upload)

4. Send notifications:
    - To influencers: "New order for {campaign}: ${amount}"
    - Register conversation thread for this order

#### Step 4: Brand Monitors Work Progress

**Route:** `GET /orders/{order}` → Brand view

**View:** `frontend/orders/campaign/brand-show.blade.php`

**Sections:**

**A. Order Header:**

- Status badge: pending / accepted / in_progress / on_review / completed
- Timeline: Placed → Accepted → In Progress → Delivered → Completed
- Amounts: Subtotal, Fee, Tax, Total

**B. Per-Influencer Breakdown:**

- Influencer | Assigned Amount | Status | Actions

**C. Work Progress Tab:**

- Shows all OrderDeliverables grouped by influencer
- For each deliverable:
    - Type (image/video/document)
    - Submission date
    - Notes from influencer
    - Status badge: submitted / approved / changes_requested / rejected
    - Actions:
        - ✅ Approve
        - ⚠️ Request Changes (with comment form)
        - ❌ Reject (with reason)

**D. Approval Workflow:**

- If any deliverable is `changes_requested`:
    - Show: "⚠️ Changes Requested" badge
    - Send notification to influencer: "Brand requested changes on deliverable X"
    - Influencer can resubmit
    - Brand can approve resubmission

- Once all deliverables `status = approved`:
    - Auto-update SubOrder `status = completed`
    - Auto-update Order `status = completed`
    - Enable Review button

#### Step 5: Brand Submits Review

**Route:** `POST /orders/{order}/reviews`

**Form:**

- Review stars (1-5)
- Review title
- Review comment

**Action:**

- Create Review entry:
    - `reviewer_type = 'brand'`
    - `reviewee_type = 'influencer'`
    - Rating, title, comment
- Send notification to influencer: "Brand left a review"
- Influencer can leave counter-review

---

### 3.3 Influencer Perspective: Order Acceptance Through Delivery

#### Step 1: Receive Order Notification

**Notification:** "New campaign order from {brand}: {campaign title} - ${amount}"

**Action:** Influencer navigates to `GET /orders`

#### Step 2: View & Accept Order

**Route:** `GET /orders/{order}` → Influencer view

**View:** `frontend/orders/campaign/influencer-show.blade.php`

**Sections:**

**A. Order Summary:**

- Campaign name | Brand | Campaign description | Duration
- Proposed amount: $500 (from brand's proposal)
- Status: pending (awaiting your decision)

**B. Price Negotiation Section:**

- Brand proposed rate: ${proposed_amount}
- Two buttons:
    - ✅ "Accept Rate"
    - 💬 "Counter Offer"

**If Counter Offer clicked:**

- Show form: "Enter your rate: $ [input]"
- Submit button sends message to brand: "Can you offer $XXX instead?"
- Status becomes: `price_counter`
- Brand can accept the counter or propose new rate

- If brand accepts:
    - Status: `price_agreed`
    - Influencer sees: "✅ Price agreed: $XXX"

**If Accept Rate clicked:**

- Update CampaignInfluencer `agreed_amount = proposed_amount`
- Status: `price_agreed`
- Show message: "✅ You've accepted. Waiting for brand to create order..."

#### Step 3: Order Placed - Influencer Sees Details

**Once brand creates order, influencer sees:**

**Status:** ✅ Order Confirmed: $500

**Order Details:**

- Order #
- Campaign requirements (JSON from SubOrder.deliverables)
- Deadline
- How to submit (file upload, link, etc.)

**Work Progress Section:**

- Checklist of deliverables:
    - [ ] Deliver {requirement 1}
    - [ ] Deliver {requirement 2}
    - etc.

#### Step 4: Submit Deliverables

**Route:** `POST /orders/{order}/submit-deliverable`

**Form:**

- Deliverable type selector: Image / Video / Document / Link / Other
- File upload or URL input
- Notes: "Please see attached. High-res, 1080p..."
- Submit button

**Action:**

1. Create OrderDeliverable:
    - `deliverable_type = selected type`
    - `file_path = uploaded file` OR `external_url = link`
    - `notes = influencer notes`
    - `status = submitted`
    - `uploaded_by_user_id = influencer user id`

2. Update SubOrder:
    - `status = in_progress` (once first deliverable submitted)

3. Send notification to brand:
    - "Influencer submitted deliverable for {campaign}: {requirement}"
    - Brand can now review

#### Step 5: Respond to Changes Requested

**If brand leaves feedback:** Request Changes

**Influencer receives notification:**

- "Brand requested changes on deliverable: '{feedback}'"

**Influencer navigates back to `GET /orders/{order}`:**

- Sees red badge: "⚠️ Changes Requested"
- Can re-upload same deliverable type
- New submission replaces old one
- Brand reviews again

**Repeat until approved.**

#### Step 6: Work Complete - Awaiting Review

**Once all deliverables approved:**

**Influencer sees:**

- Status: ✅ Work Completed
- Message: "Great! Your work has been approved. {Brand} may leave a review."

**If brand leaves review:**

- Influencer receives notification
- Can view review in `GET /orders/{order}` → "Reviews" tab
- Can leave counter-review: "Rate Brand" form

---

## 4) Required Pattern: Mandatory Layering

### 4.1 Controller → Service → Repository → Model (Scopes)

**Rule: Never bypass this flow**

Example: Fetch campaign orders for admin dashboard

```
1. Controller (Backend/CampaignOrderController)
   ├─ Parse request: filters (status, type, search), page
   ├─ Call Service:  $campaignOrderService->buildAdminIndex($filters)
   └─ Pass result to view

2. Service (Services/Admin/CampaignOrderService)
   ├─ Validate filter inputs
   ├─ Call Repository: $campaignOrderRepository->getDashboardIndex($filters)
   ├─ Assemble payload: {orders, stats, options}
   └─ Return payload to controller

3. Repository (Repositories/Eloquent/EloquentCampaignOrderRepository)
   ├─ Build query using Model scopes
   ├─ Apply filters via scopes: forDashboard(), dashboardSearch(), dashboardStatus()
   ├─ Apply ordering and pagination
   ├─ Eager-load relationships
   └─ Return paginated results to service

4. Model (Models/SubOrder or Order)
   ├─ Define scopes:
   │  ├─ scopeForDashboard() → Select needed columns, eager-load
   │  ├─ scopeDashboardSearch() → WHERE order_number LIKE or campaign LIKE
   │  ├─ scopeDashboardStatus() → WHERE status = $status
   │  └─ scopeDashboardType() → WHERE campaign_id IS NOT NULL (for campaign type)
   └─ Define relationships: order(), influencer(), campaign(), etc.
```

### 4.2 Blade and JS Pattern

**Rule: Keep presentation logic separate**

1. **Page shell and filters in main view** (`index.blade.php`)
    - Header with title, description
    - Filter bar (search, select filters, reset button)
    - Stats grid/boxes
    - Placeholder for table

2. **Table rows in partial** (`_results.blade.php`)
    - Included in main view
    - Also returned by AJAX endpoint
    - No filter bar or page wrapper
    - Just `<tbody>` with row markup

3. **Dynamic behavior in JS** (`campaign-orders-dashboard.js`)
    - Intercept form #submit to AJAX POST
    - AJAX GET `/admin/orders/table?filters=...`
    - Replace tbody with returned HTML
    - Update `.stats-grid` with new counts
    - Show toast feedback

### 4.3 Service Pattern: What Services Do

**Services own:**

1. **Payload Assembly**
    - Fetch data from repository
    - Build options (status enums, type options, etc.)
    - Package into shape needed by view

2. **Business Rules**
    - Can order be updated? Check statuses, permissions
    - Calculate totals, fees, taxes
    - Validate price negotiation state

3. **Side Effects & Transactions**
    - Create orders with multiple related entities (Order + SubOrders + OrderDeliverables)
    - Update statuses across multiple tables
    - Send notifications/messages

**Services do NOT:**

- Direct database queries (use Repository)
- Raw model manipulation (use Repository)
- View rendering (return data, let controller pass to view)

---

## 5) Permissions and Authorization

### 5.1 CampaignOrderPolicy

**Routes protected by:** `Authorize::with(CampaignOrderPolicy::class)`

```php
// Admin dashboard access
if (!$user->can('view', $order)) abort(403);

// Brand access to own orders
- Brand can view orders where brand_id = their brand_id
- Brand can update order prices/statuses for OWN orders
- Brand cannot view other brand's orders

// Influencer access to assigned orders
- Influencer can view orders where they have sub-orders
- Influencer can submit/update deliverables for their sub-orders
- Influencer cannot view sub-order amounts for OTHER influencers in same order
```

### 5.2 Permission Slugs for Role/Seeding

```
orders.index                   # View order listing
orders.view                    # View order detail
orders.create                  # Create orders (admin only)
orders.update                  # Update order status (admin)
orders.mark-payout             # Mark payouts (admin)
orders.bulk-mark-complete      # Bulk operations (admin)
```

---

## 6) Key Database Relationships (ERD Summary)

```
Order (Master)
├── many-to-one: Brand
├── many-to-one: Campaign
├── many-to-one: User (buyer)
├── one-to-many: SubOrder (child orders per influencer)
├── one-to-many: OrderItem (only for package orders)
├── one-to-many: OrderDeliverable (via OrderItem)
└── one-to-many: Conversation

SubOrder (Campaign Order Item)
├── many-to-one: Order (parent)
├── many-to-one: CampaignInfluencer
├── many-to-one: Influencer
└── many-to-one: OrderDeliverable (reverse relation on order_deliverable)

OrderDeliverable (Work Progress Item)
├── many-to-one: OrderItem
├── many-to-one: User (uploaded_by)
└── many-to-many: Review (can be reviewed)

CampaignInfluencer (Application)
├── many-to-one: Campaign
├── many-to-one: Influencer
└── one-to-many: SubOrder
```

---

## 7) Naming Standards and Conventions

### 7.1 Controllers

```
Backend\CampaignOrderController           # Admin dashboard
Frontend\CampaignOrderController          # Brand + Influencer unified
(not separate Brand/Influencer controllers)
```

### 7.2 Services

```
Services\Admin\CampaignOrderService       # Admin business rules
Services\Frontend\CampaignOrderService    # Neutral service (brand-safe, influencer-safe)
```

### 7.3 Repository

```
Repositories\Contracts\CampaignOrderRepositoryInterface
Repositories\Eloquent\EloquentCampaignOrderRepository
```

### 7.4 Request Validation Classes

```
Http\Requests\Backend\CampaignOrder\UpdateCampaignOrderRequest
Http\Requests\Frontend\CampaignOrder\AcceptCampaignOrderRequest
Http\Requests\Frontend\CampaignOrder\SubmitDeliverableRequest
Http\Requests\Frontend\CampaignOrder\RequestChangesRequest
```

### 7.5 Blade Views

```
backend/pages/orders/                    # Generic orders (both campaign & package)
frontend/orders/campaign/                # Campaign-specific frontend
frontend/orders/campaign/brand-index.blade.php
frontend/orders/campaign/brand-show.blade.php
frontend/orders/campaign/influencer-index.blade.php
frontend/orders/campaign/influencer-show.blade.php
frontend/orders/partials/_price-negotiation.blade.php
frontend/orders/partials/_work-progress.blade.php
frontend/orders/partials/_approval-section.blade.php
```

### 7.6 JavaScript Modules

```
resources/js/admin/campaign-orders-dashboard.js
resources/js/frontend/campaign-order-detail.js
```

---

## 8) Key Implementation Checklist

### 8.1 Models (Extend/Create)

- [ ] Extend `SubOrder` model with relations to WorkProgress entities
- [ ] Ensure `Order` model has proper scope methods
- [ ] `OrderDeliverable` model has status transitions
- [ ] `CampaignInfluencer` supports price agreement tracking

### 8.2 Controllers

- [ ] `Backend/CampaignOrderController` → index, show, update-status, mark-payout
- [ ] `Frontend/CampaignOrderController` → index, show, accept, submit-deliverable, approve-deliverable, submit-review
- [ ] Authorization via `CampaignOrderPolicy`

### 8.3 Services

- [ ] `CampaignOrderService` (admin) → buildAdminIndex, getOptions, etc.
- [ ] `CampaignOrderService` (frontend) → getInfluencerView, getBrandView, submitDeliverable, approveDeliverable

### 8.4 Repositories

- [ ] `EloquentCampaignOrderRepository` → getDashboardIndex, getBrandOrders, getInfluencerOrders
- [ ] Model scopes: forDashboard, dashboardSearch, dashboardStatus, dashboardType

### 8.5 Views

- [ ] Admin: `backend/pages/orders/index`, show
- [ ] Brand: `frontend/orders/campaign/brand-index`, brand-show
- [ ] Influencer: `frontend/orders/campaign/influencer-index`, influencer-show
- [ ] Partials: price-negotiation, work-progress, approval, review

### 8.6 JS/AJAX

- [ ] `campaign-orders-dashboard.js` → Filter, search, pagination AJAX
- [ ] `campaign-order-detail.js` → Deliverable upload, review form

### 8.7 Routes

- [ ] All routes in `routes/web.php` (admin group + frontend group)
- [ ] Route model binding for Order/SubOrder

### 8.8 Authorization

- [ ] `CampaignOrderPolicy` → view, update, delete per user type
- [ ] Middleware: `EnforceDashboardRoutePermission` for admin routes

---

## 9) Status Enums Reference

### 9.1 Order Status (Master Order)

```
pending       → Placed, awaiting influencer acceptance
accepted      → All influencers accepted, awaiting work
in_progress   → Work in progress by influencers
delivered     → Work delivered, awaiting brand review
on_review     → Brand reviewing deliverables
completed     → All work approved, awaiting payout
cancelled     → Order cancelled
refunded      → Refunded to buyer
```

### 9.2 SubOrder Status (Per-Influencer Order)

```
pending       → Awaiting influencer acceptance
accepted      → Influencer accepted, awaiting work
in_progress   → Work in progress
on_review     → Brand reviewing deliverables
completed     → All deliverables approved
cancelled     → Order cancelled
```

### 9.3 OrderDeliverable Status (Work Item)

```
submitted         → Submitted, awaiting brand review
approved          → Approved by brand
changes_requested → Brand requested changes
rejected          → Rejected, cannot proceed
```

---

## 10) Notifications and Messaging Reference

### 10.1 System Messages (Conversation Integration)

**When:** Brand proposes price
**To:** Influencer
**Message:** "Brand {brand_name} proposed ${amount} for {campaign_title}"

**When:** Influencer accepts price
**To:** Brand
**Message:** "Influencer {name} accepted your price proposal"

**When:** Influencer counters price
**To:** Brand
**Message:** "Influencer {name} countered with ${amount}"

**When:** Order created
**To:** Influencer
**Message:** "New order confirmed: {campaign_title} - ${amount}. Deliverables due: {deadline}"

**When:** Influencer submits deliverable
**To:** Brand
**Message:** "{influencer_name} submitted deliverable: {type}"

**When:** Brand requests changes
**To:** Influencer
**Message:** "Brand requested changes: '{feedback}'"

**When:** All deliverables approved
**To:** Influencer
**Message:** "✅ All deliverables approved! Looking forward to your review from the brand."

**When:** Brand leaves review
**To:** Influencer
**Message:** "Brand {brand_name} left a review: {rating}⭐ - {title}"

**When:** Payout marked complete
**To:** Influencer
**Message:** "✅ Payment processed: ${amount}"

---

## 11) Form Validation Rules

### 11.1 Accept Order (Influencer)

```php
'accept' => 'required|boolean'  // Simple confirmation
```

### 11.2 Accept Price (Influencer)

```php
'price_acceptance' => 'required|in:accept,counter,reject'
```

### 11.3 Counter Offer (Influencer)

```php
'counter_amount' => 'required|numeric|min:1|max:999999'
'reason' => 'nullable|string|max:500'
```

### 11.4 Submit Deliverable (Influencer)

```php
'deliverable_type' => 'required|in:image,video,document,link,other'
'file' => 'required_if:deliverable_type,image,video,document|file|max:100000'
'external_url' => 'required_if:deliverable_type,link|url'
'notes' => 'nullable|string|max:1000'
```

### 11.5 Request Changes (Brand)

```php
'feedback' => 'required|string|min:10|max:1000'
'deliverable_id' => 'required|exists:order_deliverables,id'
```

### 11.6 Mark Payout (Admin)

```php
'influencer_id' => 'required|exists:influencers,id'
'amount' => 'required|numeric|min:0.01'
'reference' => 'required|string|max:100'
'paid_date' => 'required|date|before_or_equal:today'
'notes' => 'nullable|string|max:500'
```

---

## 12) Example Workflow Sequence (Happy Path)

```
Day 1:
├─ Brand creates campaign & publishes
├─ Admin assigns 3 influencers
└─ Influencers receive invitations

Day 2:
├─ Influencer A: Views invitation, clicks "Interested"
├─ Influencer B: Views invitation, clicks "Interested"
├─ Influencer C: Declines
└─ Brand sees: 2 interested, 1 declined

Day 3:
├─ Brand proposes: Influencer A → $500, Influencer B → $450
├─ Both influencers receive proposals
├─ A: Accepts $500 ✅
└─ B: Counters with $550

Day 4:
├─ Brand responds to B: Accepts $550
├─ B: Accepts $550 ✅
├─ Brand creates Order (parent + 2 child sub-orders)
└─ Both influencers notified: "Order confirmed!"

Days 5-10:
├─ A & B working on deliverables
├─ A submits: 3 images on day 7
├─ B submits: 2 videos on day 9
├─ Brand reviews A's work → Approves all 3 ✅
└─ Brand reviews B's work → Requests changes on 1 video

Day 11:
├─ B resubmits revised video
├─ Brand approves revised video ✅
├─ All deliverables now approved
└─ Order auto-marks `status = completed`

Day 12:
├─ A & B can leave reviews (optional)
├─ Admin marks payouts:
│  ├─ Influencer A: $500 paid (ref: TRF-2026-001)
│  ├─ Influencer B: $550 paid (ref: TRF-2026-002)
│  └─ Both notified: "Payment received"
└─ Order complete ✅
```

---

## 13) Testing Strategy

### 13.1 Feature Tests to Write

```php
// Admin perspective
CampaignOrderIndexTest           // Filtering, search, pagination
CampaignOrderShowTest            // View order detail
CampaignOrderStatusUpdateTest    // Status transitions
CampaignOrderPayoutTest          // Payout marking

// Brand perspective
BrandOrderIndexTest              // Brand sees only own orders
BrandOrderShowTest               // Brand can see details
BrandAcceptOrderTest             // Brand accepts order
BrandApproveDeliverableTest      // Brand approves work
BrandSubmitReviewTest            // Brand leaves review

// Influencer perspective
InfluencerOrderIndexTest         // Influencer sees assigned orders
InfluencerOrderShowTest          // Influencer sees details
InfluencerAcceptPriceTest        // Influencer negotiates price
InfluencerSubmitDeliverableTest  // Influencer uploads work
InfluencerRequestChangesTest     // Influencer resubmits after feedback
```

### 13.2 Unit Tests

```php
CampaignOrderRepositoryTest      // Query tests
CampaignOrderServiceTest         // Business rules
CampaignOrderPolicyTest          // Authorization
```

---

## 14) Troubleshooting Common Issues

### Issue: "Influencer cannot see order amount in sub-order"

**Solution:** Check SubOrder relationship in views. Only brand should see SubOrder.amount directly. Influencer sees it via `order.total_amount` (split per sub-order). Prevent n+1 via eager-loading.

### Issue: "Status doesn't update when deliverable approved"

**Solution:**

- When last OrderDeliverable is approved, SubOrder must auto-update `status = completed`
- When all SubOrders are completed, Order must auto-update `status = completed`
- Use listeners on OrderDeliverable status change, or service method

### Issue: "Influencer can see other influencer's counter-offer"

**Solution:** Hide price negotiation details from influencers. Only show their own proposed/counter amounts. Use `->where('influencer_id', $user->influencer->id)` in queries.

---

## 15) Future Enhancements

1. **Partial Approvals:** Allow brand to approve some deliverables while requesting changes on others
2. **Revision History:** Track all deliverable revisions with timestamps
3. **Batch Payouts:** Admin can mark multiple orders for payout in one action
4. **Analytics:** Brand dashboard widget showing order success rates, delivery times
5. **Escrow/Holdback:** Brand can hold % of payment until after review period
6. **Dispute Resolution:** Formal dispute/escalation workflow if price negotiation breaks down

---

**End of Reference Document**

Version: 1.0 | Last Updated: April 19, 2026
