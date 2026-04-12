# RBAC System Refactoring - Implementation Steps

## ✅ COMPLETED CHANGES

### 1. **UserController** (`app/Http/Controllers/Backend/UserController.php`)
```
✅ assignRolesStore() - Now syncs user_type with role
✅ update() - Now syncs user_type with role on role change
✅ Both methods prevent superadmin modification
✅ Both methods prevent current user from losing dashboard access
```

### 2. **RestrictDashboardAccess Middleware** (`app/Http/Middleware/RestrictDashboardAccess.php`)
```
✅ Clarified 7-point validation flow
✅ Ensures brand/influencer completely blocked
✅ Allows admin/moderator/superadmin with dashboard.view permission
✅ Better error messages for each check
```

### 3. **MenuHelper** (`app/Helpers/MenuHelper.php`)
```
✅ Added permission-based sidebar rendering
✅ Menu items now check permissions before display
✅ Added getPermissionForMenuItem() helper
✅ Empty permission groups are hidden
```

### 4. **User Model** (`app/Models/User.php`)
```
✅ Added syncRoles() safety - prevents removing superadmin role
✅ Added deletion protection for superadmin users
✅ Updated canAccessDashboard() to include superadmin
```

### 5. **RolePermissionSeeder** (`database/seeders/RolePermissionSeeder.php`)
```
✅ Superadmin gets all permissions
✅ Admin gets broad management access
✅ Moderator gets review/support access
✅ Manager gets read-only access
✅ Roles auto-created if missing
```

---

## 🚀 NEXT STEPS (DO THESE)

### Step 1: Run Database Seeders
```bash
# Clear existing permissions if corrupted
php artisan db:seed --class=PermissionSeeder

# Seed base roles (superadmin only)
php artisan db:seed --class=RoleSeeder

# Assign permissions to roles and create helper roles
php artisan db:seed --class=RolePermissionSeeder
```

### Step 2: Create Initial Superadmin User
```bash
php artisan tinker
```

Then in tinker:
```php
$user = User::create([
    'name' => 'Superadmin',
    'email' => 'superadmin@rockies.com',
    'password' => bcrypt('your_password_here'),
    'user_type' => 'superadmin',
    'email_verified_at' => now(),
    'is_active' => true
]);

$superadminRole = Role::where('is_superadmin', true)->first();
$user->roles()->attach($superadminRole);

echo "Superadmin created: {$user->email}";
```

### Step 3: Verify Existing Superadmin Users
```bash
php artisan tinker
```

```php
// Check if any user is already superadmin
$superadmins = User::whereHas('roles', function($q) {
    $q->where('is_superadmin', true);
})->get();

$superadmins->each(function($user) {
    echo "{$user->id} - {$user->name} ({$user->email}) - user_type: {$user->user_type}\n";
});
```

### Step 4: Sync User Types for Existing Users
```bash
php artisan tinker
```

```php
// For each user, sync user_type with their primary role
User::all()->each(function($user) {
    if ($user->roles->count() > 0) {
        $roleName = strtolower($user->roles->first()->name);
        if ($user->user_type !== $roleName) {
            $user->update(['user_type' => $roleName]);
            echo "Updated {$user->email}: user_type = {$roleName}\n";
        }
    }
});
```

### Step 5: Test Dashboard Access
1. Login as superadmin
2. Access `/dashboard` - should work
3. Go to Dashboard > Access Control > Users
4. Create a test "Admin" user
5. Check user_type is "admin"
6. Logout and login as admin
7. Should have access to admin features
8. Check sidebar only shows permitted items

### Step 6: Test Brand/Influencer Block
1. Create/edit a user with user_type: "brand"
2. Try to access `/dashboard`
3. Should redirect with error: "You do not have permission to access the dashboard."

### Step 7: Test Permission Updates
1. Go to Dashboard > Roles (if implemented)
2. Edit "Moderator" role
3. Add or remove a permission
4. Logout and login as moderator
5. Sidebar should update
6. Menu items should appear/disappear based on permission

---

## 📋 VERIFICATION CHECKLIST

Run these checks to verify everything works:

```php
// In tinker, run these commands:

// 1. Check superadmin has all permissions
$superadmin = User::where('email', 'superadmin@rockies.com')->first();
echo "Superadmin permissions count: " . $superadmin->roles->first()->permissions->count();

// 2. Check admin role exists and has permissions
$adminRole = Role::where('slug', 'admin')->first();
echo "Admin role permissions: " . $adminRole->permissions->count();

// 3. Check middleware blocks brand/influencer
$brand = User::where('user_type', 'brand')->first();
echo "Brand can access dashboard: " . ($brand ? ($brand->canAccessDashboard() ? 'YES' : 'NO') : 'No brand user');

// 4. Check user_type sync
$admin_user = User::where('user_type', 'admin')->first();
$admin_user_role = $admin_user->roles->first()?->name;
echo "User type matches role: " . (strtolower($admin_user_role) === $admin_user->user_type ? 'YES' : 'NO');

// 5. Check superadmin cannot be deleted
try {
    $superadmin->delete();
    echo "FAILED: Superadmin was deleted!";
} catch (Exception $e) {
    echo "SUCCESS: Cannot delete superadmin - " . $e->getMessage();
}

// 6. Check superadmin role cannot be deleted
try {
    $superadminRole = Role::where('is_superadmin', true)->first();
    $superadminRole->delete();
    echo "FAILED: Superadmin role was deleted!";
} catch (Exception $e) {
    echo "SUCCESS: Cannot delete superadmin role - " . $e->getMessage();
}

// 7. Check sidebar rendering
auth()->login($admin_user);
$sidebarData = \App\Helpers\MenuHelper::buildSidebarMenu('dashboard.index');
echo "Sidebar items rendered: " . count($sidebarData['items']);
```

---

## 🔄 ROLLBACK (IF NEEDED)

If something goes wrong:

```bash
# Reset roles and permissions
php artisan migrate:refresh --seed

# Or manually reset:
php artisan tinker
```

```php
// Clear all role-permission mappings
DB::table('role_permissions')->truncate();

// Re-seed
exit; // Exit tinker
php artisan db:seed --class=RolePermissionSeeder
```

---

## 📖 WHAT WAS CHANGED & WHY

| Change | File | Reason |
|--------|------|--------|
| Sync user_type with role | UserController | Prevents user_type/role mismatch |
| Prevent superadmin modification | UserController | Security - superadmin must be immutable |
| Prevent own access removal | UserController | Safety - prevents self-lockout |
| Clarified middleware | RestrictDashboardAccess | Better validation order and messages |
| Permission-based sidebar | MenuHelper | UX - users don't see inaccessible items |
| syncRoles safety | User Model | Security - cannot remove superadmin role |
| Deletion protection | User Model | Security - cannot delete superadmin users |
| Comprehensive seeder | RolePermissionSeeder | Cleaner role setup with proper permissions |

---

## 🎯 KEY FLOWS NOW WORKING

### Flow 1: Creating Admin User
```
User Input → Validation → Role Assignment → user_type Sync → Permission Inheritance → Dashboard Access
```

### Flow 2: Accessing Dashboard
```
Request → Middleware Checks (7 points) → Permission Validation → Sidebar Renders (permission-based) → Dashboard Loads
```

### Flow 3: Assigning Permissions to Role
```
Superadmin Action → Role Permissions Updated → All Users with Role Get New Permissions → Sidebar Updates for All
```

---

## 🐛 COMMON ISSUES & FIXES

**Issue: "Cannot find method canAccessDashboard()"**
- Make sure User model is updated with new method
- Run: `composer dump-autoload`

**Issue: "User type not syncing"**
- Check UserController update() and assignRolesStore() have sync code
- Manually fix: `$user->update(['user_type' => strtolower($user->roles->first()->name)])`

**Issue: "Sidebar showing all items"**
- Check MenuHelper buildSidebarMenu() has permission filtering
- Verify user has permission: `$user->hasPermission('dashboard.view')`

**Issue: "Brand/Influencer can access dashboard"**
- Check middleware is registered in routes
- Check middleware RestrictDashboardAccess applied to dashboard routes
- Verify user_type is exactly 'brand' or 'influencer'

---

## 📞 SUPPORT

If you encounter issues:

1. Check RBAC_SYSTEM_REFACTORING_COMPLETE.md for full documentation
2. Run verification checks above
3. Review error messages carefully - they're descriptive
4. Check database: roles, permissions, role_permissions tables
5. Run: `php artisan cache:clear` if permissions seem stale

---

**Implementation Date:** April 12, 2026  
**Status:** Ready for Testing  
**Estimated Time:** 15-30 minutes to complete setup
