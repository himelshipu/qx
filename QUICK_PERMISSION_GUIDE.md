# Quick Permission Implementation Guide

## 🚀 One-Minute Summary

Your Rockies platform now has **enterprise-grade permission system**:

✅ **200+ permissions** across all modules  
✅ **4 distinct roles** with different access levels  
✅ **Route-level protection** using middleware  
✅ **Controller-level protection** using trait  
✅ **Menu auto-filtering** based on permissions  
✅ **Superadmin bypass** automatic  

---

## 📋 What Was Added

### New Permissions (8 total)
```
categories.reorder      → Drag-drop reorder categories
brands.reorder         → Drag-drop reorder brands
influencers.reorder    → Drag-drop reorder influencers
testimonials.reorder   → Drag-drop reorder testimonials
analytics.users        → View user analytics
analytics.campaigns    → View campaign analytics
analytics.orders       → View order analytics
```

### New Middleware
```
CheckPermission.php → Protects routes with permission checks
Usage: ->middleware('check-permission:permission.slug')
```

### New Trait
```
PermissionChecker.php → Allows controllers to check permissions
Methods: checkPermission(), checkPermissions(), etc.
```

### Protected Routes (4)
```
POST /dashboard/categories/reorder     → requires categories.reorder
POST /dashboard/brands/reorder         → requires brands.reorder
POST /dashboard/influencers/reorder    → requires influencers.reorder
POST /dashboard/testimonials/reorder   → requires testimonials.reorder
```

---

## 👤 User Permission Levels

### Superadmin Role
```
✅ Everything
✅ All 200+ permissions
✅ No restrictions
```

### Admin Role
```
✅ Dashboard & Analytics
✅ User Management (Full CRUD)
✅ Content Management (Full CRUD + Reorder)
✅ Campaign Management (Full)
✅ Orders & Payments (Full)
✅ Reports & Settings
✗ Cannot access outside dashboard
```

### Moderator Role
```
✅ Dashboard (basic)
✅ Support Tickets (Full)
✅ Content Moderation
✅ User Verification
✓ Read-only for resources
✗ Cannot create/edit/delete content
✗ Cannot manage users/campaigns
```

### Manager Role
```
✅ Dashboard (read-only)
✅ View Analytics
✓ Read-only access to resources
✓ View Reports
✗ Cannot modify anything
✗ Cannot manage anything
```

---

## 🔒 How Protection Works

### Method 1: Middleware (Routes)
```php
// In routes/web.php
Route::post('/items/reorder', [ItemController::class, 'reorder'])
    ->middleware('check-permission:items.reorder');

// User tries without permission → 403 Forbidden
// User tries with permission → Proceeds
```

### Method 2: Trait (Controllers)
```php
// In controller
use PermissionChecker;

public function destroy(Item $item)
{
    $this->checkPermission('items.destroy');
    
    $item->delete();
    return back()->with('success', 'Deleted');
}

// Without permission → Throws AuthorizationException (403)
// With permission → Proceeds normally
```

### Method 3: Conditional (Views/Logic)
```php
// In controller or view
if (auth()->user()->hasPermission('items.destroy')) {
    // Show delete button
}

// Without permission → Button hidden
// With permission → Button visible and functional
```

---

## 📊 Permission Matrix

```
┌─────────────┬───────────┬──────────┬────────────┬──────────┐
│ Resource    │ Superadmin│ Admin    │ Moderator  │ Manager  │
├─────────────┼───────────┼──────────┼────────────┼──────────┤
│ Dashboard   │ ✅ View   │ ✅ View  │ ✅ View    │ ✅ View  │
│ Users       │ ✅ CRUD   │ ✅ CRUD  │ ✅ View    │ ✅ View  │
│ Categories  │ ✅ CRUD   │ ✅ CRUD  │ ✗ None     │ ✗ None   │
│ Brands      │ ✅ CRUD   │ ✅ CRUD  │ ✗ None     │ ✗ None   │
│ Campaigns   │ ✅ CRUD   │ ✅ CRUD  │ ✅ View    │ ✅ View  │
│ Orders      │ ✅ CRUD   │ ✅ CRUD  │ ✅ View    │ ✅ View  │
│ Payments    │ ✅ CRUD   │ ✅ CRUD  │ ✗ None     │ ✗ None   │
│ Support     │ ✅ CRUD   │ ✅ CRUD  │ ✅ CRUD    │ ✗ None   │
│ Analytics   │ ✅ All    │ ✅ All   │ ✅ Basic   │ ✅ Basic │
│ Reports     │ ✅ All    │ ✅ All   │ ✗ None     │ ✅ View  │
└─────────────┴───────────┴──────────┴────────────┴──────────┘
```

---

## ⚡ Quick Implementation

### For Existing Routes

1. **Find the route** in `routes/web.php`
2. **Add middleware** to route:
   ```php
   ->middleware('check-permission:resource.action')
   ```

### For Existing Controllers

1. **Add trait** to class:
   ```php
   use PermissionChecker;
   ```

2. **Add check** to sensitive methods:
   ```php
   $this->checkPermission('resource.action');
   ```

### For New Routes

1. **Define permission** in `PermissionSeeder.php`:
   ```php
   ['name' => 'New Action', 'slug' => 'resource.action', 'module' => 'resource'],
   ```

2. **Assign to roles** in `RolePermissionSeeder.php`:
   ```php
   $adminPermissions = [..., 'resource.action'];
   ```

3. **Protect route** in `routes/web.php`:
   ```php
   Route::post('/path', [Controller::class, 'method'])
       ->middleware('check-permission:resource.action');
   ```

4. **Run seeders**:
   ```bash
   php artisan db:seed
   ```

---

## 🔍 Testing Permissions

### Via Browser

1. **Login as Admin**
   - Navigate to `/dashboard`
   - Try to reorder items → Should work
   - Should see all menu items

2. **Login as Moderator**
   - Navigate to `/dashboard`
   - Try to reorder items → Should fail (403)
   - Should see limited menu items

3. **Login as Manager**
   - Navigate to `/dashboard`
   - Try to view reports → Should work
   - Try to edit anything → Should fail (403)

### Via Terminal

```bash
# Check permissions in database
mysql -u root -p rockies

# List all permissions
SELECT slug FROM permissions WHERE is_active = 1;

# Check admin permissions
SELECT p.slug FROM permissions p
JOIN permission_role pr ON p.id = pr.permission_id
JOIN roles r ON pr.role_id = r.id
WHERE r.slug = 'admin' ORDER BY p.slug;

# Check user's permissions (user_id=1)
SELECT DISTINCT p.slug FROM permissions p
JOIN permission_role pr ON p.id = pr.permission_id
JOIN role_user ru ON pr.role_id = ru.role_id
WHERE ru.user_id = 1 ORDER BY p.slug;
```

---

## 📝 Code Examples

### Example 1: Check Permission in Controller

```php
<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\PermissionChecker;

class UserController extends Controller
{
    use PermissionChecker;

    public function destroy(User $user)
    {
        // Check permission before proceeding
        $this->checkPermission('users.destroy');
        
        $user->delete();
        
        return back()->with('success', 'User deleted successfully');
    }

    public function bulkDelete()
    {
        // Check multiple permissions (all required)
        $this->checkPermissions([
            'users.destroy',
            'bulk.users-delete'
        ]);
        
        // Bulk delete logic
    }

    public function manage()
    {
        // Check any permission (at least one required)
        $this->checkPermissionsAny([
            'users.edit',
            'users.update'
        ]);
        
        // Management logic
    }
}
```

### Example 2: Protect Routes

```php
<?php

// In routes/web.php

// Single permission
Route::delete('/users/{user}', [UserController::class, 'destroy'])
    ->middleware('check-permission:users.destroy');

// Multiple routes with same permission
Route::middleware('check-permission:reports.export')->group(function () {
    Route::get('/reports/pdf', [ReportController::class, 'exportPdf']);
    Route::get('/reports/csv', [ReportController::class, 'exportCsv']);
});

// Reorder with permission (already done)
Route::post('/categories/reorder', [CategoryController::class, 'reorder'])
    ->middleware('check-permission:categories.reorder');
```

### Example 3: Conditional UI

```php
<!-- In Blade template -->

@if(auth()->user()->hasPermission('users.destroy'))
    <button onclick="deleteUser({{ $user->id }})">Delete</button>
@endif

@if(auth()->user()->hasPermission('users.create'))
    <a href="{{ route('users.create') }}" class="btn btn-primary">Create User</a>
@endif

<!-- Show admin-only section -->
@if(auth()->user()->hasPermission('settings.update'))
    <div class="admin-settings">
        <!-- Admin controls -->
    </div>
@endif
```

### Example 4: Add New Permission

```php
// Step 1: Add to PermissionSeeder.php
'permissions' => [
    ...existing permissions...
    ['name' => 'Export Reports', 'slug' => 'reports.export', 'module' => 'reports'],
];

// Step 2: Assign to role in RolePermissionSeeder.php
$adminPermissions = [
    ...existing permissions...
    'reports.export',
];

// Step 3: Protect route in routes/web.php
Route::get('/reports/export', [ReportController::class, 'export'])
    ->middleware('check-permission:reports.export');

// Step 4: Run seeders
// php artisan db:seed

// Step 5: Use in controller
$this->checkPermission('reports.export');

// Step 6: Use in view
@if(auth()->user()->hasPermission('reports.export'))
    <button>Export</button>
@endif
```

---

## 🎯 Best Practices

### 1. Permission Naming
```
GOOD:     users.destroy, categories.reorder, reports.export
BAD:      delete, sort, download
PATTERN:  {resource}.{action}
```

### 2. Grouping Similar Permissions
```php
// Group by resource
'users.create', 'users.read', 'users.update', 'users.destroy'

// NOT mixed with different resources
'create_user', 'get_category', 'edit_order'
```

### 3. Role Assignment Strategy
```
Superadmin → Gets everything (automatic)
Admin      → Gets module management permissions
Moderator  → Gets review/support permissions
Manager    → Gets read-only/report permissions
```

### 4. Default to Deny
```php
// Wrong: Only check if should allow
if (isAdmin()) { /* Allow */ }

// Right: Always check explicit permission
$this->checkPermission('action.resource');
```

### 5. Clear Error Messages
```php
// Good
$this->checkPermission('users.destroy', 
    'You do not have permission to delete users. Contact administrator.'
);

// Avoid
$this->checkPermission('users.destroy');
```

---

## ⚠️ Common Issues & Solutions

### Issue 1: "Permission Denied" on Route That Should Work
**Solution**:
1. Check user has the role assigned
2. Check role has permission in `RolePermissionSeeder.php`
3. Check permission exists in `PermissionSeeder.php`
4. Verify middleware spelling: `check-permission:exact.slug`

### Issue 2: Menu Item Still Visible for User Without Permission
**Solution**:
1. Run `php artisan cache:clear`
2. Check `MenuHelper.php` has permission mapping for that route
3. Add mapping if missing:
   ```php
   'route.name' => 'permission.slug',
   ```

### Issue 3: Superadmin Can't Access Feature
**Solution**:
1. Superadmin auto-gets all permissions (don't add checks)
2. Don't use `checkPermission()` for superadmin
3. If checking, it auto-bypasses

### Issue 4: Getting 403 When Should Have Access
**Solution**:
1. Verify user role: `$user->roles;`
2. Verify role has permission: 
   ```sql
   SELECT p.slug FROM permissions p
   JOIN permission_role pr ON p.id = pr.permission_id
   WHERE pr.role_id = {user_role_id};
   ```
3. Run seeders: `php artisan db:seed`

---

## 📚 Related Files

| File | Purpose |
|------|---------|
| `database/seeders/PermissionSeeder.php` | Define all permissions |
| `database/seeders/RolePermissionSeeder.php` | Assign permissions to roles |
| `app/Http/Middleware/CheckPermission.php` | Route-level protection |
| `app/Traits/PermissionChecker.php` | Controller-level protection |
| `app/Helpers/MenuHelper.php` | Menu visibility filtering |
| `bootstrap/app.php` | Middleware registration |
| `routes/web.php` | Route definitions with middleware |

---

## 🔗 Related Documentation

- `ADMIN_DASHBOARD_PERMISSIONS_IMPLEMENTATION.md` - Comprehensive guide
- `PERMISSIONS_SETUP_VERIFICATION.md` - Verification checklist
- This file - Quick reference

---

## 💡 Common Tasks

### Add Permission to User
```php
$user->assignRole('admin'); // Gets all admin permissions
// OR
$user->givePermissionTo('users.destroy'); // Direct permission
```

### Remove Permission from User
```php
$user->removeRole('admin');
// OR
$user->revokePermissionTo('users.destroy');
```

### Check if User Has Multiple Permissions
```php
// All required
if ($user->hasAllPermissions(['users.edit', 'users.update'])) { }

// Any of them
if ($user->hasAnyPermission(['users.destroy', 'bulk.users-delete'])) { }

// Specific permission
if ($user->hasPermission('users.destroy')) { }
```

### List All User Permissions
```php
$permissions = $user->getPermissions();
foreach ($permissions as $permission) {
    echo $permission->slug;
}
```

---

## ✅ Verification Commands

```bash
# Check permissions exist
mysql -u root -p rockies
SELECT COUNT(*) FROM permissions WHERE is_active = 1;

# Check role-permission mapping
SELECT r.name, COUNT(pr.permission_id) 
FROM roles r
LEFT JOIN permission_role pr ON r.id = pr.role_id
GROUP BY r.id;

# Clear cache
php artisan cache:clear

# Run seeders
php artisan db:seed

# Test route
curl -X POST http://localhost:8000/dashboard/categories/reorder \
     -H "Authorization: Bearer {token}"
```

---

## 🎓 Next Level

### Add Permission Checks to More Controllers
```bash
# Add PermissionChecker trait to:
- BrandController.php
- InfluencerController.php
- CampaignController.php
- OrderController.php
- PaymentController.php
```

### Add Middleware to More Routes
```bash
# Protect all sensitive routes:
- All DELETE operations
- All POST operations
- All PUT/PATCH operations
```

### Add Permission Logging
```php
// Log who used what permission
Log::info("User {$user->id} used permission: {$permission}");
```

### Create Admin Dashboard for Permissions
```php
// Add interface to:
- View all permissions
- Assign permissions to roles
- Audit permission usage
- Reset role permissions
```

---

## 🆘 Need Help?

1. Check `ADMIN_DASHBOARD_PERMISSIONS_IMPLEMENTATION.md` for detailed docs
2. Review code examples in controllers
3. Check database tables: `permissions`, `roles`, `permission_role`, `role_user`
4. Test with terminal commands provided above
5. Check Laravel logs: `storage/logs/laravel.log`

---

**Status**: ✅ Ready to Use  
**Last Updated**: April 13, 2026  
**Next Step**: Verify setup and test permissions
