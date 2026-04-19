# Campaign Work Status Workflow - COMPLETE STATE MACHINE FIXES

## Overview

Fixed critical workflow flaw where influencers could skip steps and move backward in the work status workflow. The complete state machine validation is now applied across:

1. **Influencer Work Status Updates** (Frontend Controller)
2. **Brand Work Status Updates** (Frontend Controller)
3. **Backend Package Order State Machine** (Backend Order Controller)
4. **UI/Blade Templates** (Dropdown with only valid transitions)

---

## Problem Statement

### Before Fixes

- ❌ Influencer could click dropdown and jump to ANY status (pending → delivered → on_review → completed)
- ❌ Influencer could go backward (on_review → pending)
- ❌ Influencer could set completed/on_review/approved directly
- ❌ No validation prevented skipping steps
- ❌ UI showed all 5 options regardless of current status

### After Fixes

- ✅ Only valid next transitions are offered
- ✅ Backward transitions prevented (strict linear flow)
- ✅ Status can only change via proper workflow
- ✅ UI only shows applicable options
- ✅ Backend rejects invalid transitions with error message

---

## INFLUENCER WORKFLOW (Work Progression)

### Allowed Influencer Transitions

```
pending → accepted
  ↓
accepted → in_progress
  ↓
in_progress → delivered
  ↓
(Brand reviews: delivered → on_review → approved/changes_requested → completed)

Special Case: changes_requested
  Can go: → in_progress (back to fix) OR → delivered (resubmit)
```

### Influencer CANNOT Do

- ❌ Skip steps (pending → in_progress)
- ❌ Go backward (in_progress → accepted)
- ❌ Set to on_review (brand only)
- ❌ Set to approved (brand only)
- ❌ Set to completed (brand only)

### Status Meanings (Influencer)

| Status              | Meaning                                      | Influencer Action         |
| ------------------- | -------------------------------------------- | ------------------------- |
| `pending`           | Order placed, awaiting influencer acceptance | Must click Accept         |
| `accepted`          | Influencer accepted the work                 | Must click Start Work     |
| `in_progress`       | Influencer is actively working               | Work on deliverables      |
| `delivered`         | Influencer submitted work for review         | ⏳ Wait for brand review  |
| `changes_requested` | Brand asked for revisions                    | Fix & resubmit OR abandon |

---

## BRAND WORKFLOW (Review & Approval)

### Allowed Brand Transitions

```
delivered (influencer submitted) → on_review
  ↓
on_review → approved (accept work) OR changes_requested (ask for revisions)
  ↓
If approved:
  approved → completed
    ↓
  completed → review_pending
    ↓
  review_pending → reviewed (TERMINAL)

If changes_requested:
  (Waiting for influencer to resubmit as 'delivered')
```

### Brand CANNOT Do

- ❌ Jump to approved without on_review
- ❌ Set to pending/accepted/in_progress (influencer only)
- ❌ Skip the review phase

### Status Meanings (Brand)

| Status              | Meaning                             | Brand Action               |
| ------------------- | ----------------------------------- | -------------------------- |
| `delivered`         | Influencer submitted work           | Review the submission      |
| `on_review`         | Work is under review                | Approve or request changes |
| `approved`          | Work accepted, moving to completion | Proceed to completion      |
| `changes_requested` | Asking for revisions                | Wait for resubmission      |
| `completed`         | Work finalized                      | Proceed to rating          |
| `review_pending`    | Final rating phase pending          | Rate/review work           |
| `reviewed`          | Final review complete               | DELETED (terminal)         |

---

## Implementation Details

### 1. INFLUENCER: CampaignApplicationController.php

**Method:** `updateInfluencerWorkStatus()`

**Lines:** 277-343

**Changes:**

```php
// BEFORE: Allowed any status
'work_status' => 'required|in:pending,accepted,in_progress,on_review,completed'

// AFTER: Only influencer-controllable statuses
'work_status' => 'required|in:accepted,in_progress,delivered'
```

**Validation Added:**

```php
// Check current status and validate transition
if (!$this->canInfluencerTransitionStatus($currentStatus, $newStatus)) {
    return error("Cannot transition from '{$currentStatus}' to '{$newStatus}'")
}
```

**Transition Rules (New Helper Method: `canInfluencerTransitionStatus()`):**

```php
private function canInfluencerTransitionStatus(string $from, string $to): bool
{
    $allowedTransitions = [
        'pending'           => ['accepted'],
        'accepted'          => ['in_progress'],
        'in_progress'       => ['delivered'],
        'changes_requested' => ['in_progress', 'delivered'],
    ];

    return in_array($to, $allowedTransitions[$from] ?? [], true);
}
```

**Timestamps Tracked:**

- `accepted_at` - When influencer accepted the work
- `delivered_at` - When influencer submitted work
- Future: `completed_at`, `reviewed_at` via brand updates

---

### 2. BRAND: CampaignApplicationController.php

**Method:** `updateBrandWorkStatus()`

**Lines:** 345-402

**Changes:**

```php
// BEFORE: Allowed any status
'work_status' => 'required|in:pending,accepted,in_progress,on_review,completed'

// AFTER: Only brand-controllable statuses
'work_status' => 'required|in:on_review,approved,changes_requested,completed,review_pending,reviewed'
```

**Validation Added:**

```php
// Check current status and validate transition
if (!$this->canBrandTransitionStatus($currentStatus, $newStatus)) {
    return error("Cannot transition from '{$currentStatus}' to '{$newStatus}'")
}
```

**Transition Rules (New Helper Method: `canBrandTransitionStatus()`):**

```php
private function canBrandTransitionStatus(string $from, string $to): bool
{
    $allowedTransitions = [
        'delivered'         => ['on_review'],
        'on_review'         => ['approved', 'changes_requested'],
        'changes_requested' => [], // Waiting for influencer resubmit
        'approved'          => ['completed'],
        'completed'         => ['review_pending'],
        'review_pending'    => ['reviewed'],
        'reviewed'          => [], // TERMINAL
    ];

    return in_array($to, $allowedTransitions[$from] ?? [], true);
}
```

**Timestamps Tracked:**

- `approved_at` - When brand approved work
- `completed_at` - When work moved to completion
- `reviewed_at` - When final review completed

---

### 3. UI: designed-show.blade.php (Influencer View)

**Lines:** 917-965

**Changes:**

```blade
<!-- BEFORE: All options always shown -->
<option value="pending">Order Pending</option>
<option value="accepted">Accepted</option>
<option value="in_progress">In Progress</option>
<option value="on_review">On Review</option>
<option value="completed">Completed</option>

<!-- AFTER: Dynamic options based on current status -->
@if ($influencerApplication->work_status === 'pending')
    <option value="pending" selected disabled>Order Pending (current)</option>
    <option value="accepted">→ Accept Order & Start Preparation</option>
@elseif ($influencerApplication->work_status === 'accepted')
    <option value="accepted" selected disabled>Accepted (current)</option>
    <option value="in_progress">→ Start Working on Deliverables</option>
@elseif ($influencerApplication->work_status === 'in_progress')
    <option value="in_progress" selected disabled>In Progress (current)</option>
    <option value="delivered">→ Submit Work for Review (Delivered)</option>
@elseif ($influencerApplication->work_status === 'changes_requested')
    <option value="changes_requested" selected disabled>Changes Requested (current)</option>
    <option value="in_progress">← Go Back to In Progress</option>
    <option value="delivered">→ Submit Revised Work (Delivered)</option>
@elseif ($influencerApplication->work_status === 'delivered')
    <option value="delivered" selected disabled>⏳ Waiting for Brand Review...</option>
@else
    <option value="{{ $influencerApplication->work_status }}" selected disabled>
        {{ ucfirst(str_replace('_', ' ', $influencerApplication->work_status)) }} (Brand Controlled)
    </option>
@endif
```

**UI Improvements:**

- Shows workflow arrows (→ for forward, ← for backward from changes_requested)
- Descriptive action labels
- Displays current status in blue
- Shows "Waiting for Brand Review" when no action available
- Workflow guide at top: "Pending → Accepted → In Progress → Delivered → (Brand Reviews)"
- Submit button hidden when status is brand-controlled

---

### 4. BACKEND: OrderController.php

**Reference Implementation Already Done in Previous PR**

The backend `canTransitionSubOrderStatus()` method (already implemented):

```php
$allowed = [
    'pending'           => ['accepted', 'cancelled'],
    'accepted'          => ['in_progress', 'cancelled'],
    'in_progress'       => ['delivered', 'cancelled'],
    'changes_requested' => ['delivered', 'in_progress', 'cancelled'],

    'delivered'         => ['on_review', 'cancelled'],
    'on_review'         => ['approved', 'changes_requested', 'cancelled'],
    'approved'          => ['completed', 'cancelled'],

    'completed'         => ['review_pending', 'cancelled'],
    'review_pending'    => ['reviewed', 'cancelled'],

    'reviewed'          => [],
    'cancelled'         => []
];
```

This provides the gold standard that the frontend now mirrors.

---

## Complete Status Flow Diagram

```
┌─────────────────────────────────────────────────────────────────────────┐
│                        CAMPAIGN WORK WORKFLOW                           │
└─────────────────────────────────────────────────────────────────────────┘

INFLUENCER PHASE:
┌─────────┐
│ pending ├──(ACCEPT)──┐
└─────────┘            │
                       v
                 ┌──────────┐
                 │ accepted ├──(START WORK)──┐
                 └──────────┘                 │
                                              v
                                      ┌────────────────┐
                                      │  in_progress   ├──(SUBMIT)──┐
                                      └────────────────┘            │
                                                                     v
                                                            ┌──────────────┐
                                                            │  delivered   │
                                                            └──────────────┘
                                                                     ^
                           ┌─────────────────────── BACK ───────────┘
                           │
                  ┌────────────────────┐
                  │ changes_requested  │
                  │ (go back to fix)   │
                  └────────────────────┘

BRAND PHASE (Review & Approval):
                       ┌──────────────┐
                       │  delivered   │
                       └──────────────┘
                              │
                         (REVIEW)
                              v
                       ┌─────────────┐
                       │ on_review   │
                       └─────────────┘
                             / \
                     (APPROVED)   (REVISIONS)
                           /         \
              v          v
        ┌─────────┐  ┌────────────────────┐
        │Approved │  │ changes_requested  │──→ [Influencer fixes & resubmits as 'delivered']
        └─────────┘  └────────────────────┘
              │
         (COMPLETE)
              v
        ┌──────────┐
        │Completed │
        └──────────┘
              │
        (FINAL REVIEW)
              v
        ┌──────────────┐
        │review_pending│
        └──────────────┘
              │
         (FINALIZE)
              v
        ┌──────────┐
        │ reviewed │ ← TERMINAL STATE
        └──────────┘
```

---

## Testing Checklist

### Influencer (Work Progression)

- [ ] **Pending State**
    - [ ] ✅ Can only select "Accept Order" from dropdown
    - [ ] ✅ Cannot jump to in_progress
    - [ ] ✅ Cannot go to on_review/completed

- [ ] **Accepted State**
    - [ ] ✅ Can only select "Start Working" from dropdown
    - [ ] ✅ Cannot go backward to pending
    - [ ] ✅ Cannot jump to delivered

- [ ] **In Progress State**
    - [ ] ✅ Can only select "Submit Work (Delivered)" from dropdown
    - [ ] ✅ Cannot skip to completed
    - [ ] ✅ Cannot go backward

- [ ] **Delivered State**
    - [ ] ✅ Dropdown shows "⏳ Waiting for Brand Review"
    - [ ] ✅ No update button (read-only)
    - [ ] ✅ Cannot self-update status

- [ ] **Changes Requested State**
    - [ ] ✅ Can select "Go Back to In Progress" (← arrow)
    - [ ] ✅ Can select "Submit Revised Work" (→ arrow)
    - [ ] ✅ No other options available

### Brand (Review & Approval)

- [ ] **Delivered → On Review**
    - [ ] ✅ Can move to on_review
    - [ ] ✅ Cannot skip to approved

- [ ] **On Review**
    - [ ] ✅ Can select "Approved"
    - [ ] ✅ Can select "Request Changes"
    - [ ] ✅ Cannot go back to delivered

- [ ] **Changes Requested**
    - [ ] ✅ No immediate options (waiting for influencer)
    - [ ] ✅ Influencer can resubmit as delivered
    - [ ] ✅ Brand can review delivered again

- [ ] **Approved → Completed**
    - [ ] ✅ Can move to completed
    - [ ] ✅ Cannot go back

- [ ] **Completed → Review Pending → Reviewed**
    - [ ] ✅ Linear progression
    - [ ] ✅ Reviewed is terminal

### Invalid Transitions (Should Fail)

- [ ] ✅ Influencer trying to directly POST pending→delivered (rejected)
- [ ] ✅ Influencer trying to POST in_progress→on_review (rejected)
- [ ] ✅ Influencer trying to POST pending→completed (rejected)
- [ ] ✅ Brand trying to approve without on_review (rejected)
- [ ] ✅ Brand trying to go backward (rejected)

---

## Error Messages

When invalid transition attempted:

**Backend Response:**

```
Cannot transition from 'in_progress' to 'on_review'.
Status transitions are strictly controlled.
```

```
Cannot transition from 'delivered' to 'pending'.
Transition is not allowed by workflow rules.
```

---

## Files Modified

1. **app/Http/Controllers/Frontend/CampaignApplicationController.php**
    - Lines 277-343: `updateInfluencerWorkStatus()` + validation + helper
    - Lines 345-402: `updateBrandWorkStatus()` + validation + helper
    - Added: `canInfluencerTransitionStatus()` helper method
    - Added: `canBrandTransitionStatus()` helper method

2. **resources/views/frontend/campaigns/designed-show.blade.php**
    - Lines 917-965: Dynamic dropdown with only valid transitions
    - Added: Workflow explanation box
    - Added: Status labels with arrows (→ ←)
    - Added: Conditional submit button

---

## Summary

**Before:** Free-for-all status changes = chaos
**After:** Strict linear workflow with validation at both UI and backend layers

The influencer now follows a clear path:

```
pending → accepted → in_progress → delivered → [Wait for brand review]
                                                           ↓
                                                    [Brand reviews]
                                                           ↓
                                                   on_review → approved → completed → review_pending → reviewed
```

No skipping. No going backward (except for changes_requested recovery). No confusion.
