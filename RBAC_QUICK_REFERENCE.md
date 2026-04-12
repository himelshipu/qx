# RBAC System - Quick Reference Guide

## 🎯 At a Glance

| Component | Purpose | Key File |
|-----------|---------|----------|
| **Roles** | Categorize users into groups | `Role.php` |
| **Permissions** | Define what actions users can do | `Permission.php` |
| **User_type** | Determines dashboard eligibility | `users.user_type` column |
| **Middleware** | Validates dashboard access | `RestrictDashboardAccess.php` |
| **Sidebar** | Shows only accessible menu items | `MenuHelper.php` |

---

## 👥 User Types

### Dashboard Access (Can Login)
- **`superadmin`** - Full control, all permissions
- **`admin`** - Management access, broad permissions
- **`moderator`** - Review & support, limited permissions
- **`manager`** - Read-only, minimal permissions

### No Dashboard Access (Frontend Only)
- **`brand`** - BLOCKED from dashboard
- **`influencer`** - BLOCKED from dashboard

---

## 🔐 The Golden Rules

### Rule 1: User_type = Role Name
```php
// Always true:
$user->user_type === strtolower($user->roles->first()->name)

// Example:
// If user has 'Admin' role → user_type must be 'admin'
// If user has 'Moderator' role → user_type must be 'moderator'
```

### Rule 2: Superadmin is Immutable
```php
// Cannot:
$superadmin->delete(); // Throws exception
$superadmin->roles()->sync([]); // Restores automatically
Role::where('is_superadmin', true)->delete(); // Throws exception
```

### Rule 3: Permissions Flow Through Roles
```php
// Users DON'T have direct permissions (usually)
// Instead: User → Role → Permissions
User → admin role → 'dashboard.view' permission ✅
User → (no permission) → 'dashboard.view' permission ❌
```

### Rule 4: Brand/Influencer = No Dashboard
```php
// These are BLOCKED at middleware level:
if ($user->user_type === 'brand' || $user->user_type === 'influencer') {
    redirect to home
}
```

---

## 📌 Permission Naming Convention

```
{resource}.{action}

Examples:
dashboard.view
users.create
users.edit
users.delete
campaigns.index
orders.update
```

---

## 🛠️ Common Tasks

### Create Admin User
```php
$user = User::create([
    'name' => 'John Admin',
    'email' => 'john@example.com',
    'password' => bcrypt('password'),
    'user_type' => 'admin',      // ← Important!
    'email_verified_at' => now(),
    'is_active' => true
]);

$adminRole = Role::where('slug', 'admin')->first();
$user->roles()->attach($adminRole);
```

### Check if User Has Permission
```php
// Method 1: Direct
$user->hasPermission('users.create'); // true/false

// Method 2: Helper
$user->canDo('users.create'); // true/false

// Method 3: Multiple (ANY)
$user->canDoAny(['users.create', 'users.edit']); // true if has ANY

// Method 4: Multiple (ALL)
$user->canDoAll(['users.create', 'users.edit']); // true if has ALL
```

### Check if User Has Role
```php
$user->hasRole('admin'); // true/false
$user->isSuperadmin(); // true/false
```

### Update User's Role
```php
// Single role
$user->roles()->sync([Role::where('slug', 'admin')->first()->id]);
$user->update(['user_type' => 'admin']);

// Multiple roles (if needed)
$user->roles()->sync([
    Role::where('slug', 'admin')->first()->id,
    Role::where('slug', 'moderator')->first()->id
]);
$user->update(['user_type' => 'admin']); // Use primary role
```

### Assign Permission to Role
```php
$adminRole = Role::where('slug', 'admin')->first();
$permission = Permission::where('slug', 'users.create')->first();

$adminRole->givePermissionTo($permission);
```

### Remove Permission from Role
```php
$adminRole = Role::where('slug', 'admin')->first();
$permission = Permission::where('slug', 'users.create')->first();

$adminRole->revokePermissionTo($permission);
```

### Get All User Permissions
```php
$permissions = $user->getAllPermissions(); // Collection

// Or get permissions for API response
$apiResponse = $user->getPermissionsForApiResponse();
// Returns: {
//   accessible_modules: [...],
//   key_permissions: {...},
//   user_type: "admin",
//   is_superadmin: false
// }
```

---

## 🔍 Middleware Flow (Dashboard Access)

```
Request to /dashboard
    ↓
[1] Authenticated? → NO → Redirect /login
                  → YES ↓
[2] Email Verified? → NO → Redirect /verify-email
                   → YES ↓
[3] Account Active? → NO → Redirect / (Account deactivated)
                   → YES ↓
[4] Is Brand/Influencer? → YES → Redirect / (Blocked)
                       → NO ↓
[5] Valid User Type? → NO → Redirect / (Invalid type)
                   → YES ↓
[6] Has Role? → NO → Redirect / (No role assigned)
             → YES ↓
[7] Has dashboard.view? → NO → Redirect / (No permission)
                      → YES ↓
                    PASS → Continue
```

---

## 🎨 Sidebar Rendering Flow

```
Sidebar Build
    ↓
For each menu item:
    ├─ Get required permission
    ├─ Check user.hasPermission()?
    ├─ YES → Include in menu ✓
    └─ NO → Skip (hide) ✗
    ↓
Render only authorized items
```

**Example:**
```php
// User has 'users.index' permission
// → "Users" menu appears ✓

// User lacks 'users.create' permission
// → "Create User" submenu hidden ✗
```

---

## 📊 Permission Levels

### Level 1: View/Index
```php
'users.index'           // Can see users list
'campaigns.show'        // Can view campaign details
'orders.index'          // Can see orders list
```

### Level 2: Create/Edit
```php
'users.create'          // Can create user
'campaigns.update'      // Can edit campaign
'orders.store'          // Can create order
```

### Level 3: Delete
```php
'users.destroy'         // Can delete user
'campaigns.destroy'     // Can delete campaign
```

### Level 4: Special Actions
```php
'users.toggle-status'   // Can toggle user active/inactive
'campaigns.assign'      // Can assign influencers to campaign
'orders.update-status'  // Can change order status
```

---

## 🚨 Safety Features

### Prevent Superadmin Deletion
```php
// In User model booted method:
static::deleting(function (self $user) {
    if ($user->isSuperadmin()) {
        throw new \Exception('Cannot delete superadmin users.');
    }
});
```

### Prevent Superadmin Role Removal
```php
// In User.syncRoles():
if ($this->isSuperadmin()) {
    // Ensure superadmin role always remains
    if (!in_array($superadminRole->id, $roleIds)) {
        $roleIds[] = $superadminRole->id;
    }
}
```

### Prevent Self-Lockout
```php
// In UserController.assignRolesStore():
if ($user->id === auth()->id() && !$hasDashboardAccess) {
    return error('Cannot remove your own dashboard access');
}
```

---

## 🔧 Debugging

### Check Role Permissions
```php
$role = Role::where('slug', 'admin')->first();
$role->permissions->pluck('slug')->toArray();
// Output: ['dashboard.view', 'users.create', ...]
```

### Check User Permissions
```php
$user = User::find(1);
$user->getAllPermissions()->pluck('slug')->toArray();
// Output: ['dashboard.view', 'users.create', ...]
```

### Check User Has Permission
```php
$user = User::find(1);
$user->hasPermission('users.create'); // true/false
```

### Check User Roles
```php
$user = User::find(1);
$user->roles->pluck('name')->toArray();
// Output: ['Admin', 'Moderator']
```

### Check User_type Consistency
```php
$users = User::all();
$users->each(function($user) {
    $role = strtolower($user->roles->first()->name ?? '');
    if ($user->user_type !== $role) {
        echo "MISMATCH: {$user->email} has type '{$user->user_type}' but role '{$role}'";
    }
});
```

---

## 📈 Role Hierarchy

```
Superadmin
    ↓ More Permissions
    Admin
    ↓ More Permissions
    Moderator
    ↓ More Permissions
    Manager
    ↓ No Dashboard
    Brand/Influencer
```

---

## 📱 API Response Example

```json
{
  "user": {
    "id": 1,
    "name": "John Admin",
    "email": "john@example.com",
    "user_type": "admin",
    "permissions": {
      "accessible_modules": ["dashboard", "users", "campaigns"],
      "key_permissions": {
        "can_view_dashboard": true,
        "can_manage_users": true,
        "can_manage_roles": true,
        "can_manage_content": true
      },
      "is_superadmin": false
    }
  }
}
```

---

## 🎓 Learning Path

1. **Start Here:** Understand Role vs Permission vs User_type
2. **Then Read:** RBAC_SYSTEM_REFACTORING_COMPLETE.md (full docs)
3. **Practice:** Create test users with different roles
4. **Test:** Verify sidebar and dashboard access
5. **Debug:** Use commands above to troubleshoot

---

## ⚡ Quick Commands

```bash
# Start tinker
php artisan tinker

# Inside tinker:

# Get all roles
Role::all()->pluck('slug', 'name');

# Get all permissions
Permission::all()->pluck('slug', 'name');

# Create user with role
$user = User::create(['name' => 'Test', 'email' => 'test@test.com', 'password' => bcrypt('pwd'), 'user_type' => 'admin']);
$user->roles()->attach(Role::where('slug', 'admin')->first());

# Check user permission
User::find(1)->hasPermission('users.create');

# Get user's roles
User::find(1)->roles->pluck('name');

# Add permission to role
Role::find(1)->permissions()->attach(Permission::where('slug', 'users.create')->first());
```

---

## 📞 When Something Goes Wrong

| Symptom | Cause | Fix |
|---------|-------|-----|
| User can't access dashboard | Missing permission or role | `$user->roles()->attach($role)` |
| User_type wrong | Manual update failed | `$user->update(['user_type' => 'admin'])` |
| Sidebar showing all items | Permission filtering missing | Check MenuHelper buildSidebarMenu() |
| Can't delete superadmin | Protection in place | This is intentional - don't bypass |
| Brand can access dashboard | Middleware not applied | Check route protection |

---

**Version:** 1.0  
**Last Updated:** April 12, 2026  
**Questions?** See RBAC_SYSTEM_REFACTORING_COMPLETE.md
