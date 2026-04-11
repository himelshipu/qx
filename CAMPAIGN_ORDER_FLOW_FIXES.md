# Campaign Order Flow - Gaps Fixed

## Problems Identified

### Gap 1: Empty Initial Message
**Problem:** When brand clicks message button for an influencer in campaign applications, no context is sent. Influencer has no idea what the campaign is about, budget range, or deliverables.

**Solution:** Updated `ConversationController::openOrderConversation()` to send initial message with:
- Campaign title
- Budget range (min-max)
- Duration (start-end dates)
- Campaign description (first 100 chars)
- Context: "Let's discuss the details and negotiate a rate"

**Files Modified:**
- `app/Http/Controllers/Frontend/ConversationController.php`
- `resources/views/frontend/campaigns/designed-show.blade.php` - Added `?campaign={{ $campaign->id }}` to message button link

---

### Gap 2: No Price Negotiation Before Approval
**Problem:** Brand can only approve/reject influencer. No way to propose a price. Influencer doesn't know what they'll be paid until after approval.

**Solution:** Added price input field in the campaign applications table:
- Brand enters price while approving: `$ [input field] + [Approve button]`
- Price stored in `CampaignApplication->proposed_rate`
- Updated `CampaignController::updateApplicationStatus()` to accept `proposed_price` parameter

**Files Modified:**
- `resources/views/frontend/campaigns/designed-show.blade.php` - Added inline price input field in actions
- `app/Http/Controllers/Frontend/CampaignController.php` - Added validation for `proposed_price` parameter

---

### Gap 3: Influencer Cannot Accept/Counter Offer Price
**Problem:** Influencer sees they're approved but doesn't know the price or have a way to accept/negotiate it.

**Solution:** Added Price Negotiation section in influencer's campaign view:
- Shows brand's proposed price: "The brand has proposed: $ XXX.XX"
- Two buttons: **✓ Accept Price** | **Counter Offer**
- Counter Offer opens inline form to enter their price
- When influencer accepts or counters, sends message to moderator conversation

**Price States:**
1. `proposed_rate` set + `agreed_rate` null = Show acceptance/counter offer buttons
2. `agreed_rate` set = Show "✓ Price Agreed" badge with final amount
3. No `proposed_rate` = No price section shown

**Files Modified:**
- `resources/views/frontend/campaigns/designed-show.blade.php` - Added price negotiation section for influencers
- `app/Http/Controllers/Frontend/CampaignController.php` - Updated `updateInfluencerWorkStatus()` to handle:
  - `accept_price` button → Updates `agreed_rate = proposed_rate`
  - `counter_offer` button → Updates `proposed_rate` to new value
  - Both actions send notification messages to brand via conversation

---

## Flow Diagram

```
BRAND SIDE:
───────────
1. Creates campaign (sets budget_min, budget_max)
2. Clicks "Message" on influencer application
   → Conversation opens with auto-message containing campaign details
3. Reviews influencer profile
4. Sets price + clicks Approve
   → proposed_rate is stored

INFLUENCER SIDE:
────────────────
1. Sees campaign application
2. Brand sends message with campaign context
3. Sees application status = "Approved"
4. Sees section: "Brand proposed: $ XXX.XX"
5. Two options:
   a) Accept Price → agreed_rate set → conversation message sent
   b) Counter Offer → New form appears → proposed_rate updated → message sent

BRAND RECEIVES:
───────────────
- If accepted: Message "I accept your price of $ XXX.XX"
- If countered: Message "I'd like to counter. My price is $ YYY.YY"

NEXT STEP (TODO):
─────────────────
- Order creation button should only be enabled when agreed_rate is set
- Both parties have agreed on price
- Then can proceed to create order with agreed amount
```

---

## Database Fields Used

| Field | Table | Purpose |
|-------|-------|---------|
| `proposed_rate` | campaign_applications | Brand's initial price offer |
| `agreed_rate` | campaign_applications | Final agreed price (both parties accept) |
| `status` | campaign_applications | Application status (applied, approved, rejected, completed) |

---

## API/Route Changes

| Route | Method | Change |
|-------|--------|--------|
| `/messages/open/{influencer}` | GET | Now accepts `?campaign={id}` query param to send initial context message |
| `updateApplicationStatus` | POST | Now accepts `proposed_price` parameter |

---

## What's Still Needed

1. **Order Creation Lock:** Only allow order creation when `agreed_rate` is set
2. **Brand Counter-Counter:** Brand should be able to re-counter after influencer counters
3. **Notification System:** Real-time notifications when price is accepted/countered
4. **Payment Tracking:** Link final agreed_rate to SubOrder amount on order creation
5. **Timeline:** Show price negotiation history/timeline in campaign view

---

## Testing Checklist

- [ ] Brand clicks message on influencer, sees campaign context in auto-message
- [ ] Brand enters price and approves influencer
- [ ] Influencer sees "Brand proposed: $ XXX.XX" in campaign view
- [ ] Influencer can click "Accept Price" → conversation shows acceptance message
- [ ] Influencer can click "Counter Offer" → form appears, can enter different price
- [ ] Brand receives counter offer message with new price
- [ ] After agreement, influencer view shows "✓ Price Agreed" badge
