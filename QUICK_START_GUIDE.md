# ROCKIES Platform - Quick Reference Guide

## 🚀 Fast Start

The platform is fully set up with comprehensive test data and proper software patterns implemented.

### Test Login Credentials

All test accounts use: **Password: `password`**

| Role          | Email                        | Use Case                                     |
| ------------- | ---------------------------- | -------------------------------------------- |
| 👑 SuperAdmin | superadmin@rockies.local     | **SUPREME LEADER** - Complete system control |
| Admin         | testadmin@system.local       | Full system control, user management         |
| Moderator     | testmoderator@system.local   | Campaign moderation, dispute resolution      |
| Brand 1       | testbrand1@system.local      | Create campaigns, manage orders              |
| Brand 2       | testbrand2@system.local      | Alternative brand for testing                |
| Influencer 1  | testinfluencer1@system.local | Manage packages, accept orders               |
| Influencer 2  | testinfluencer2@system.local | Alternative influencer for testing           |

**🌟 SuperAdmin (superadmin@rockies.local):**

- Complete system access with all 144 permissions
- Can create and manage roles (Admin, Moderator, Manager, etc.)
- Can assign permissions to any role
- Initial supreme leader with full authority
- Can delegate specific roles to other admins

---

## 🎯 What's Available to Test

### For Brands

- ✅ Create campaigns with targeting & budget
- ✅ Browse influencer packages by category
- ✅ Place orders and track status
- ✅ Leave reviews after completion
- ✅ Upload campaign assets and images
- ✅ Message influencers in-app

### For Influencers

- ✅ Create and price packages
- ✅ Accept orders from brands
- ✅ Upload deliverables
- ✅ View and respond to reviews
- ✅ Track payments and payouts
- ✅ Build portfolio with case studies

### For SuperAdmin (Supreme Leader)

- ✅ **Complete system control** - All 144 permissions
- ✅ Create new roles (Admin, Moderator, Manager, etc.)
- ✅ Assign permissions to roles
- ✅ Manage all user accounts
- ✅ Configure system-wide settings
- ✅ View complete analytics and reports
- ✅ Resolve critical disputes and issues
- ✅ Delegate authority to Admin users

### For Admins

- ✅ User management (create/edit/deactivate)
- ✅ Role assignment
- ✅ Campaign approval/moderation
- ✅ Order dispute resolution
- ✅ Analytics and reporting
- ✅ System configuration

### For Moderators

- ✅ Campaign review and approval
- ✅ User verification
- ✅ Complaint handling
- ✅ Content moderation
- ✅ Dispute mediation

---

## 📊 Pre-Loaded Test Data

### Database Size

- **37 users** (6 dedicated test accounts + 31 platform users)
- **14 brands** (2 test brands + 12 seeded)
- **14 influencers** (2 test influencers + 12 seeded)
- **72 campaigns** (various statuses: draft, published, completed)
- **96 packages** (multiple pricing tiers and platforms)
- **24 orders** (complete order lifecycle examples)
- **17 reviews** (bidirectional brand↔influencer feedback)

### Test Brands

1. **Urban Fashion Inc** (Fashion industry)
    - Email: testbrand1@system.local
    - Website: https://urban-fashion-inc.test
    - Status: Verified

2. **Wellness & Health Co** (Health industry)
    - Email: testbrand2@system.local
    - Website: https://wellness-health-co.test
    - Status: Verified

### Test Influencers

1. **Sarah Fashion** (@sarah_style)
    - Email: testinfluencer1@system.local
    - Niche: Fashion & Lifestyle
    - Status: Active

2. **Marcus Fitness** (@marcus_fit)
    - Email: testinfluencer2@system.local
    - Niche: Fitness & Wellness
    - Status: Active

---

## 🔧 Latest System Changes

### Bug Fixes Completed ✅

1. **User Type Field** - Changed from ENUM to VARCHAR
    - Allows role names: "test", "brand", "influencer", "moderator", "admin"
    - Fixes: SQLSTATE[01000] data truncation error

2. **Image Upload** - Fixed preview persistence on edit page
    - Images now display correctly when revisiting edit form
    - Alpine.js properly stores initial preview values

3. **UI Styling** - Enhanced information sidebar with colors
    - Gradient backgrounds (purple→indigo for create, cyan→blue for edit)
    - Color-coded status badges and emoji icons
    - Improved visual hierarchy

### Infrastructure Updates ✅

1. **Soft Deletes Implementation**
    - Added `deleted_at` columns to 12 core tables
    - Models updated with SoftDeletes trait
    - Enables audit trail and data recovery

2. **Comprehensive Seeding**
    - Created TestAccountsSeeder with proper role assignments
    - All existing platform seeders executed successfully
    - Realistic, minimal data without random values

3. **Role & Permission System**
    - 6 defined roles with 144 granular permissions
    - Proper role hierarchy (Admin > Moderator > Brand/Influencer)
    - User-role pivot relationships established

---

## 🔍 Key Features Overview

### Dashboard

- Admin: User analytics, system health, moderation queue
- Brand: Campaign performance, order tracking, earnings summary
- Influencer: Available offers, order status, payment history
- Moderator: Pending approvals, flagged content, dispute queue

### Campaign Management

- Create with title, description, budget, timeline
- Set target audience (followers, niches, countries)
- Upload assets (images, videos, documents)
- Track applications and approvals
- Manage influencer assignments

### Order System

- Brand selects package → Places order
- Influencer reviews request → Accepts or declines
- Order progresses: pending → accepted → in-progress → completed
- Payment processing and commission calculation
- Completion triggers review request

### Review & Ratings

- Brands review influencers (quality, responsiveness, professionalism)
- Influencers review brands (payment timeliness, clarity, fairness)
- 5-star rating system with written feedback
- Reviews visible on profiles

### Package Management

- Influencers list available packages
- Pricing and platform flexibility
- Deliverable specifications
- Timeline and revision limits

---

## 📋 Database Schema Highlights

### User Types

```
brand        → Brand company account
influencer   → Content creator account
moderator    → Platform moderator
admin        → System administrator
```

### Campaign Status

```
draft       → Being created, not published
published   → Active, accepting applications
completed   → Campaign finished
archived    → Retired campaign
```

### Order Status

```
pending     → Awaiting influencer response
accepted    → Influencer accepted
in-progress → Content being created
completed   → Delivered and approved
cancelled   → Order cancelled
```

### Soft Delete Tables

```
brands, influencers, packages, orders,
categories, reviews, testimonials,
case_studies, users, support_tickets,
payments, payouts
```

---

## 🎓 Common Testing Workflows

### Test 1: Create Campaign (Brand User)

1. Login: testbrand1@system.local / password
2. Navigate: Campaigns → New Campaign
3. Fill: Title, Description, Budget ($5000-$50000)
4. Set: Timeline (2-8 weeks), Audience targeting
5. Submit → Campaign created with "draft" status

### Test 2: Create Package (Influencer User)

1. Login: testinfluencer1@system.local / password
2. Navigate: My Packages → New Package
3. Fill: Name, Description, Price, Platforms
4. Specify: Deliverables, Timeline, Revisions
5. Submit → Package available for brands

### Test 3: Place Order (Brand User)

1. Login: testbrand1@system.local / password
2. Browse: Influencers → Influencer Packages
3. Select: Desired package and influencer
4. Review: Package details and price
5. Submit → Order created (status: pending)

### Test 4: Accept Order (Influencer User)

1. Login: testinfluencer1@system.local / password
2. Navigate: Orders → Review new order
3. Review: Brand requirements and timeline
4. Accept → Order status changes to "in-progress"

### Test 5: Complete & Review Order

1. Upload deliverables (images, videos, documents)
2. Brand approves → Status: completed
3. Leave review with rating and comment
4. Influencer replies with review

---

## 🔐 Security Features

### Role-Based Access Control

- Fine-grained permission system
- 144 unique permissions across 6 roles
- Route-level authorization checks
- User role verification on each request

### Data Protection

- Password hashing (bcrypt)
- Email verification for accounts
- Soft deletes preserve data history
- Audit trail for sensitive operations

### Account Verification

- All test accounts: email_verified_at set ✓
- No email confirmation needed for testing
- Ready for immediate login

---

## 📈 Monitoring & Logging

### Available Logs

- Application logs: `/storage/logs/`
- Database operations: Check for soft-deleted records
- User actions: Audit trail in each model
- Order history: Complete lifecycle tracking

### Check System Status

```bash
# Check database connection
php artisan db:seeding

# Verify soft deletes
php artisan tinker
>>> App\Models\Brand::withTrashed()->count()

# View user roles
>>> auth()->user()->roles()->get()
```

---

## 🆘 Troubleshooting

### If Test Accounts Don't Work

1. Verify migration ran: `php artisan migrate --list`
2. Re-seed accounts: `php artisan db:seed --class=TestAccountsSeeder`
3. Check user_type field is string: `SELECT * FROM users LIMIT 1\G`

### If Pages Shows "Unauthorized"

1. Verify user has assigned role: `php artisan tinker`
2. Check role permissions: `User::find(1)->roles()->get()`
3. Assign role: `$user->roles()->sync([$role->id])`

### If Images Not Displaying

1. Ensure storage linked: `php artisan storage:link`
2. Check file permissions: `chmod 755 storage/app/public`
3. Verify migration applied: Soft deletes shouldn't affect this

---

## 📚 Quick Reference Commands

```bash
# View all users
php artisan tinker
>>> User::all(['id','name','email','user_type']);

# Check user roles
>>> User::find(1)->roles()->get(['name']);

# Count orders by status
>>> Order::groupBy('status')->selectRaw('status, count(*) as count')->get();

# View soft-deleted records
>>> Brand::onlyTrashed()->get();

# Restore soft-deleted record
>>> Brand::withTrashed()->find(1)->restore();
```

---

## 🚀 Next Actions

1. **Login as SuperAdmin (The Supreme Leader)**
    - Email: superadmin@rockies.local
    - Password: password
    - URL: http://localhost:8000/dashboard
    - Authority: Complete system access, can create roles, assign permissions

2. **Explore SuperAdmin Capabilities**
    - View all users and their roles
    - Manage roles and permissions
    - Configure system settings
    - Create new roles (Admin, Moderator, Manager) if needed

3. **Switch to Admin Account**
    - Email: testadmin@system.local
    - Password: password
    - Manages system on behalf of SuperAdmin

4. **Test Brand Workflow**
    - Email: testbrand1@system.local
    - Create campaigns
    - Browse and order influencer packages

5. **Test Complete Workflow**
    - Place order (Brand)
    - Accept order (Influencer)
    - Upload deliverables
    - Complete and review

---

**Platform is ready for comprehensive testing and development! 🎉**

For detailed implementation notes, see: `PLATFORM_SETUP_COMPLETE.md`
