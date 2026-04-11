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

## 10. Campaign Budget Confirmation (Required Before Order)

Current campaign schema has `budget_min` and `budget_max` and influencer assignments can have `agreed_amount`.

Observed gap:

- Campaign can have a budget range, but there is no strict budget confirmation gate before creating campaign orders.

Recommended budget management policy:

1. Introduce budget lock state before order creation
- Add a campaign financial stage such as `budget_unconfirmed` -> `budget_confirmed` -> `order_ready`.
- Only allow `createFromCampaign` when budget is confirmed.

2. Define confirmation rules
- Rule A: every approved influencer assignment must have `agreed_amount > 0`.
- Rule B: total of approved influencer `agreed_amount` must be within campaign budget constraints.
- Rule C: if total exceeds `budget_max`, order creation must be blocked.
- Rule D: if `budget_min` and `budget_max` are empty, require explicit budget confirmation note by brand/admin before order creation.

3. Store financial snapshots at order creation
- Persist a budget snapshot in order metadata:
	- campaign budget min/max
	- final committed total
	- number of approved influencers
	- confirmation actor and timestamp

4. Freeze budget-relevant values after confirmation
- Once campaign is `order_ready`, block edits to:
	- `budget_min`, `budget_max`, `currency`
	- influencer `agreed_amount`
- Changes after lock should require explicit admin unlock and audit reason.

5. Handle budget mismatch cases
- If approved influencer totals are below `budget_min`, allow warning but require explicit confirmation.
- If above `budget_max`, hard block unless admin exception approval with reason.

Implementation note for current system:

- `createFromCampaign` already checks that each approved influencer has `agreed_amount > 0`.
- Add budget-range validation and budget lock checks in the same action before transaction start.

## 11. Problems Found in Current Package/Campaign Flow

The following issues were observed in the existing system behavior.

### 11.1 Package Flow Problems

1. Status loop vulnerability
- Backward transitions can still occur across UI/admin paths without strict transition guard (example: in_progress -> accepted).

2. Mixed source of truth for order status display
- Some screens compute status from items while others read raw stored order status, causing temporary mismatch labels.

3. Timeline role leakage
- Financial milestones (payout) can appear in places where business asked to hide them from brand view.

4. Parent completion gate drift risk
- Parent completion should always depend on child task approval, not only delivered state; needs one centralized rule.

5. Review model directional conflict
- Single unique `order_item_id` in `reviews` can conflict with strict two-way review requirements on the same task.

### 11.2 Campaign Flow Problems

1. Budget confirmation not enforced as a workflow gate
- Budget range exists in schema but not fully enforced before campaign order creation.

2. Campaign work ownership is not fully explicit
- Current order module gives admin strong control, but influencer/brand campaign status responsibilities are not fully formalized.

3. Campaign transition rules are not state-machine guarded
- Similar loop/rollback risks exist without centralized transition validation.

4. Campaign payout visibility policy is not fully segmented by role
- Needs explicit role-based visibility (admin full, influencer own, brand no influencer payout settlement details).

5. Campaign review parity is incomplete
- Two-way immutable public review policy should be consistently applied to campaign deliverables with clear context references.

### 11.3 Cross-Cutting Problems

1. Scattered validation logic
- Status and review constraints are enforced in multiple controllers; this increases inconsistency risk.

2. Missing mandatory override audit trail
- Admin exceptional rollback or force-change paths need mandatory reason capture and history logging.

3. Inconsistent public-facing explanation text
- User-facing labels and backend status values can diverge, causing confusion in brand/influencer views.
