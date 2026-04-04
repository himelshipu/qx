ঠিক আছে। আর বকবক না। পুরো কনসেপ্ট ক্লিয়ার করে দিচ্ছি।

---

# Project Concept: Influencer Marketing & Campaign Management Platform

**Inspiration:** [Collabstr.com](https://collabstr.com)
**Core Logic:** Two workflows + Sub-Order system

---

## 1. User Types (5 Roles)

| User Type | What They Do |
| :--- | :--- |
| **Brand** | Creates campaigns, approves/cancels influencers, buys packages, chats |
| **Influencer** | Creates packages. **Never sees chat** (Moderator speaks for them) |
| **Moderator** | Chats **AS** the influencer with brands. Handles multiple influencers |
| **Admin** | Assigns influencers to campaigns, updates status, tracks payments, assigns moderators |
| **Super Admin** | Creates/deletes Admins. Full system access |

> Super Admin creates the first Admin.

---

## 2. Two Types of Orders

### Type A: Campaign Order
- Brand creates campaign → Admin assigns influencers → Brand approves some → Admin creates order
- **Structure:** 1 Master Order → Multiple Sub-Orders (one per approved influencer)
- **Why:** Each influencer has independent status and payment

### Type B: Package Order
- Brand buys influencer's package directly → Order created instantly
- **Structure:** 1 Order = 1 Package (no sub-orders)
- **Why:** Simple, direct purchase

---

## 3. Workflow A: Campaign Order

**Step 1:** Brand creates campaign (e.g., "Need 5 influencers for deodorant ad")

**Step 2:** Admin assigns 5 influencers to campaign

**Step 3:** Brand approves 3, cancels 2

**Step 4:** Admin clicks "Create Order" → System creates 1 Master Order + 3 Sub-Orders

**Step 5:** Admin updates each Sub-Order status independently:
- Ongoing → On Review → Completed → Cancelled

**Step 6:** Admin pays each influencer manually (off-platform) → Records payment in system

---

## 4. Workflow B: Package Order

**Step 1:** Influencer creates packages (price, deliverables, timeline)

**Step 2:** Brand browses packages → Adds to Cart

**Step 3:** Brand clicks "Confirm" → Order created instantly

**Step 4:** Chat opens with Moderator (assigned to that influencer)

**Step 5:** Brand chats (thinks it's influencer) → Moderator replies AS influencer

**Step 6:** Work completed → Admin updates status → Manual payment

---

## 5. Chat Flow (Moderator Mediated)

**When Brand sends a message:**
1. Message sent
2. If no moderator assigned → Admin sees: "Brand X messaged Influencer Y"
3. Admin assigns a moderator to that influencer
4. From then on, that moderator acts AS that influencer for ALL brands
5. **Real influencer never sees any chat**

**One moderator can handle multiple influencers:**
- Moderator Rahim → Acts as Influencer Sumi → Chats with Brand Pran
- Moderator Rahim → Acts as Influencer Sumi → Chats with Brand RFL
- Moderator Rahim → Acts as Influencer Jara → Chats with Brand Skipper

---

## 6. Login Redirect Rules

| Scenario | Redirect After Login |
| :--- | :--- |
| Normal login | → Dashboard |
| Login during package confirmation | → That influencer's Chatbox |
| Login after clicking "Negotiate" | → That influencer's Chatbox |

---

## 7. Status Values

**Campaign Status (existing):**
```
pending → payment processed → on progress → order complete → payment completed → campaign completed
```

**Sub-Order Work Status (new):**
```
ongoing → on_review → completed → cancelled
```

**Package Order Status (new):**
```
ongoing → on_review → completed → cancelled
```

---

## 8. Complete Diagram

```
SUPER ADMIN
    │
    └── Creates Admins
            │
            ├──────────┬──────────┬──────────┐
            │          │          │          │
         BRAND    MODERATOR    INFLUENCER    ADMIN


CAMPAIGN ORDER FLOW:
═════════════════════
BRAND                    ADMIN
  │                        │
  │ 1. Creates campaign    │
  │                        │
  │                    2. Assigns 5 influencers
  │                        │
  │ 3. Approves 3          │
  │                        │
  │                    4. Creates 1 Master Order
  │                       + 3 Sub-Orders
  │                        │
  │                    5. Updates each Sub-Order status
  │                        │
  │                    6. Manual payment + tracking


PACKAGE ORDER FLOW:
═════════════════════
BRAND                    MODERATOR
  │                        │
  │ 1. Selects package     │
  │    + Add to cart       │
  │                        │
  │ 2. Clicks Confirm      │
  │                        │
  │                    3. Order created
  │                       Chat opens
  │                        │
  │ 4. Chats               │
  │                        │
  │                    5. Replies AS influencer
  │                        │
  │                    6. Status update → Payment


CHAT FLOW:
═══════════
Brand sends message → Admin sees (if no moderator)
                    → Admin assigns moderator
                    → Moderator replies AS influencer
                    → Influencer never sees
```

---

## 9. Copilot Implementation Guide

### Before ANY code, explore and report:

```text
1. What framework? (Laravel/Django/Express/Rails/etc.)
2. What folder structure?
3. What tables already exist? (users, campaigns, orders, chats, packages)
4. Does users table have a 'role' column? What values?
5. Show me existing controller patterns.
6. Show me existing route definitions.
7. What frontend? (React/Blade/EJS/etc.)
```

### Then implement ADDING only, never DELETE:

| Feature | Action |
| :--- | :--- |
| 5 User Types | Add `role` column if missing |
| Campaign Influencer Assignment | Add `campaign_influencers` table |
| Master Order + Sub-Orders | Add `orders` + `sub_orders` tables |
| Packages | Add `packages` table if missing |
| Cart | Add `carts` table |
| Moderator Assignment | Add `moderator_assignments` table |
| Manual Payments | Add `manual_payments` table |

### Follow existing patterns for:
- Controller naming
- Model relationships
- Route definitions
- Response formats

---

## 10. Summary

| What | How |
| :--- | :--- |
| Campaign Order | Campaign → Assign → Approve → Create Order → Sub-Orders |
| Package Order | Select Package → Cart → Confirm → Direct Order |
| Chat | Brand messages → Admin assigns moderator → Moderator replies AS influencer |
| Payment | Manual off-platform → Admin records in system |

---

**End of Concept**