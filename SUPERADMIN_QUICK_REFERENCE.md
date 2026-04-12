# Superadmin Role - Quick Reference Guide

## Quick Start

### Check if User is Superadmin

```php
// In controller or wherever you need it
if (auth()->user()->isSuperadmin()) {
    // User has superadmin role
}
```

### Check if Role is Superadmin

```php
$role = Role::find(1);
if ($role->isSuperadmin()) {
    // Role is superadmin
}
```

### Assign Superadmin Role to User

```php
$user = User::find(1);
$superadminRole = Role::where('slug', 'superadmin')->first();
$user->assignRole($superadminRole);
```

### Get All Users with Superadmin Role

```php
$superadminRole = Role::where('slug', 'superadmin')->first();
$superadminUsers = $superadminRole->users()->get();
```

## Using RoleService

### Inject Service in Controller

```php
public function __construct(
    private RoleService $roleService
) {}
```

### Common Operations

```php
// Get all superadmin roles
$adminRoles = $this->roleService->getSuperadminRoles();

// Check if role is superadmin
$isSuperadmin = $this->roleService->isSuperadmin($role);

// Make a role superadmin
$this->roleService->markAsSuperadmin($role);

// Remove superadmin status
$this->roleService->removeSuperadminStatus($role);

// Create new role
$newRole = $this->roleService->createRole([
    'name' => 'Custom Admin',
    'slug' => 'custom-admin',
    'is_superadmin' => true
]);
```

## Authorization in Controllers

### Using Gates

```php
// Check if user can create roles
if (auth()->user()->can('create', Role::class)) {
    // Show create form
}

// Check if user can manage a specific role
if (auth()->user()->can('update', $role)) {
    // Show edit form
}
```

### Using Policy Authorize Method

```php
public function update(Role $role)
{
    // Automatically checks RolePolicy::update()
    $this->authorize('update', $role);

    // Safe to update role here
}

public function delete(Role $role)
{
    // Superadmin roles cannot be deleted
    $this->authorize('delete', $role);
    $role->delete();
}
```

## Middleware Protection

### Create Custom Middleware

```php
// app/Http/Middleware/IsSuperadmin.php
class IsSuperadmin
{
    public function handle($request, $next)
    {
        if (!$request->user()?->isSuperadmin()) {
            abort(403, 'Superadmin access required');
        }
        return $next($request);
    }
}
```

### Use in Routes

```php
Route::middleware('is.superadmin')->group(function () {
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);
});
```

## Database Queries

### Find Superadmin Roles

```php
// Single superadmin role
$superadmin = Role::where('is_superadmin', true)->first();

// All superadmin roles
$superadmins = Role::where('is_superadmin', true)->get();

// Users with superadmin roles
$users = User::whereHas('roles', function ($query) {
    $query->where('is_superadmin', true);
})->get();
```

### Role Details

```php
$role = Role::find(1);
$role->name;                    // Role name
$role->slug;                    // Role slug
$role->is_superadmin;           // Boolean
$role->is_active;               // Boolean
$role->permissions()->count();  // Permission count
$role->users()->count();        // User count
```

## User Role Management

### User Methods

```php
$user = User::find(1);

// Check roles
$user->hasRole('superadmin');              // boolean
$user->isSuperadmin();                     // boolean
$user->hasSuperadminRole();                // boolean

// Get roles
$user->roles;                              // All roles
$user->getSuperadminRoles();               // Superadmin roles only

// Manage roles
$user->assignRole($role);
$user->removeRole($role);
$user->syncRoles([1, 2, 3]);               // Replace all roles

// Check permissions (considers superadmin)
$user->hasPermission('create-campaign');
```

## Testing

### Run Tests

```bash
# Run superadmin tests
php artisan test tests/Unit/Models/SuperadminRoleTest.php

# Run all tests
php artisan test

# Run with coverage
php artisan test --coverage
```

### Test Examples

```php
public function test_user_is_superadmin()
{
    $user = User::create($userData);
    $superadminRole = Role::where('slug', 'superadmin')->first();
    $user->assignRole($superadminRole);

    $this->assertTrue($user->isSuperadmin());
}
```

## API Responses

### User with Superadmin Status

```json
{
    "id": 1,
    "name": "Admin User",
    "email": "admin@example.com",
    "is_superadmin": true,
    "roles": [
        {
            "id": 5,
            "name": "Superadmin",
            "slug": "superadmin",
            "is_superadmin": true,
            "is_active": true
        }
    ]
}
```

## Common Patterns

### Admin-Only Endpoint

```php
Route::post('/admin/roles', function (Request $request) {
    // Using middleware
})->middleware('is.superadmin');

// Or using controller authorization
public function store(Request $request)
{
    $this->authorize('create', Role::class);
    // Create role
}
```

### Admin Check in View/Blade

```php
@if(auth()->user()?->isSuperadmin())
    <div class="admin-panel">
        <!-- Admin content -->
    </div>
@endif
```

### Audit Log Pattern

```php
if (auth()->user()->isSuperadmin()) {
    Log::info('Superadmin action', [
        'user_id' => auth()->id(),
        'action' => 'create_role',
        'timestamp' => now()
    ]);
}
```

## Useful Artisan Commands

```bash
# Seed superadmin role
php artisan db:seed --class=RoleSeeder

# Create a role manually
php artisan tinker
>>> Role::create(['name' => 'Admin', 'slug' => 'admin', 'is_superadmin' => true])

# Get role info
>>> Role::where('slug', 'superadmin')->first()

# List all superadmin roles
>>> Role::where('is_superadmin', true)->get()
```

## Troubleshooting

### User not recognized as superadmin

```php
// Check if user has the role
$user->roles()->pluck('slug'); // Should include 'superadmin'

// Check role is_superadmin flag
$user->roles()->where('is_superadmin', true)->count(); // > 0

// Verify database
> SELECT * FROM roles WHERE id IN (
    SELECT role_id FROM user_roles WHERE user_id = 1
  );
```

### Authorization denied

```php
// Check gate registration in AppServiceProvider
Gate::policy(Role::class, RolePolicy::class); // Must exist

// Check policy method
// RolePolicy::update() must return true
```

### Missing superadmin role after seed

```bash
# Reseed the role
php artisan db:seed --class=RoleSeeder

# Use tinker to create if needed
php artisan tinker
>>> Role::updateOrCreate(
    ['slug' => 'superadmin'],
    ['name' => 'Superadmin', 'is_superadmin' => true, 'is_active' => true]
)
```

## Related Files

- **Migration**: `database/migrations/2026_04_01_000000_add_is_superadmin_to_roles_table.php`
- **Model Updates**: `app/Models/Role.php`, `app/Models/User.php`
- **Service**: `app/Services/RoleService.php`
- **Policy**: `app/Policies/RolePolicy.php`
- **Seeder**: `database/seeders/RoleSeeder.php`
- **Tests**: `tests/Unit/Models/SuperadminRoleTest.php`
- **Documentation**: `SUPERADMIN_ROLE_IMPLEMENTATION.md`

---

For detailed information, see `SUPERADMIN_ROLE_IMPLEMENTATION.md`
