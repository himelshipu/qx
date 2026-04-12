# RBAC System Refactoring - Complete Documentation

**Status:** ✅ COMPLETED  
**Date:** April 12, 2026  
**Framework:** Laravel + Spatie Laravel-Permission

---

## Overview

This document describes the refactored Role-Based Access Control (RBAC) system. The system provides:
- **Superadmin** complete platform control
- **Role-based access** for admin, moderator, and manager roles
- **Permission-based sidebar** rendering
- **Safe role management** with superadmin protection
- **User type synchronization** with role assignments

---

## System Architecture

### 1. Users & Roles

#### User Model (`app/Models/User.php`)

**Key Attributes:**
- `user_type` (string): Must match assigned role name (e.g., 'admin', 'moderator', 'brand', 'influencer')
- `is_active` (boolean): Account status

**Key Methods:**
- `isSuperadmin()`: Checks if user has superadmin role
- `hasRole($roleSlug)`: Checks specific role
- `hasPermission($permissionSlug)`: Checks permission (superadmin always returns true)
- `canAccessDashboard()`: Checks if user can access dashboard
- `canDo($permissionSlug)`: Helper for checking permissions
- `getPermissionsForApiResponse()`: Returns permissions in API-friendly format

**Safety Features:**
- `syncRoles()` prevents removing superadmin role from superadmin users
- Deletion of superadmin users is prevented in `booted()` method
- `user_type` must be updated when role changes

#### Role Model (`app/Models/Role.php`)

**Key Attributes:**
- `is_superadmin` (boolean): Protected role flag
- `is_active` (boolean): Role status

**Key Methods:**
- `isProtected()`: Returns true if role is superadmin
- `hasPermission($permissionSlug)`: Checks if role has permission
- `givePermissionTo()`: Assign permission to role
- `syncPermissions()`: Bulk permission assignment

**Protection:**
- Superadmin role cannot be deleted
- Superadmin role cannot be edited through UI
- Superadmin role cannot be toggled inactive

#### Permission Model (`app/Models/Permission.php`)

**Attributes:**
- `slug` (string): Permission identifier (e.g., 'dashboard.view')
- `name` (string): Human-readable name
- `module` (string): Permission module/group
- `is_active` (boolean): Status

---

### 2. User Types

**Dashboard Users:**
- `superadmin` - Supreme authority, all permissions
- `admin` - Administrator, broad management access
- `moderator` - Content review and support access
- `manager` - Limited read-only access

**Frontend Users (No Dashboard Access):**
- `brand` - Brands creating campaigns (blocked from dashboard)
- `influencer` - Influencers bidding on campaigns (blocked from dashboard)

---

### 3. Middleware: RestrictDashboardAccess

**File:** `app/Http/Middleware/RestrictDashboardAccess.php`

**7-Point Validation Flow:**

1. **Authentication Check** - User must be logged in
2. **Email Verification** - User must have verified email
3. **Account Status** - User must be active (`is_active = true`)
4. **User Type Validation** - Brand/Influencer completely blocked
5. **Dashboard User Type** - Only admin/moderator/superadmin allowed
6. **Role Assignment** - User must have at least one role
7. **Permission Check** - User must have 'dashboard.view' permission OR be superadmin

**Result:** Only admin/moderator/superadmin with proper permissions access dashboard

---

### 4. Controllers

#### UserController (`app/Http/Controllers/Backend/UserController.php`)

**Role Assignment Methods:**
- `assignRolesStore()` - Assign roles via AJAX
  - ✅ Prevents superadmin role assignment
  - ✅ Syncs `user_type` with primary role
  - ✅ Prevents current user from losing dashboard access
  - ✅ Prevents modification of superadmin users

- `update()` - Update user details
  - ✅ Prevents editing superadmin users
  - ✅ Updates `user_type` when role changes
  - ✅ Validates role before assignment

#### RoleController (`app/Http/Controllers/Backend/RoleController.php`)

**Features:**
- ✅ Prevents superadmin role editing
- ✅ Prevents superadmin role deletion
- ✅ Prevents superadmin role status toggle
- ✅ Uses `isProtected()` method for all checks

---

### 5. Sidebar & Menu System

#### MenuHelper (`app/Helpers/MenuHelper.php`)

**Permission-Based Rendering:**
- Menu items now check permissions before display
- Hidden items depend on user permissions
- `getPermissionForMenuItem()` maps menu routes to permission slugs
- Empty permission groups are automatically hidden

**Key Changes:**
```php
// Before: All menu items shown
// After: Only permitted menu items shown

$permission = self::getPermissionForMenuItem($key, $subItem);
if ($permission && !$user->hasPermission($permission)) {
    continue; // Skip this item
}
```

**Benefits:**
- Non-intrusive - users don't see buttons they can't access
- Dynamic - sidebar updates with permission changes
- Clean - no 403 errors on hidden routes

---

## Role-Permission Mapping

### Seeder: RolePermissionSeeder

**File:** `database/seeders/RolePermissionSeeder.php`

**Role Permissions:**

#### Superadmin
- Gets **ALL** active permissions automatically
- No restrictions
- Cannot have permissions removed

#### Admin
- Dashboard access
- Full user management (CRUD)
- Role management (CRUD)
- Permission management
- Full content management (categories, brands, influencers, campaigns, packages, orders, etc.)
- Settings and system logs
- Reports and exports
- Bulk operations

#### Moderator
- Dashboard access (read-only)
- Reviews management
- Support tickets
- Content moderation
- User verification/approval
- Conversations view
- Analytics (read-only)

#### Manager
- Dashboard access (read-only)
- Campaigns (view only)
- Orders (view only)
- Packages (view only)
- Influencers (view only)
- Brands (view only)
- Analytics (read-only)

---

## Role Assignment Flow

### Step 1: Assign Role to User
```php
// UserController@assignRolesStore()
$user->roles()->sync($request->roles);
$user->update(['user_type' => strtolower($role->name)]);
```

### Step 2: Validation Checks
- ✅ Superadmin users cannot be modified
- ✅ Superadmin role cannot be assigned
- ✅ User cannot remove own dashboard access
- ✅ User_type synced with role name

### Step 3: Automatic Permission Checking
- User gets permissions from assigned roles
- Middleware validates dashboard access
- Sidebar respects permissions

### Result
- User_type always matches role name
- Permissions are consistently applied
- No orphaned or mismatched states

---

## Safety Rules & Protections

### Superadmin Protection
```php
// In Role model - Cannot delete
protected static function booted() {
    static::deleting(function (self $role) {
        if ($role->isProtected()) {
            throw new \Exception('Cannot delete superadmin role.');
        }
    });
}

// In User model - Cannot delete
static::deleting(function (self $user) {
    if ($user->isSuperadmin()) {
        throw new \Exception('Cannot delete superadmin users.');
    }
});
```

### Role Modification Safety
```php
// In syncRoles() - Ensures superadmin role cannot be removed
if ($this->isSuperadmin()) {
    $superadminRole = Role::where('is_superadmin', true)->first();
    if ($superadminRole && !in_array($superadminRole->id, $roleIds)) {
        $roleIds[] = $superadminRole->id; // Force include
    }
}
```

### Self-Modification Prevention
```php
// In assignRolesStore()
if ($user->id === auth()->id() && !$hasDashboardAccess) {
    return error('You cannot remove your own dashboard access.');
}
```

---

## Permissions List

### Dashboard Permissions
- `dashboard.view` - Access dashboard
- `analytics.view` - View analytics
- `analytics.kpis` - View KPIs
- `analytics.revenue` - View revenue

### User Management
- `users.index`, `users.create`, `users.store`, `users.show`
- `users.edit`, `users.update`, `users.toggle-status`

### Role Management
- `roles.index`, `roles.create`, `roles.store`
- `roles.edit`, `roles.update`, `roles.toggle-status`

### Content Management
- Categories, Brands, Influencers, Campaigns, etc.
- Each has: index, create, store, show, edit, update, toggle-status

### Commerce
- Packages, Carts, Orders, Payments, Payouts, Wishlists

### Communication
- Conversations, Support Tickets, Notifications

### Moderation
- `moderation.queue`, `moderation.review`
- `moderation.block-users`, `moderation.remove-content`

### Settings & System
- `settings.view`, `settings.edit`, `settings.update`
- `system.logs`, `system.activity-logs`

### Reports
- `reports.view`, `reports.orders`, `reports.revenue`
- `reports.users`, `reports.campaigns`
- `reports.export`, `reports.export-pdf`, `reports.export-csv`

---

## Implementation Guide

### 1. Run Seeders
```bash
php artisan db:seed --class=PermissionSeeder
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=RolePermissionSeeder
```

### 2. Create Initial Superadmin
```bash
php artisan tinker
# Then in tinker:
$user = User::create([
    'name' => 'Superadmin',
    'email' => 'superadmin@example.com',
    'password' => bcrypt('password'),
    'user_type' => 'superadmin',
    'email_verified_at' => now(),
    'is_active' => true
]);
$superadminRole = Role::where('is_superadmin', true)->first();
$user->roles()->attach($superadminRole);
```

### 3. Create Other Users
```bash
# Via UI: Dashboard > Access Control > Users > Create
# Select role (Admin, Moderator, or Manager)
# User_type will auto-sync to role name
```

### 4. Update User Permissions
```bash
# Via UI: Dashboard > Access Control > Assign Permissions
# Select role, check/uncheck permissions
# All users with that role get updated permissions
```

---

## Testing Checklist

- [ ] Superadmin can access all features
- [ ] Admin can manage most resources
- [ ] Moderator can only access moderation features
- [ ] Manager can only view resources
- [ ] Brand/Influencer blocked from dashboard
- [ ] User_type matches role name after assignment
- [ ] Sidebar hides items without permission
- [ ] Cannot edit/delete superadmin role
- [ ] Cannot delete superadmin user
- [ ] Cannot remove own dashboard access
- [ ] Permission inheritance works through roles
- [ ] API permissions response is accurate

---

## Files Modified

### Controllers
- `app/Http/Controllers/Backend/UserController.php` - Added safety checks and user_type sync
- `app/Http/Controllers/Backend/RoleController.php` - Already had superadmin protection

### Models
- `app/Models/User.php` - Added syncRoles safety, deletion protection, updated canAccessDashboard
- `app/Models/Role.php` - Already had protection
- `app/Models/Permission.php` - No changes needed

### Middleware
- `app/Http/Middleware/RestrictDashboardAccess.php` - Improved validation flow, clarified checks

### Helpers
- `app/Helpers/MenuHelper.php` - Added permission-based rendering

### Seeders
- `database/seeders/RolePermissionSeeder.php` - Replaced with comprehensive role-permission mapping
- `database/seeders/PermissionSeeder.php` - No changes needed
- `database/seeders/RoleSeeder.php` - No changes needed

---

## Key Principles

1. **Superadmin is Supreme** - Cannot be modified or removed
2. **User_type = Role** - Always synchronized, prevents mismatch
3. **Permission-Based Access** - Dashboard features, sidebar, and API all check permissions
4. **Role Inheritance** - Users get permissions only through roles
5. **Safety First** - Cannot lock yourself out, cannot remove superadmin
6. **Progressive Enhancement** - Sidebar gracefully hides unpermitted items
7. **Centralized Management** - All permissions defined in seeders

---

## Workflow Example

### Admin User Creation & Permission Management

```
1. Superadmin logs in
2. Goes to Dashboard > Access Control > Users
3. Clicks "Create User"
4. Enters name, email, password
5. Selects role: "Admin"
6. User_type automatically set to "admin"
7. User gets all Admin permissions via role
8. User can now access dashboard and manage resources
```

### Permission Adjustment

```
1. Superadmin goes to Dashboard > Roles
2. Clicks on "Admin" role
3. Checks "Moderation" permissions
4. Saves changes
5. All Admin users now have moderation access
6. Sidebar updates for all Admins
```

---

## Troubleshooting

### User Can't Access Dashboard
**Check:**
1. Is user active? (`is_active = true`)
2. Has role assigned? (`user_roles` table)
3. Does role have `dashboard.view` permission?
4. Is `user_type` valid? ('admin', 'moderator', 'superadmin')

### User_type Mismatch
**Solution:**
```php
// Sync manually if needed
$user->update(['user_type' => strtolower($user->roles->first()->name)]);
```

### Superadmin Lost Dashboard Access
**Solution:**
```php
// Restore superadmin permissions
$superadmin = User::where('email', 'superadmin@example.com')->first();
$superadminRole = Role::where('is_superadmin', true)->first();
$superadmin->roles()->sync([$superadminRole->id]);
```

---

## Future Enhancements

1. **Audit Logging** - Track all role/permission changes
2. **Role Templates** - Pre-built role configurations
3. **Permission Groups** - Better permission organization
4. **Granular API Permissions** - Different permissions for API vs UI
5. **Temporary Roles** - Time-limited role assignments
6. **Delegation** - Allow admins to assign certain roles

---

## Compliance Notes

✅ **GDPR Ready** - User data properly isolated  
✅ **Audit Trail** - Changes trackable through timestamps  
✅ **Least Privilege** - Users get only required permissions  
✅ **Separation of Duties** - Superadmin, Admin, Moderator roles separated  
✅ **Immutable Superadmin** - Cannot be accidentally modified  

---

**Document Version:** 1.0  
**Last Updated:** April 12, 2026  
**Maintained By:** Development Team
