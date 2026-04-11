# Order, Task, Payment, Timeline, and Review Flow Reference

This document reflects the current implemented behavior and the pre-implementation decisions to apply next.

## 1. Core Entities and Levels

The system currently operates with these layers:

- Parent Order: checkout-level container, usually one per brand checkout.
- Child Order: influencer-level order under a parent order for package flow.
- Order Item (Task): package deliverable line item under an order.
- Sub Order: campaign-specific influencer work unit under a campaign order.
- Review: linked to a single order item.

## 2. Package Flow (Current Behavior)

### 2.1 Creation

When brand completes cart checkout:

- One Parent Order is created with status pending.
- One Child Order is created per influencer with status pending.
- One or more Order Items are created under each Child Order with status pending.
- Due date is set on each task from package delivery days if available.

### 2.2 Task Status Flow (Package Item)

Current package item statuses used in UI and controllers:

- pending
- accepted
- in_progress
- delivered
- approved
- rejected
- cancelled
- completed (admin-level option exists)

Typical expected path:

- pending -> accepted -> in_progress -> delivered -> approved

Possible rejection path:

- delivered -> rejected -> delivered -> approved

Backward loop risk that must be blocked in the next implementation:

- accepted -> in_progress -> accepted (should be disallowed)
- delivered -> in_progress (should be disallowed except admin force-override with reason)

### 2.3 Who Can Update Package Task Status

- Influencer (frontend order page): pending, accepted, in_progress, delivered
- Brand (frontend order page): approved or rejected decision after delivery
- Admin (dashboard order page): can set full item workflow including admin-only states

### 2.4 Parent and Child Order Status Sync

System computes order-level status from item-level statuses:

- all pending -> order pending
- all accepted -> order accepted
- all approved or completed -> order approved
- all delivered/approved/completed -> order delivered
- mixed in-progress state -> order in_progress

Transition policy to apply next:

- Status transitions must be forward-only for normal actors.
- Backward transitions should be admin-only and require mandatory reason logging.

### 2.5 Parent Completion Rule (Brand)

Brand can complete parent order only when all child tasks are approved or completed.

If not fully approved, complete action is blocked.

## 3. Campaign Flow (Current Behavior)

### 3.1 Campaign Order Creation

Admin creates campaign master order from approved campaign influencers:

- Master order created with status pending
- One Sub Order per approved influencer created with status pending

### 3.2 Sub Order Status Flow (Campaign)

Current sub order statuses:

- pending
- accepted
- in_progress
- on_review
- completed
- cancelled

### 3.3 Who Can Update Campaign Work Status

Current implementation in order module:

- Admin updates sub order status from dashboard

Design requirement to apply next:

- Campaign work status ownership must be explicit per role (influencer progress, brand review, admin override).
- Campaign should follow the same anti-loop transition policy as package tasks.

### 3.4 Campaign Auto Completion

Campaign master order is auto-completed when all non-cancelled sub orders are completed.

## 4. Payment Flow (Current Behavior)

### 4.1 Brand Checkout Payment

At checkout, pricing applies:

- subtotal
- platform charge
- total

Order records are created with those totals.

### 4.2 Influencer Payout Recording

Current payout marking is manual by admin:

- Package flow: admin marks each order item paid and records amount/reference/note/marked by
- Campaign flow: admin marks each sub order paid and records amount/reference/note/marked by

### 4.3 Payment Status Ownership

- Admin controls payout recorded state for tasks/sub orders.
- Brand and influencer cannot directly mark payout as paid.

Visibility policy to apply next:

- Brand does not need influencer payout settlement details.
- Influencer payout status should be visible to admin and influencer only.
- Brand pages should show only brand-side payment obligations and order completion state.

## 5. Timeline Display (Where and What)

### 5.1 Brand Order Detail Page

Parent compact timeline shows:

- Order placed
- Parent order created
- Accepted
- Completed

Design change to apply next:

- Remove payout stage from brand timeline (admin/internal financial step).

Child order timeline shows:

- Placed
- Accepted
- Delivered
- Reviewed
- Completed

Task card timeline shows:

- Placed
- Accepted
- Delivered

### 5.2 Influencer Order Detail Page

Task-level timeline is shown per assigned item:

- Placed
- Accepted
- Delivered

### 5.3 Admin Order Detail Page

Admin sees:

- order-level status controls
- item-level status controls
- campaign sub-order timeline fields (accepted/completed/paid)
- payout record fields for item and sub-order

## 6. Review Flow (Current Behavior)

Reviews are immutable and always public after submission.

### 6.1 Who Reviews Whom

- Brand -> Influencer: per approved/completed task from brand order detail page
- Influencer -> Brand: from influencer order detail page after order completion

Campaign parity requirement to apply next:

- Campaign deliverables must have the same two-way review semantics as package tasks.
- Review context must include campaign/task identifier, reviewer, reviewee, and order/sub-order reference.

### 6.2 Visibility Rules

- All submitted reviews are public
- No hide toggle should be used after submit
- No edit after submit
- No delete after submit

### 6.3 Where Reviews Are Shown

- Influencer public profile: reviews received for that influencer, with reviewer and task/order context
- Brand public profile: reviews received for that brand, with reviewer and task/order context

## 7. Role Responsibility Matrix

| Area | Brand | Influencer | Admin |
|---|---|---|---|
| Create package checkout | Yes | No | Optional/manual |
| Update own task work status | No | Yes | Yes (override) |
| Approve/reject delivered task | Yes | No | Yes |
| Complete parent package order | Yes | No | Yes (override) |
| Update campaign sub-order status | No | No (in order module) | Yes |
| Mark payouts paid | No | No | Yes |
| Submit review to other party | Yes (to influencer per task) | Yes (to brand) | No |
| Edit/hide/delete submitted review | No | No | No |

## 8. Known Current Constraint

Current review table has unique order_item_id.

This means one order item can hold only one review row total. If both sides need separate review records tied to the same exact task, current schema can conflict.

## 9. Suggestions

The following suggestions are prioritized based on the updated business rules.

### 9.1 Must Implement First (Safety + Correctness)

1. Add strict transition state machine (package and campaign)
- Enforce forward-only transitions for normal actors.
- Block loops like accepted -> in_progress -> accepted and delivered -> in_progress.
- Allow admin rollback only through a dedicated override path with mandatory reason.
- Persist override reason and actor in status history.

2. Add role-scoped visibility for payment and payout
- Brand UI: hide influencer payout cleared/paid details.
- Influencer UI: show own payout state only.
- Admin UI: show full payout lifecycle with references.

3. Normalize timeline scopes by role
- Brand timeline: operational delivery/review/completion only.
- Influencer timeline: assignment/progress/review result.
- Admin timeline: full operational + financial + override history.

### 9.2 Review Model Hardening

4. Introduce directional reviews schema
- Add explicit fields for reviewer_type and reviewee_type (or split into brand_to_influencer_reviews and influencer_to_brand_reviews).
- Remove single-row-per-order-item limitation for two-way review cases.

5. Keep reviews immutable and always public by data contract
- Enforce immutable create-only behavior at model/service level.
- Remove any remaining visibility toggle UI/actions from admin.

### 9.3 Campaign Alignment

6. Align campaign work lifecycle with package governance
- Define campaign equivalent stages: assigned -> accepted -> in_progress -> submitted/on_review -> approved/rejected -> completed.
- Define exactly who can move each stage (influencer, brand, admin).

7. Define campaign payout checkpoints
- Payout release condition should be tied to approved campaign deliverables.
- Keep payout update right with admin only.

8. Define campaign review surfaces
- After campaign deliverable approval/completion, allow two-way review following same immutability/public rules.
- Show campaign review data on public profiles with campaign/task references.

### 9.4 Technical Consistency

9. Add explicit status enum contracts
- Define shared status constants for order, item, and sub-order to avoid drift between UI labels and controller validation lists.

10. Add status history logging for every transition
- Persist actor, old status, new status, reason, and timestamp for auditability.

11. Unify payout and payment reconciliation
- Link manual payout events to a normalized payment ledger record and show reconciliation status per order.

12. Add policy-guarded state machine service
- Centralize legal transitions per role instead of scattered controller checks.
