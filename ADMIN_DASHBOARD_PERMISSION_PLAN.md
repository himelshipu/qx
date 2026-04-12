# Admin Dashboard - Permission-Based Access Control Plan

## Executive Summary
Implement comprehensive permission-based access control for the admin dashboard to:
1. Restrict dashboard access to admin/moderator users ONLY
2. Brand and influencer users must NOT see the dashboard even if they know the URL
3. Control features based on role permissions: View, Create, Edit, Delete, Status Toggle
4. Ensure sidebar menus dynamically show based on user permissions
5. Protect all dashboard actions with permission checks

## Current Status
- ✅ Superadmin user created (superadmin@rockies.com) with all 144 permissions
- ✅ Dashboard access middleware "RestrictDashboardAccess" exists but needs enhancement
- ✅ PermissionSeeder already has comprehensive permissions defined
- ⚠️ Dashboard controllers need permission checks
- ❌ Sidebar/menu permission filtering not implemented
- ❌ Permission helper for views/API responses not implemented

## Architecture Overview

### Layer 1: Route Level Protection
- ✅ Middleware `RestrictDashboardAccess` - Ensures only users with roles that have dashboard.view permission
- ⚠️ Need to enhance to completely block brand/influencer by user_type check first

### Layer 2: Controller Level Protection  
- Each controller method should check specific permission before executing action
- Pattern: `$this->authorize('permission.slug')`

### Layer 3: View/API Response Level
- Sidebar/menus only show items user has permission to access
- API responses include permission information for frontend to conditionally render

---

## Phase 1: Extend & Audit PermissionSeeder

### Current Permission Categories (144 total)
1. Dashboard (1) - dashboard.view
2. Users (2) - users.index, users.toggle-status
3. Roles (7) - roles.index, create, store, edit, update, destroy, toggle-status
4. Categories (7) - categories.* 
5. Brands (8) - brands.*
6. Influencers (9) - influencers.*
7. Portfolios (8) - portfolios.*
8. Moderators (8) - moderators.*
9. Campaigns (10) - campaigns.*
10. Campaign Influencers (7) - campaign-influencers.*
11. Packages (9) - packages.*
12. Commerce (2) - carts.*
13. Orders (8) - orders.*
14. Payments (1) - payments.index
15. Payouts (1) - payouts.index
16. Wishlists (1) - wishlists.index
17. Reviews (3) - reviews.*
18. Support Tickets (5) - support-tickets.*
19. Conversations (1) - conversations.index
20. Content (Pages, Case Studies, Testimonials, FAQ, Knowledge Base) (~27) 

### Missing Permissions To Add:
1. **Users Management**
   - users.create, users.store, users.edit, users.update, users.destroy (User CRUD)
   - users.show (View user details)

2. **Dashboard Analytics**
   - analytics.view (Access to charts, stats, KPIs)

3. **Content Management (Pages, Sections)**
   - pages.index, pages.create, pages.edit, pages.update, pages.destroy, pages.toggle-status
   - page-sections.* (Complete CRUD)

4. **Knowledge Base**
   - knowledge-base.* (CRUD operations)

5. **Settings/Configuration**
   - settings.view, settings.edit, settings.update
   - system.logs (View system logs)

6. **Reports**
   - reports.orders, reports.revenue, reports.users, reports.campaigns
   - reports.export (Export reports to CSV/PDF)

7. **Verification**
   - verification.view (Pending verifications)
   - verification.approve, verification.reject

---

## Phase 2: Enhance Dashboard Access Middleware

### Current: RestrictDashboardAccess.php
- Checks if user has roles with dashboard.view permission
- Admin/Moderator can access

### Enhancements Needed:
1. **Primary Check**: Block brand/influencer by user_type FIRST
   ```
   if (in_array(auth()->user()->user_type, ['brand', 'influencer'])) {
       return redirect('/')->with('error', 'Access Denied');
   }
   ```

2. **Second Check**: Verify user_type is admin, moderator, or superadmin
   ```
   if (!in_array(auth()->user()->user_type, ['admin', 'moderator', 'superadmin'])) {
       return forbidden();
   }
   ```

3. **Third Check**: Verify role has dashboard.view permission (for admin/moderator)
   - Superadmin auto-passes
   - Admin/Moderator must have the permission

---

## Phase 3: Controller Authorization Gate

### Authorization Pattern (Laravel Gates/Policies):

**Option A: Using authorize() in Controller**
```php
public function index()
{
    $this->authorize('roles.index'); // Throws 403 if no permission
    
    return Role::all();
}
```

**Option B: Using middleware on route**
```php
Route::middleware('permission:roles.index')->get('/roles', [RoleController::class, 'index']);
```

### Implementation Strategy:
- Create custom `PermissionMiddleware` that accepts permission slug
- Apply to all dashboard routes
- Fallback to controller authorization for complex logic

---

## Phase 4: Response/View Permission Helper

### Create PermissionHelper Trait
```php
trait HasPermissions {
    public function can($permission): bool {
        return $this->auth->user()->hasPermission($permission);
    }
    
    public function canAny(array $permissions): bool {
        // Check if user has ANY of the permissions
    }
    
    public function canAll(array $permissions): bool {
        // Check if user has ALL permissions
    }
    
    public function getAccessibleModules(): array {
        // Return list of modules user can access
    }
}
```

### Usage in Controllers:
```php
public function index()
{
    return response()->json([
        'roles' => Role::all(),
        'permissions' => [
            'can_create' => $this->canUser('roles.create'),
            'can_edit' => $this->canUser('roles.edit'),
            'can_delete' => $this->canUser('roles.destroy'),
        ]
    ]);
}
```

---

## Phase 5: Sidebar Menu Permission Filtering

### Menu Structure (Backend):
```
Dashboard (dashboard.view)
├── Users (users.index) 
│   ├── View (users.index)
│   ├── Create (users.create)
│   └── Roles (roles.index)
├── Content Management
│   ├── Categories (categories.index)
│   ├── Brands (brands.index)
│   ├── Influencers (influencers.index)
│   └── Pages (pages.index)
├── Campaigns
│   ├── Campaigns (campaigns.index)
│   └── Packages (packages.index)
├── Orders & Revenue
│   ├── Orders (orders.index)
│   ├── Payments (payments.index)
│   ├── Payouts (payouts.index)
│   └── Reviews (reviews.index)
├── Support
│   ├── Support Tickets (support-tickets.index)
│   └── Conversations (conversations.index)
├── Content Pages (content module)
│   ├── Case Studies (case-studies.index)
│   ├── Testimonials (testimonials.index)
│   ├── FAQ (faqs.sections.index)
│   └── Knowledge Base (knowledge-base.index)
└── Settings (settings.view)
    ├── System Settings (settings.edit)
    └── Logs (system.logs)
```

### Sidebar API Response:
```php
// GET /api/dashboard/menu
{
    "menu": [
        {
            "label": "Dashboard",
            "icon": "dashboard",
            "permission": "dashboard.view",
            "accessible": true,
            "children": [
                {
                    "label": "Analytics",
                    "route": "dashboard.analytics",
                    "permission": "analytics.view",
                    "accessible": true
                }
            ]
        },
        // ... more items
    ]
}
```

---

## Phase 6: Access Blocking for Brand/Influencer

### Implementation Points:

**Route Level:**
- Add early check in middleware to block brand/influencer completely

**Controller Level:**
- Add check at top of every dashboard controller method
- Return 403 Forbidden with clear message

**API Response Level:**
- Include 'is_dashboard_accessible' flag in auth user response
- Frontend checks this before rendering dashboard

**Frontend Level:**
- Hide dashboard link for brand/influencer users
- Redirect to home if they try to access /dashboard URL

---

## Detailed Implementation Plan

### Step 1: Extend PermissionSeeder ✓ (See Phase 1)

### Step 2: Enhance RestrictDashboardAccess Middleware
- Add user_type check BEFORE role check
- Return clear error for brand/influencer

### Step 3: Create PermissionMiddleware
- Accept permission slug as parameter
- Check user has the permission
- Return 403 if not

### Step 4: Apply PermissionMiddleware to Routes
- Dashboard routes group with middleware
- Individual critical routes with specific permissions

### Step 5: Create PermissionHelper Trait
- Add to User model or base Controller
- Provide helper methods for checking permissions

### Step 6: Create AdminMenu Model & Seeder
- Define menu structure with permissions
- Seed through DatabaseSeeder
- Use in API response

### Step 7: Create Dashboard Menu API Endpoint
- GET /api/dashboard/menu
- Returns menu items filtered by user permissions

### Step 8: Update Controllers  
- Add permission checks
- Return permission info in API responses

### Step 9: Test & Verification
- Test superadmin access to all  
- Test admin/moderator access based on permissions
- Test brand/influencer complete blocking

---

## File Structure Changes

### New Files To Create:
1. `app/Http/Middleware/PermissionMiddleware.php`
2. `app/Models/AdminMenu.php` 
3. `app/Traits/HasPermissions.php`
4. `database/seeders/AdminMenuSeeder.php`
5. `app/Http/Controllers/Api/MenuController.php` (or similar)

### Files To Modify:
1. `database/seeders/PermissionSeeder.php` - Add missing permissions
2. `app/Http/Middleware/RestrictDashboardAccess.php` - Enhance user_type check
3. `routes/web.php` - Add permission middleware to routes
4. Individual controller files - Add permission checks
5. `bootstrap/app.php` - Register PermissionMiddleware

---

## Testing Strategy

### Unit Tests:
- Test User model permission checking methods
- Test middleware authorization

### Feature Tests:
- Test superadmin can access all dashboard pages
- Test admin with specific permissions can access those pages
- Test admin without permission gets 403
- Test brand user gets 403 immediately
- Test influencer user gets 403 immediately
- Test each action requires its permission

### Manual Tests:
- Login as different user types
- Try accessing restricted pages
- Verify sidebar only shows accessible items
- Try direct URL access to forbidden resources

---

## Priority Order
1. **CRITICAL**: Enhance RestrictDashboardAccess to block brand/influencer by user_type
2. **HIGH**: Extend PermissionSeeder with missing user/settings permissions
3. **HIGH**: Create PermissionMiddleware for route-level checks
4. **MEDIUM**: Add permission checks in controllers
5. **MEDIUM**: Create menu system with permission filtering
6. **LOW**: Add permission info to API responses
7. **LOW**: Admin menu UI components (if frontend exists)

