# Campaign Negotiation Flow - Fixed

## Overview

Fixed the campaign negotiation modal to properly handle the **'invited'** status when an admin invites an influencer to a campaign. The brand can now send the first offer/counter without waiting for an influencer's initial application.

## Problem

When admin invited an influencer to a campaign, the workflow was:

1. ✅ Admin invites influencer → `CampaignApplication` created with status `'invited'`
2. ✅ Brand sees "Negotiate" button
3. ❌ Brand clicks "Negotiate" but NO counter/price input was shown
4. ❌ Brand couldn't send a counter offer until influencer applied with a price

The `canCounter` logic only checked for `['applied', 'countered_by_influencer']` but NOT `'invited'`.

## Solution

Updated `canCounter` property in negotiation modal to include `'invited'` status:

**File:** `resources/js/frontend/campaigns-negotiation-modal.js`

### Change 1: canCounter Logic

```javascript
// BEFORE
get canCounter() {
    return in_array(this.status, ['applied', 'countered_by_influencer']);
}

// AFTER
get canCounter() {
    // Brand can counter when: status = 'invited' (initial offer), 'applied' (respond to influencer's initial),
    // or 'countered_by_influencer' (respond to influencer's counter)
    return in_array(this.status, ['invited', 'applied', 'countered_by_influencer']);
}
```

### Change 2: Status Labels

Updated labels to clarify the action needed for 'invited' status:

```javascript
// BEFORE
'invited': 'Awaiting influencer application',

// AFTER
'invited': 'No price set yet - send your offer',
```

---

## Negotiation Workflow

### Scenario 1: Admin Invites → Brand Sends First Offer

```
1. Admin Action:
   - Invites influencer to campaign
   - CampaignApplication created with status='invited'

2. Brand Action:
   - Logs into campaign dashboard
   - Sees "Negotiate" button next to invited influencer
   - Clicks "Negotiate" → Modal opens

3. Modal State (status='invited'):
   - Status Label: "No price set yet - send your offer"
   - Influencer Offer: Not shown (none exists)
   - Brand Counter Input: ✅ SHOWN (canCounter = true)
   - Accept Button: ❌ HIDDEN (canAccept = false)
   - Counter Button: ✅ VISIBLE & ENABLED
   - Reject Button: ✅ VISIBLE & ENABLED

4. Brand Action:
   - Enters price in counter input
   - Clicks "Counter" button
   - Application status changes to 'countered_by_brand'
   - Influencer gets notification

5. Backend Action:
   - Validates brand_offer > 0
   - Updates application.brand_offer
   - Updates application.status = 'countered_by_brand'
   - Updates application.last_counter_by = 'brand'
```

### Scenario 2: Influencer Applies with Offer → Brand Responds

```
1. Influencer Action:
   - Views campaign as published
   - Applies for campaign with initial offer price
   - CampaignApplication created with status='applied'
   - influencer_offer field is populated

2. Brand Action:
   - Sees "Negotiate" button with (applied) status
   - Clicks "Negotiate" → Modal opens

3. Modal State (status='applied'):
   - Status Label: "Influencer initial offer pending"
   - Influencer Offer: ✅ SHOWN (displays influencer's price)
   - Brand Counter Input: ✅ SHOWN (canCounter = true)
   - Accept Button: ✅ VISIBLE & ENABLED (canAccept = true)
   - Counter Button: ✅ VISIBLE & ENABLED
   - Reject Button: ✅ VISIBLE & ENABLED

4. Brand Options:
   Option A: Accept influencer's offer
     - Clicks "Accept" → Accepts influencer_offer
     - Status changes to 'accepted'
     - Work can begin

   Option B: Counter with different price
     - Enters different price
     - Clicks "Counter" → Sends brand_offer
     - Status changes to 'countered_by_brand'
     - Waiting for influencer response

   Option C: Reject
     - Clicks "Reject" → Declines application
     - Status changes to 'declined_by_brand'
```

### Scenario 3: Influencer Counters → Brand Responds

```
1. Previous State:
   - Application status = 'countered_by_brand'
   - Influencer waiting to respond

2. Influencer Action:
   - Views waiting counters
   - Sees brand's counter offer (brand_offer)
   - Counters with different price
   - Status changes to 'countered_by_influencer'
   - influencer_offer updated with new price

3. Brand Action:
   - Sees "Negotiate" button updated
   - Clicks "Negotiate" → Modal opens

4. Modal State (status='countered_by_influencer'):
   - Status Label: "Waiting for your response"
   - Influencer Offer: ✅ SHOWN (influencer's counter price)
   - Brand Counter Input: ✅ SHOWN (canCounter = true)
   - Accept Button: ✅ VISIBLE & ENABLED (canAccept = true)
   - Counter Button: ✅ VISIBLE & ENABLED
   - Reject Button: ✅ VISIBLE & ENABLED

5. Brand Options:
   Option A: Accept influencer's counter
   Option B: Send another counter offer
   Option C: Reject entire negotiation
```

### Scenario 4: Waiting for Response

```
1. Previous State:
   - Application status = 'countered_by_brand'
   - brand_offer is set
   - Waiting for influencer to respond

2. Brand Action:
   - Clicks "Negotiate" → Modal opens

3. Modal State (status='countered_by_brand'):
   - Status Label: "Waiting for influencer response"
   - Your Counter Offer: ✅ SHOWN (displays current brand_offer)
   - Counter Input: ❌ HIDDEN (canCounter = false)
   - Accept Button: ❌ HIDDEN (canAccept = false)
   - Counter Button: ❌ HIDDEN
   - Reject Button: ✅ VISIBLE & ENABLED (can reject anytime)
   - Waiting Message: ✅ SHOWN "Waiting for influencer's response..."
```

---

## Button Enable/Disable Rules

### Accept Button

- **Visible when:** `canAccept = true`
- **Conditions for canAccept = true:**
    - Status = 'applied' AND influencerOffer exists, OR
    - Status = 'countered_by_influencer' AND influencerOffer exists, OR
    - Status = 'countered_by_brand' AND brandOffer exists (influencer accepting)
- **Disabled when:** `isSubmitting = true` (during submission)
- **Always hidden UNLESS:** Valid offer exists in current status

### Counter Button

- **Visible when:** `canCounter = true`
- **Conditions for canCounter = true:**
    - Status = 'invited' (brand sending first offer), OR
    - Status = 'applied' (brand responding to influencer's initial), OR
    - Status = 'countered_by_influencer' (brand responding to counter)
- **Disabled when:**
    - Price input is empty OR ≤ 0, OR
    - `isSubmitting = true` (during submission)
- **Always hidden:** When status = 'countered_by_brand' (waiting for influencer)

### Reject Button

- **Always visible and enabled** (except when `isSubmitting`)
- **Can be clicked anytime** to decline the negotiation
- **Never disabled** because rejection is always an option

---

## Database/Model Details

### CampaignApplication States

| Status                    | Created By       | Next Actor | Can Counter | Can Accept |
| ------------------------- | ---------------- | ---------- | ----------- | ---------- |
| `invited`                 | Admin            | Brand      | ✅ Yes      | ❌ No      |
| `applied`                 | Influencer       | Brand      | ✅ Yes      | ✅ Yes     |
| `countered_by_brand`      | Brand            | Influencer | ❌ No       | ❌ No      |
| `countered_by_influencer` | Influencer       | Brand      | ✅ Yes      | ✅ Yes     |
| `accepted`                | Brand/Influencer | -          | ❌ No       | ❌ No      |
| `declined_by_brand`       | Brand            | -          | ❌ No       | ❌ No      |
| `declined_by_influencer`  | Influencer       | -          | ❌ No       | ❌ No      |

### Fields Used

- `status`: Current negotiation status
- `influencer_offer`: Price offered by influencer (if they applied)
- `brand_offer`: Price counter-offered by brand
- `last_counter_by`: Who sent the most recent counter ('brand' or 'influencer')
- `applied_at`: Timestamp of application/invitation

---

## Testing Checklist

- [ ] Admin invites influencer
    - [ ] Brand sees "Negotiate" button
    - [ ] Modal opens with "No price set yet - send your offer" message
    - [ ] Counter input is visible
    - [ ] Brand can enter price and click "Counter"
    - [ ] Status changes to 'countered_by_brand'
    - [ ] Accept button remains hidden (no offer to accept)
    - [ ] Reject button is always available

- [ ] Influencer applies with offer
    - [ ] Brand sees influencer's offer in modal
    - [ ] Accept button is visible and enabled
    - [ ] Counter button is visible (can negotiate further)
    - [ ] Reject button is visible
    - [ ] Brand can accept, counter, or reject

- [ ] Brand sends counter
    - [ ] Status changes to 'countered_by_brand'
    - [ ] Waiting message shown
    - [ ] Counter input hidden
    - [ ] Reject button still available

- [ ] Influencer counters back
    - [ ] Status changes to 'countered_by_influencer'
    - [ ] Both offers visible
    - [ ] Brand can accept, counter again, or reject

---

## Files Changed

1. **resources/js/frontend/campaigns-negotiation-modal.js**
    - Updated `canCounter` getter to include 'invited' status
    - Updated `statusLabel` for 'invited' to be more descriptive
    - Comments updated for clarity

2. **resources/views/frontend/campaigns/designed-show.blade.php**
    - No changes needed (already supports all states)

---

## Summary

The negotiation modal now properly supports the complete workflow:

1. ✅ Admin invites influencer
2. ✅ Brand sends initial offer when no offer exists yet
3. ✅ Influencer applies with their own offer
4. ✅ Brand and influencer negotiate back and forth
5. ✅ Either party can accept when a valid offer exists
6. ✅ Either party can reject at any time
7. ✅ Accept button only shows when there's a real offer to accept
8. ✅ Reject button always available as escape option
