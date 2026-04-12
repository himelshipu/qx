# RBAC System Refactoring - Visual Summary

## System Architecture Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                      RBAC SYSTEM OVERVIEW                        │
└─────────────────────────────────────────────────────────────────┘

                        USER TYPES
                            │
        ┌───────────────────┼───────────────────┐
        │                   │                   │
    SUPERADMIN           ADMIN              BRAND/INFLUENCER
        │                   │                   │
    ┌───┴───┐           ┌───┴───┐          ┌───┴───┐
    │       │           │       │          │       │
    ↓       ↓           ↓       ↓          ↓       ↓
  admin   roles       users  campaigns   BLOCKED BLOCKED
                                           from dashboard
                                           
                      MIDDLEWARE
                      (Validation)
                            │
        ┌───────────────────┼───────────────────┐
        │                   │                   │
   Authenticated?    Email Verified?      Is Active?
        │                   │                   │
        └───────────────────┼───────────────────┘
                            │
                    Is Brand/Influencer?
                            │
                        NO  │  YES
                        │   └─→ BLOCKED
                        │
                    Has Role?
                        │
                    YES │  NO
                        │   └─→ BLOCKED
                        │
                    Has dashboard.view
                    permission?
                        │
                    YES │  NO
                        │   └─→ BLOCKED
                        │
                    ✅ ACCESS GRANTED
```

---

## Data Model

```
┌──────────────┐         ┌──────────────┐
│    USERS     │         │    ROLES     │
├──────────────┤         ├──────────────┤
│ id           │◄──────┐ │ id           │
│ name         │       │ │ name         │
│ email        │       │ │ slug         │
│ user_type    │       └─│ is_superadmin│
│ is_active    │  Many-to-Many (user_roles table)
│              │         │              │
└──────────────┘         └──────────────┘
                              │
                              │ Many-to-Many
                              │ (role_permissions)
                              │
                         ┌────────────────┐
                         │  PERMISSIONS   │
                         ├────────────────┤
                         │ id             │
                         │ name           │
                         │ slug           │
                         │ module         │
                         │ is_active      │
                         └────────────────┘
```

---

## User Type to Role Mapping

```
User Creation/Assignment Flow:
───────────────────────────

User → Assign Role → Sync user_type → Set Permissions
         │               │                  │
      ┌──┴──┐          ┌──┴──┐           ┌──┴──┐
      │     │          │     │           │     │
   admin  moderator   admin  moderator  admin  moderator
      │     │          │     │
      └─────┴──┬───────┴─────┘
               ↓
          user_type = role name
          (automatically synced)
```

**Examples:**
```
Role: Admin        → user_type: admin
Role: Moderator    → user_type: moderator
Role: Manager      → user_type: manager
Role: Superadmin   → user_type: superadmin

Brand/Influencer   → user_type: brand/influencer
(Not roles,       (Not assigned dashboard role)
 frontend only)
```

---

## Permission Hierarchy

```
PERMISSIONS BY MODULE
─────────────────────

Dashboard
├── dashboard.view
├── analytics.view
├── analytics.kpis
└── analytics.revenue

Users Management
├── users.index
├── users.create
├── users.store
├── users.show
├── users.edit
├── users.update
└── users.toggle-status

Roles
├── roles.index
├── roles.create
├── roles.store
├── roles.edit
├── roles.update
└── roles.toggle-status

[...20+ more modules...]

Content
├── campaigns.index
├── campaigns.create
├── campaigns.show
├── campaigns.edit
├── campaigns.update
└── campaigns.delete

Commerce
├── orders.index
├── orders.show
├── orders.update-status
├── payments.index
├── payouts.index
└── wishlists.index

Moderation
├── moderation.queue
├── moderation.review
├── moderation.block-users
└── moderation.remove-content
```

---

## Role Permission Assignment Matrix

```
┌────────────┬──────────┬──────────┬─────────┬──────────────┐
│ Permission │ Superadmin│  Admin   │Moderator│   Manager    │
├────────────┼──────────┼──────────┼─────────┼──────────────┤
│dashboard   │    ✅    │    ✅    │   ✅    │     ✅       │
│users.*     │    ✅    │    ✅    │   ❌    │     ❌       │
│roles.*     │    ✅    │    ✅    │   ❌    │     ❌       │
│campaigns   │    ✅    │    ✅    │   ❌    │   view only  │
│reviews.*   │    ✅    │    ✅    │   ✅    │     ❌       │
│support.*   │    ✅    │    ✅    │   ✅    │     ❌       │
│moderation.*│    ✅    │    ✅    │   ✅    │     ❌       │
│settings.*  │    ✅    │    ✅    │   ❌    │     ❌       │
└────────────┴──────────┴──────────┴─────────┴──────────────┘

Key:
✅ Full access (create, read, update, delete)
view only = Can only see/read
❌ No access
```

---

## Sidebar Rendering Flow

```
┌─────────────────────────────────────┐
│ User Requests Dashboard             │
└─────────────────────────────────────┘
               ↓
┌─────────────────────────────────────┐
│ MenuHelper::buildSidebarMenu()       │
└─────────────────────────────────────┘
               ↓
         For each menu item:
               ├─ Get required permission
               ├─ Check user.hasPermission()?
               │
         ┌─────┴─────┐
         │           │
        YES          NO
         │           │
    Include       Skip (Hide)
         │           │
         └─────┬─────┘
               ↓
    ┌────────────────────────┐
    │ Render Permitted Items │
    │ (Dynamic Sidebar)      │
    └────────────────────────┘
```

**Example:**
```
MenuHelper builds sidebar:
├── Dashboard ✓ (always visible)
├── MANAGEMENT GROUP
│   ├── Categories (user has permission) ✓
│   ├── Brands (user has permission) ✓
│   └── Influencers (user lacks permission) ✗ HIDDEN
├── CAMPAIGNS GROUP
│   ├── All Campaigns (user has permission) ✓
│   └── New Campaign (user has permission) ✓
└── [other groups...] (filtered)
```

---

## Safety Features Overview

```
┌──────────────────────────────────────────────────────┐
│              SAFETY & PROTECTION LAYER               │
└──────────────────────────────────────────────────────┘

1. SUPERADMIN IMMUTABILITY
   ├── Cannot be deleted → Model booted protection
   ├── Cannot be edited → Controller validation
   ├── Cannot lose role → syncRoles() safeguard
   └── Cannot be reassigned → Permission check

2. SELF-PROTECTION
   ├── Cannot remove own dashboard access
   ├── Cannot modify own user type inappropriately
   └── Cannot lock yourself out

3. ROLE INTEGRITY
   ├── Superadmin role always protected
   ├── User_type always syncs with role
   └── Permissions always flow through roles

4. VALIDATION
   ├── 7-point middleware checks
   ├── User_type consistency checks
   └── Permission inheritance validation
```

---

## Authentication & Authorization Flow

```
┌─────────────────────────────────────┐
│ User Requests Dashboard             │
└─────────────────────────────────────┘
            │
            ↓
┌─────────────────────────────────────┐
│ RestrictDashboardAccess Middleware   │
│ (7-point validation)                │
└─────────────────────────────────────┘
            │
   ┌────────┴────────┬────────┬────────┬────────┬────────┬────────┐
   ↓                 ↓        ↓        ↓        ↓        ↓        ↓
Check 1         Check 2    Check 3  Check 4  Check 5  Check 6  Check 7
Auth?           Email      Active?   Brand/   Valid    Role     Permission
│               Verified?            Influencer? Type?  Assigned? .view?
│
NO │ YES
│  └──→ CHECK 2
       │
      NO │ YES
       │  └──→ CHECK 3
          │
         NO │ YES
          │  └──→ CHECK 4
             │
            NO │ YES
             │  └──→ ... (continue)
                │
              PASS ALL 7 ✅
                │
                ↓
      ┌──────────────────────┐
      │ Dashboard Loads      │
      │ Sidebar Renders      │
      │ User Can Access      │
      └──────────────────────┘
```

---

## Permission Checking Methods

```
┌──────────────────────────────────┐
│ Permission Checking in Code      │
└──────────────────────────────────┘

Method 1: Direct Check
─────────────────────
$user->hasPermission('users.create')
├── Check superadmin → return true
├── Check user permissions
└── Check role permissions

Method 2: Helper Method
──────────────────────
$user->canDo('users.create')
└── Calls hasPermission() internally

Method 3: Multiple (ANY)
───────────────────────
$user->canDoAny(['users.create', 'users.edit'])
└── Returns true if user has ANY permission

Method 4: Multiple (ALL)
───────────────────────
$user->canDoAll(['users.create', 'users.edit'])
└── Returns true if user has ALL permissions

Method 5: Role Check
────────────────────
$user->hasRole('admin')
└── Checks if user has specific role

Method 6: Superadmin Check
──────────────────────────
$user->isSuperadmin()
└── Checks if user is superadmin (has superadmin role)
```

---

## State Transitions

```
USER LIFECYCLE
──────────────

New User
  ↓ Create with role_id
├─ Set user_type = role name
├─ Create roles entry
└─ Inherit role permissions
  ↓
Active User
  ├─ Bypass middleware checks
  ├─ Access dashboard
  └─ See permitted sidebar items
  ↓
Role Change
  ├─ Sync roles
  ├─ Update user_type
  ├─ Inherit new permissions
  └─ Sidebar updates automatically
  ↓
User Deactivation
  ├─ Set is_active = false
  ├─ Blocked at middleware (check 3)
  └─ Cannot access dashboard
  ↓
User Deletion
  ├─ Check if superadmin
  ├─ If superadmin → Throw error ❌
  └─ If regular user → Delete ✅
```

---

## Code Interaction Map

```
┌─────────────────────────────────────────────────┐
│              APPLICATION FLOW                   │
└─────────────────────────────────────────────────┘

USER INTERACTION
    │
    ├─ Requests Dashboard
    │  └─ RestrictDashboardAccess Middleware
    │     ├─ User model: isSuperadmin(), hasPermission()
    │     └─ Role model: permissions relationship
    │
    ├─ Sidebar Renders
    │  └─ MenuHelper::buildSidebarMenu()
    │     ├─ User model: hasPermission()
    │     ├─ Role model: permissions relationship
    │     └─ Permission model: slug matching
    │
    ├─ Clicks Menu Item
    │  └─ Request reaches route
    │     └─ Route middleware/controller validates
    │        └─ User model: hasPermission() again
    │
    └─ Admin Creates User
       └─ UserController::store()
          ├─ Create User model instance
          ├─ Set user_type from role
          ├─ Attach role
          └─ User now has permissions from that role
```

---

## File Dependency Graph

```
┌─────────────────────────────────────────────────┐
│              FILE DEPENDENCIES                  │
└─────────────────────────────────────────────────┘

RestrictDashboardAccess.php
  ├─ User.php (isSuperadmin, hasPermission)
  ├─ Role.php (is_superadmin)
  └─ Permission.php (slug)

MenuHelper.php
  ├─ User.php (hasPermission)
  ├─ Role.php (permissions)
  └─ Permission.php (slug)

UserController.php
  ├─ User.php (all methods)
  ├─ Role.php (findOrFail)
  └─ Permission.php (none)

User.php
  ├─ Role.php (relationships)
  ├─ Permission.php (relationships)
  └─ HasPermissionsHelper.php (traits)

RolePermissionSeeder.php
  ├─ Role.php (sync)
  ├─ Permission.php (whereIn)
  └─ Database
```

---

## Testing Scenarios

```
SCENARIO 1: Superadmin Access
──────────────────────────────
User: superadmin@example.com
Role: Superadmin
Status: Active
Result: ✅ Full access to everything

SCENARIO 2: Admin Limited Access
─────────────────────────────────
User: admin@example.com
Role: Admin (no 'users.delete')
Status: Active
Result: ✅ Can do most things, cannot delete users

SCENARIO 3: Brand Blocked
─────────────────────────
User: brand@example.com
User_type: brand
Status: Active, logged in
Result: ❌ BLOCKED at middleware check 4 (brand user type)

SCENARIO 4: Permission Hidden Sidebar
──────────────────────────────────────
User: moderator@example.com
Role: Moderator (no 'users.create')
Status: Active
Result: ✅ Can see dashboard but "Create User" hidden from sidebar

SCENARIO 5: No Dashboard Permission
────────────────────────────────────
User: manager@example.com
Role: Manager (no 'dashboard.view')
Status: Active
Result: ❌ BLOCKED at middleware check 7 (no dashboard.view)
```

---

**Diagram Version:** 1.0  
**Created:** April 12, 2026  
**For Questions:** See RBAC_QUICK_REFERENCE.md or RBAC_SYSTEM_REFACTORING_COMPLETE.md
