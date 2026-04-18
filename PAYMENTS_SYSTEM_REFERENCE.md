# Payments System Reference (Commerce Admin Canonical Pattern)

This document defines the implementation blueprint for the admin Payments-related pages under Commerce.
Use it as the canonical reference for file/folder structure, naming, query optimization, UI consistency, and permission-safe dashboard behavior.

## 1) File and Folder Structure

### 1.1 Backend Core Structure

```text
app/
  Http/
    Controllers/
      Backend/
        PaymentsController.php
        PaymentQueueController.php
        PaymentAuditController.php
        PaymentStatementController.php
  Models/
    Payment.php
    OrderItem.php
    SubOrder.php
    Influencer.php
  Services/
    PaymentMethodService.php
```

### 1.2 Frontend Admin Structure

```text
resources/
  views/
    backend/
      pages/
        payments/
          index.blade.php
        payment-queue/
          index.blade.php
        payment-audit/
          index.blade.php
        payment-statement/
          index.blade.php
          show.blade.php
          pdf.blade.php
```

### 1.3 Route Pattern in Dashboard Group

Payments-related dashboard endpoints follow this naming:

1. dashboard.payments.index
2. dashboard.payments.show
3. dashboard.payments.refund
4. dashboard.payments.retry
5. dashboard.payment-queue.index
6. dashboard.payment-queue.bulk-mark
7. dashboard.payment-audit.index
8. dashboard.payment-audit.undo-item
9. dashboard.payment-audit.undo-suborder
10. dashboard.payment-statement.index
11. dashboard.payment-statement.show
12. dashboard.payment-statement.pdf

## 2) Coding Pattern Used by Payments Pages

### 2.1 Mandatory Query Practice

Always follow this flow:

Controller -> Model scopes -> Lean selects -> Aggregates -> View

Rules:

1. Controllers should not fetch full tables unless the view actually needs them.
2. Use dashboard-specific scopes for reusable selects and state filters.
3. Prefer aggregate queries for counts and sums instead of loading collections to count them.
4. Eager load only the columns needed by the rendered page.
5. Avoid repeated count/sum queries when one grouped aggregate can serve the same data.

### 2.2 Blade and UI Pattern

Rules:

1. Preserve the existing Commerce visual language.
2. Keep dashboard KPI cards and summary blocks consistent with other commerce pages.
3. Avoid introducing unnecessary filter controls where the page is already a report or timeline.
4. Use the same table and card conventions already used in Orders and Packages when a listing is present.

### 2.3 Model Scope Pattern

Recommended scopes:

1. Payment::forDashboard()
2. Payment::successful()
3. Payment::pending()
4. Payment::failed()
5. OrderItem::forPaymentDashboard()
6. OrderItem::unpaidForDashboard()
7. OrderItem::paidForDashboard()
8. SubOrder::forPaymentDashboard()
9. SubOrder::unpaidForDashboard()
10. SubOrder::paidForDashboard()

### 2.4 Statistics Pattern

Use one of these approaches depending on the page:

1. Single aggregate query for totals and sums when the summary is simple.
2. Group-by aggregate queries for distribution cards and breakdown widgets.
3. Separate lightweight queries only when a grouped query would become unreadable or harder to maintain.

## 3) Page-Specific Guidance

### 3.1 Payments Dashboard

Goal:

1. Show payment KPIs.
2. Show payment status distribution.
3. Show payment methods breakdown.
4. Show top brands by volume.
5. Paginate the payment table.

Performance rules:

1. Use a slim Payment select list.
2. Eager load only `order`, `brand`, `campaign`, and `paymentMethod` fields needed by the table.
3. Collapse counts and sums into aggregate queries.

### 3.2 Payment Queue

Goal:

1. Show unpaid package items.
2. Show unpaid campaign sub-orders.
3. Show top unpaid influencers.
4. Allow bulk mark actions.

Performance rules:

1. Select only required columns for item tables.
2. Use aggregate queries for top unpaid influencers instead of building the ranking entirely from loaded collections.
3. Keep the queue UI clear and report-like.

### 3.3 Payment Audit

Goal:

1. Show paid items and sub-orders.
2. Show per-admin payout summary.
3. Allow undo actions.

Performance rules:

1. Paginate the paid-item sets.
2. Use aggregate stats for totals and monthly sums.
3. Keep undo actions permission-safe.

### 3.4 Payment Statements

Goal:

1. List influencers who have paid work.
2. Show statement detail and PDF export.

Performance rules:

1. Only load influencers that actually have paid items.
2. Use paid-item scopes for statement detail queries.
3. Keep PDF generation on the server-side and load only the necessary relations.

## 4) Permission and Route Mapping

1. Keep route names aligned with permission slugs.
2. Do not create extra table-only permissions unless the action truly needs a separate gate.
3. If a future AJAX table endpoint is added, map it to the view permission in dashboard middleware.

## 5) Done Criteria for Payments Pages

Do not consider the module complete unless all checks pass:

1. Queries are slimmed to required columns.
2. Aggregate counts/sums are minimized.
3. Dashboard pages render without changing existing workflow.
4. Model scopes are used for repeated payment state logic.
5. Build and diagnostics are clean.
