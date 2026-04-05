# Testing Guide - Campaign & Package Order Workflows

## System Status

✅ **All fixes applied and verified**

- View paths corrected
- Route references fixed
- Database tables exist
- Models properly configured
- Controllers have required methods
- Role-based access control implemented

---

## User Role Capabilities

### 1. BRAND USER

**Can:**

- ✅ Create campaigns
- ✅ Browse and select packages
- ✅ Chat with creators (via moderators) about packages
- ✅ View conversations in conversations list
- ✅ See only conversations they initiated
- ✅ Cannot approve influencers (admin does this)
- ✅ Cannot access campaign influencer management

**Access Points:**

- Dashboard → CAMPAIGNS → All Campaigns / New Campaign
- Dashboard → COMMERCE → Packages → Purchase Package
- Dashboard → COMMUNICATION → Conversations

---

### 2. CREATOR/INFLUENCER USER

**Can:**

- ✅ Create packages with deliverables, timeline, price
- ✅ View their packages in admin (if moderator assigned)
- ✅ **CANNOT see any conversations directly** (security: gets 403 error)
- ✅ **All messaging through moderators only**

**Access Points:**

- Dashboard → COMMERCE → Packages (create/manage)
- **Conversations: BLOCKED (as per design)**

---

### 3. MODERATOR USER

**Can:**

- ✅ Reply to brands on behalf of assigned creators
- ✅ See all conversations where they're assigned as handler
- ✅ Send messages as if they are the creator
- ✅ Be assigned to multiple creators
- ✅ Manage multiple active conversations per creator

**Access Points:**

- Dashboard → COMMUNICATION → Conversations (filtered to assigned ones)
- **Click "View" on conversation → See chat interface**
- **Respond to brand messages as creator**

---

### 4. ADMIN USER

**Can:**

- ✅ Do everything moderators can do
- ✅ Assign influencers to campaigns
- ✅ Approve/Reject influencer assignments
- ✅ Create orders from approved influencers (triggers sub-order creation)
- ✅ Update sub-order status (ongoing → review → completed → cancelled)
- ✅ Mark sub-orders as paid
- ✅ Assign moderators to creators for conversation handling
- ✅ View all conversations (not limited to assigned)
- ✅ Access all admin panels

**Access Points:**

- Dashboard → CAMPAIGNS → Influencer Assignments
- Dashboard → CAMPAIGNS → Campaign Name → Assign Influencers
- Dashboard → COMMERCE → Orders
- Dashboard → COMMUNICATION → Conversations

---

### 5. SUPER ADMIN

**Can:**

- ✅ Everything admin can do
- ✅ Create/delete other admins
- ✅ System-wide settings

---

## Testing Workflows

### WORKFLOW A: Campaign Order

**Step 1: Brand Creates Campaign**

1. Login as BRAND
2. Go to Dashboard → CAMPAIGNS → New Campaign
3. Fill in campaign details
4. Save campaign

**Step 2: Admin Assigns Influencers**

1. Login as ADMIN
2. Go to Dashboard → CAMPAIGNS → All Campaigns
3. Click on created campaign
4. Go to "Influencer Assignments" section
5. Click "Assign Influencers"
6. Select creators to assign
7. Submit

**Step 3: Admin Approves Influencers**

1. In campaign Influencers view
2. See list of assigned creators
3. Click "Approve" on selected creators
4. (Behind scenes: Master Order + Sub-orders created)

**Step 4: Track Sub-Orders**

1. Go to Dashboard → COMMERCE → Orders
2. Click on master order created from campaign
3. See sub-orders table
4. Update individual sub-order status
5. Mark as paid when work complete

### WORKFLOW B: Package Order

**Step 1: Creator Creates Package**

1. Login as CREATOR
2. Go to Dashboard → COMMERCE → Packages → Create
3. Fill package details (price, deliverables, timeline)
4. Save package

**Step 2: Brand Purchases Package**

1. Login as BRAND
2. Go to Dashboard → COMMERCE → Packages → Purchase Package
3. Select creator and package
4. Confirm purchase
5. (Behind scenes: Order + Conversation created)

**Step 3: Chat About Package**

1. Brand goes to Dashboard → COMMUNICATION → Conversations
2. Sees new conversation for package order
3. Clicks "View" to open chat
4. Types message to "creator"

**Step 4: Moderator Responds**

1. Login as MODERATOR assigned to that creator
2. Go to Dashboard → COMMUNICATION → Conversations
3. Sees same conversation (filtered to assigned ones)
4. Clicks "View" to open chat
5. Sees brand's message
6. Types response (appears as if creator responded)

**Step 5: Admin Assigns/Reassigns Moderator**

1. Login as ADMIN
2. Go to Dashboard → COMMUNICATION → Conversations
3. If no moderator assigned, sees notification
4. Click dropdown to assign moderator
5. Select moderator from list
6. Save

---

## Testing Checklist

### Conversation View Access

- [ ] Brand can view conversations list
- [ ] Brand sees only their own conversations
- [ ] Moderator can view conversations list
- [ ] Moderator sees only assigned conversations
- [ ] Admin can view all conversations
- [ ] Creator gets 403 error when accessing conversations
- [ ] Non-authenticated user redirected to login

### Campaign Influencers View Access

- [ ] Admin can view influencer assignments
- [ ] Admin can assign new influencers
- [ ] Admin can approve influencers
- [ ] Admin can reject influencers
- [ ] Admin can cancel assignments
- [ ] Brand cannot access influencer assignments
- [ ] Creator cannot access influencer assignments

### Chat Interface Testing

- [ ] Messages display in correct order
- [ ] Timestamps show correctly
- [ ] Brand can send messages
- [ ] Moderator can send messages
- [ ] Messages appear immediately (or after refresh)
- [ ] Indication of who sent message (brand vs moderator)
- [ ] Dark mode works correctly
- [ ] Responsive on mobile

### Order Creation Testing

- [ ] Approving influencer creates master order
- [ ] Master order creates sub-orders for each approved influencer
- [ ] Sub-order status can be changed
- [ ] Sub-order can be marked as paid
- [ ] Payment tracking works
- [ ] Delivery status updates visible

---

## Known Working Routes

```
GET    /dashboard/conversations
       → Lists conversations (filtered by role)

GET    /dashboard/conversations/{id}
       → Shows chat interface
       → Accessible only to brand or assigned moderator

POST   /dashboard/conversations/{id}/messages
       → Creates new message
       → Requires proper authorization

POST   /dashboard/conversations/{id}/assign-moderator
       → Assigns moderator to conversation
       → Admin only

GET    /dashboard/campaigns/{id}/influencers
       → Lists influencers assigned to campaign

GET    /dashboard/campaigns/{id}/influencers/create
       → Form to assign new influencers

POST   /dashboard/campaigns/{id}/influencers
       → Creates influencer assignments

POST   /dashboard/campaign-influencers/{id}/approve
       → Approves influencer (creates orders)

POST   /dashboard/campaign-influencers/{id}/reject
       → Rejects influencer assignment

POST   /dashboard/campaign-influencers/{id}/cancel
       → Cancels influencer assignment
```

---

## Debugging Tips

If you encounter issues:

1. **"View not found" error:**
    - Check view file exists at `resources/views/backend/pages/...`
    - Verify route name matches `dashboard.*` prefix

2. **"Route not found" error:**
    - Clear route cache: `php artisan route:cache`
    - Clear config cache: `php artisan config:cache`

3. **"Conversation not loading" error:**
    - Check user is logged in and has `user_type` set
    - Verify `conversations` table has data
    - Check `creator_id` and `brand_user_id` are valid

4. **"Access denied to conversations" error:**
    - If creator, this is EXPECTED (403 error)
    - If brand, verify `brand_user_id` matches logged-in user
    - If moderator, verify `handled_by_user_id` matches logged-in user

---

## Database Check Commands

```bash
# Check conversations table exists
php artisan tinker
> DB::table('conversations')->count()

# Check users have user_type
> DB::table('users')->pluck('user_type')->unique()

# Check campaign_influencers
> DB::table('campaign_influencers')->count()

# Check messages table
> DB::table('messages')->count()
```

---

## Performance Notes

- Conversations paginated (15 per page)
- Messages paginated (20 per page)
- Queries use eager loading to prevent N+1 problems
- Users should see fast load times

---

## Next Steps After Testing

1. ✅ Verify all workflows work as intended
2. ⏳ Add email notifications for new messages
3. ⏳ Add real-time message updates (WebSockets)
4. ⏳ Add file uploads in messages
5. ⏳ Add message reactions (laughing, thumbs up, etc)
6. ⏳ Add bulk operations (mark all as read, etc)
7. ⏳ Add search functionality in conversations
8. ⏳ Add filters (unread, archived, etc)

---

**Last Updated:** April 5, 2026
**Status:** Ready for Testing ✅
