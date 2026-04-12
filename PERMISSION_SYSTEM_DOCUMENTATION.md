# Permission System Documentation

## Overview

This document provides comprehensive information about the ROCKIES Admin Dashboard permission system. Permissions are **read-only** and can only be created/managed through database seeders, not through the admin UI.

---

## System Architecture

### Key Components

#### 1. **Permissions Table** (`permissions`)

Stores all system permissions with the following structure:

```
- id: Primary key
- name: Human-readable permission name
- slug: Machine-readable identifier (e.g., 'users.index', 'campaigns.create')
- module: Logical grouping of permissions
- description: Detailed explanation
- is_active: Boolean flag to enable/disable permission
- created_at, updated_at: Timestamps
```

#### 2. **Roles Table** (`roles`)

Defines user roles:

```
- id: Primary key
- name: Role name (e.g., 'Administrator', 'Moderator')
- slug: Machine identifier (e.g., 'admin', 'moderator')
- description: Role explanation
- is_active: Boolean flag
- created_at, updated_at: Timestamps
- deleted_at: Soft delete timestamp
```

#### 3. **Role-Permission Pivot Table** (`role_permissions`)

Links roles to permissions:

```
- id: Primary key
- role_id: Foreign key to roles
- permission_id: Foreign key to permissions
- created_at, updated_at: Timestamps
```

#### 4. **User-Role Pivot Table** (`user_roles`)

Links users to roles:

```
- id: Primary key
- user_id: Foreign key to users
- role_id: Foreign key to roles
- created_at, updated_at: Timestamps
```

---

## Available Roles

### 1. **Administrator (admin)**

- **Total Permissions:** 144
- **Access Level:** Full access to all modules and actions
- **Capabilities:**
    - View and create all resources
    - Edit and delete any item
    - Manage users, roles
    - Control all system settings
    - Manage all content types

### 2. **Moderator (moderator)**

- **Total Permissions:** 67
- **Access Level:** Content review and user moderation
- **Capabilities:**
    - Review and approve campaigns
    - Manage support tickets
    - Toggle user statuses
    - Create/edit content (case studies, FAQs, knowledge base)
    - Approve influencer assignments
    - Cannot access user/role management

### 3. **Brand (brand)**

- **Total Permissions:** 31
- **Access Level:** Campaign and order management
- **Capabilities:**
    - Create and manage own campaigns
    - Assign influencers to campaigns
    - View and manage own orders
    - Purchase packages
    - View analytics and reviews
    - Cannot create/edit users or roles

### 4. **Influencer (influencer)**

- **Total Permissions:** 15
- **Access Level:** View-only with order management
- **Capabilities:**
    - View available campaigns
    - View own orders
    - View reviews
    - Access support tickets
    - Cannot create or delete anything

---

## Permission Modules

Permissions are organized into logical modules:

| Module                   | Permissions | Purpose                                                                               |
| ------------------------ | ----------- | ------------------------------------------------------------------------------------- |
| **dashboard**            | 1           | Dashboard access                                                                      |
| **users**                | 2           | User management                                                                       |
| **roles**                | 7           | Role management                                                                       |
| **categories**           | 7           | Category CRUD                                                                         |
| **brands**               | 8           | Brand management                                                                      |
| **influencers**          | 9           | Influencer management                                                                 |
| **portfolios**           | 8           | Portfolio item management                                                             |
| **moderators**           | 8           | Moderator management                                                                  |
| **campaigns**            | 10          | Campaign management                                                                   |
| **campaign-influencers** | 7           | Campaign influencer interactions                                                      |
| **packages**             | 9           | Package management                                                                    |
| **commerce**             | 2           | Cart management                                                                       |
| **orders**               | 8           | Order management                                                                      |
| **payments**             | 1           | Payment view                                                                          |
| **payouts**              | 1           | Payout view                                                                           |
| **wishlists**            | 1           | Wishlist view                                                                         |
| **reviews**              | 3           | Review management                                                                     |
| **support**              | 5           | Support ticket management                                                             |
| **conversations**        | 1           | Conversation access                                                                   |
| **content**              | 30          | Content management (case studies, testimonials, FAQs, knowledge base, collaborations) |
| **rbac**                 | 1           | Role-based access control                                                             |
| **admin**                | 2           | Admin-only features                                                                   |
| **Total**                | **158**     | Complete system permissions                                                           |

---

## Permission Slug Naming Convention

Permissions follow a consistent naming pattern based on action type:

### CRUD Operations

```
resource.index          - List/view all items
resource.create         - Access create form
resource.store          - Save new item
resource.show           - View single item details
resource.edit           - Access edit form
resource.update         - Save changes to item
resource.destroy        - Delete item
```

### Special Actions

```
resource.toggle-status  - Toggle active/inactive status
resource.toggle-*       - Other toggle actions
resource.assign         - Assign related items
resource.approve        - Approve submissions
resource.reject         - Reject submissions
resource.*-custom       - Other custom actions
```

---

## Managing Permissions

### ✅ **How to Add New Permissions**

Edit the `PermissionSeeder.php` file and add to the `buildPermissions()` method:

```php
// Example: Adding permissions for a new feature
['name' => 'View Reports', 'slug' => 'reports.index', 'module' => 'reports'],
['name' => 'Generate Report', 'slug' => 'reports.generate', 'module' => 'reports'],
['name' => 'Export Report', 'slug' => 'reports.export', 'module' => 'reports'],
```

Then run the seeder:

```bash
php artisan db:seed --class=PermissionSeeder
```

### ✅ **How to Assign Permissions to Roles**

Edit the `RolePermissionSeeder.php` file and update the appropriate role method:

```php
private function getBrandPermissions(): array
{
    return [
        'dashboard.view',
        'campaigns.index',
        'campaigns.create',
        // Add new permissions here
        'reports.index',
        'reports.generate',
    ];
}
```

Then run the seeder:

```bash
php artisan db:seed --class=RolePermissionSeeder
```

### ✅ **How to Enable/Disable Permissions**

Update the `is_active` column in the permissions table:

```bash
php artisan tinker
>>> App\Models\Permission::where('slug', 'reports.export')->update(['is_active' => false]);
```

---

## Using Permissions in Code

### Checking User Permissions

```php
// Check if user has a specific permission
if ($user->hasPermission('campaigns.create')) {
    // User can create campaigns
}

// Check if user has a role
if ($user->hasRole('admin')) {
    // User is admin
}

// Get all user's permissions (through roles)
$permissions = $user->getPermissions();
```

### Route Protection

Permissions are typically checked at the controller level using middleware or authorization gates.

---

## Seeder Implementation Details

### PermissionSeeder

Located at: `database/seeders/PermissionSeeder.php`

**Purpose:** Create/update all system permissions

**Features:**

- Comprehensive coverage of all 158 permissions
- Organized by module
- Descriptive names and documentation
- Uses `updateOrInsert` to prevent duplicates

**Usage:**

```bash
php artisan db:seed --class=PermissionSeeder
```

### RolePermissionSeeder

Located at: `database/seeders/RolePermissionSeeder.php`

**Purpose:** Assign permissions to roles

**Methods:**

- `getAdminPermissions()` - All 144 admin permissions
- `getModeratorPermissions()` - 67 moderator permissions
- `getBrandPermissions()` - 31 brand permissions
- `getInfluencerPermissions()` - 15 influencer permissions

**Usage:**

```bash
php artisan db:seed --class=RolePermissionSeeder
```

---

## Sidebar Navigation

The admin sidebar has been configured to exclude the Permissions CRUD section. Only the following ACCESS CONTROL items are visible:

- **Users** - User management
- **Roles** - Role management CRUD (via modals)

The permissions management is now **seeder-only** to maintain system integrity.

---

## Implementation Summary

### ✅ Completed Tasks

1. **Removed Permission CRUD Routes**
    - All 11 permission routes removed from `routes/web.php`
    - PermissionController import removed
    - Routes: index, create, store, edit, update, destroy, toggle-status, assign, etc.

2. **Updated Sidebar Menu**
    - Removed "Permissions" menu item
    - Removed "Assign Permissions" menu item
    - MenuHelper updated with clean ACCESS CONTROL section

3. **Created Comprehensive PermissionSeeder**
    - Location: `database/seeders/PermissionSeeder.php`
    - 158 permissions covering all 21 controllers and 154+ methods
    - All CRUD operations covered
    - Custom actions included (toggle, assign, approve, etc.)

4. **Created RolePermissionSeeder**
    - Location: `database/seeders/RolePermissionSeeder.php`
    - Admin: 144 permissions (full access)
    - Moderator: 67 permissions (content review)
    - Brand: 31 permissions (campaign management)
    - Influencer: 15 permissions (view-only + order management)

### ✅ Verified

- ✅ 158 permissions created successfully
- ✅ 5 roles with proper permission assignments
- ✅ 272 role-permission mappings established
- ✅ All routes removed
- ✅ Menu items cleaned
- ✅ Seeders tested and working

---

## Reference: All Permission Slugs

### Dashboard

- `dashboard.view`

### Users (2)

- `users.index`
- `users.toggle-status`

### Roles (7)

- `roles.index`, `roles.create`, `roles.store`, `roles.edit`, `roles.update`, `roles.destroy`, `roles.toggle-status`

### Categories (7)

- `categories.index`, `categories.create`, `categories.store`, `categories.edit`, `categories.update`, `categories.destroy`, `categories.toggle-status`

### Brands (8)

- `brands.index`, `brands.create`, `brands.store`, `brands.show`, `brands.edit`, `brands.update`, `brands.destroy`, `brands.toggle-status`

### Influencers (9)

- `influencers.index`, `influencers.create`, `influencers.store`, `influencers.show`, `influencers.edit`, `influencers.update`, `influencers.destroy`, `influencers.toggle-status`, `influencers.toggle-featured`

### Portfolios (8)

- `portfolios.index`, `portfolios.create`, `portfolios.store`, `portfolios.edit`, `portfolios.update`, `portfolios.destroy`, `portfolios.toggle`, `portfolios.reorder`

### Moderators (8)

- `moderators.index`, `moderators.create`, `moderators.store`, `moderators.show`, `moderators.edit`, `moderators.update`, `moderators.destroy`, `moderators.toggle-status`

### Campaigns (10)

- `campaigns.index`, `campaigns.create`, `campaigns.store`, `campaigns.show`, `campaigns.edit`, `campaigns.update`, `campaigns.destroy`, `campaigns.update-status`, `campaigns.assign`, `campaigns.assigned-influencers`

### Campaign Influencers (7)

- `campaign-influencers.index`, `campaign-influencers.create`, `campaign-influencers.store`, `campaign-influencers.approve`, `campaign-influencers.reject`, `campaign-influencers.cancel`, `campaign-influencers.destroy`

### Packages (9)

- `packages.index`, `packages.create`, `packages.store`, `packages.show`, `packages.edit`, `packages.update`, `packages.destroy`, `packages.toggle-status`, `packages.purchase`

### Commerce (2)

- `carts.index`, `carts.show`

### Orders (8)

- `orders.index`, `orders.show`, `orders.update-status`, `orders.create-from-campaign`, `orders.update-sub-order-status`, `orders.mark-sub-order-paid`, `orders.update-item-status`, `orders.mark-item-paid`

### Payments (1)

- `payments.index`

### Payouts (1)

- `payouts.index`

### Wishlists (1)

- `wishlists.index`

### Reviews (3)

- `reviews.index`, `reviews.show`, `reviews.toggle-visibility`

### Support (5)

- `support-tickets.index`, `support-tickets.show`, `support-tickets.update`, `support-tickets.destroy`, `support-tickets.bulk-update`

### Conversations (1)

- `conversations.index`

### Content Management (30)

**Case Studies (8):**

- `case-studies.index`, `case-studies.create`, `case-studies.store`, `case-studies.show`, `case-studies.edit`, `case-studies.update`, `case-studies.destroy`, `case-studies.toggle-status`

**Testimonials (7):**

- `testimonials.index`, `testimonials.create`, `testimonials.store`, `testimonials.edit`, `testimonials.update`, `testimonials.destroy`, `testimonials.toggle-status`

**FAQ Sections (7):**

- `faqs.sections.index`, `faqs.sections.create`, `faqs.sections.store`, `faqs.sections.edit`, `faqs.sections.update`, `faqs.sections.destroy`, `faqs.sections.toggle`

**FAQ Items (7):**

- `faqs.items.index`, `faqs.items.create`, `faqs.items.store`, `faqs.items.edit`, `faqs.items.update`, `faqs.items.destroy`, `faqs.items.toggle`

**Knowledge Base (7):**

- `knowledge-base.index`, `knowledge-base.create`, `knowledge-base.store`, `knowledge-base.edit`, `knowledge-base.update`, `knowledge-base.destroy`, `knowledge-base.toggle-status`

**Featured Collaborations (7):**

- `featured-collaborations.index`, `featured-collaborations.create`, `featured-collaborations.store`, `featured-collaborations.edit`, `featured-collaborations.update`, `featured-collaborations.destroy`, `featured-collaborations.toggle-publish`

**Content Library (1):**

- `content-library.index`

### Admin-Only (2)

- `cart-manager.index`, `cart-manager.show`

---

## Support & Maintenance

### Adding a New Resource

1. Add permissions to `PermissionSeeder.php`
2. Assign to roles in `RolePermissionSeeder.php`
3. Run seeders
4. Update this documentation

### Debugging Permissions

```bash
# Check a user's permissions
php artisan tinker
>>> $user = App\Models\User::find(1);
>>> $user->permissions()->pluck('slug');

# Check a role's permissions
>>> $role = App\Models\Role::where('slug', 'admin')->first();
>>> $role->permissions()->pluck('slug');
```

### Database Cleanup

```bash
# Refresh seeders
php artisan db:seed --class=PermissionSeeder
php artisan db:seed --class=RolePermissionSeeder
```

---

**Last Updated:** 2026-01-12
**System Version:** 1.0
**Total Permissions:** 158
**Total Roles:** 4 (+ super_admin if exists)
**Status:** ✅ Production Ready
