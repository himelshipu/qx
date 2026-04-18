<?php

namespace App\Traits;

use App\Models\Permission;

trait HasPermissionsHelper
{
    /**
     * Check if the user has a specific permission.
     *
     * @param string $permissionSlug The permission slug to check
     * @return bool
     */
    public function canDo(string $permissionSlug): bool
    {
        return $this->hasPermission($permissionSlug);
    }

    /**
     * Check if the user has ANY of the given permissions.
     *
     * @param array $permissionSlugs Array of permission slugs
     * @return bool
     */
    public function canDoAny(array $permissionSlugs): bool
    {
        foreach ($permissionSlugs as $slug) {
            if ($this->hasPermission($slug)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if the user has ALL of the given permissions.
     *
     * @param array $permissionSlugs Array of permission slugs
     * @return bool
     */
    public function canDoAll(array $permissionSlugs): bool
    {
        foreach ($permissionSlugs as $slug) {
            if (!$this->hasPermission($slug)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Check if the user can access a specific module.
     *
     * @param string $module The module name
     * @return bool
     */
    public function canAccessModule(string $module): bool
    {
        return Permission::where('module', $module)
            ->whereHas('roles', function ($query) {
                $query->whereIn('role_id', $this->roles()->pluck('role_id'));
            })
            ->exists();
    }

    /**
     * Get all modules the user can access.
     *
     * @return array Array of accessible module names
     */
    public function getAccessibleModules(): array
    {
        return Permission::whereHas('roles', function ($query) {
            $query->whereIn('role_id', $this->roles()->pluck('role_id'));
        })
            ->distinct('module')
            ->pluck('module')
            ->toArray();
    }

    /**
     * Get all permissions for the user with their status.
     *
     * @return array Array of permissions with can_do flag
     */
    public function getAllPermissionsWithStatus(): array
    {
        $allPermissions = Permission::where('is_active', true)->get();
        $userPermissions = array();

        foreach ($allPermissions as $permission) {
            $userPermissions[] = [
                'id' => $permission->id,
                'slug' => $permission->slug,
                'name' => $permission->name,
                'module' => $permission->module,
                'can_do' => $this->hasPermission($permission->slug),
            ];
        }

        return $userPermissions;
    }

    /**
     * Get permissions grouped by module.
     *
     * @return array Array of permissions grouped by module
     */
    public function getPermissionsByModule(): array
    {
        $permissions = $this->getAllPermissionsWithStatus();
        $grouped = [];

        foreach ($permissions as $permission) {
            $module = $permission['module'];
            if (!isset($grouped[$module])) {
                $grouped[$module] = [];
            }
            $grouped[$module][] = $permission;
        }

        return $grouped;
    }

    /**
     * Get user's permission in API response format.
     *
     * Useful for frontend to decide what to render/show.
     *
     * @return array
     */
    public function getPermissionsForApiResponse(): array
    {
        $modules = $this->getAccessibleModules();

        // Get key permissions for common actions
        $keyPermissions = [
            'can_view_dashboard' => $this->canDo('dashboard.view'),
            'can_view_analytics' => $this->canDoAny(['orders.index', 'payments.index']),
            'can_manage_users' => $this->canDoAny(['users.create', 'users.edit', 'users.update', 'users.toggle-status']),
            'can_manage_roles' => $this->canDoAny(['roles.store', 'roles.update', 'roles.destroy']),
            'can_manage_permissions' => $this->canDo('permissions.assign'),
            'can_manage_content' => $this->canAccessModule('content'),
            'can_manage_campaigns' => $this->canAccessModule('campaigns'),
            'can_manage_orders' => $this->canAccessModule('orders'),
            'can_manage_brands' => $this->canAccessModule('brands'),
            'can_manage_influencers' => $this->canAccessModule('influencers'),
            'can_manage_support' => $this->canAccessModule('support'),
            'can_manage_settings' => $this->canDoAny(['settings.index', 'settings.update', 'settings.update-order']),
            'can_moderate_content' => $this->canDoAny(['reviews.toggle-visibility', 'support-tickets.update', 'conversations.assign-moderator']),
            'can_view_reports' => $this->canDoAny(['orders.index', 'payment-statement.index', 'payments.index']),
        ];

        return [
            'accessible_modules' => $modules,
            'key_permissions' => $keyPermissions,
            'user_type' => $this->user_type,
            'is_superadmin' => $this->isSuperadmin(),
        ];
    }

    /**
     * Check if user can perform CRUD action on a resource.
     *
     * @param string $action The action: view, create, edit, delete, toggle-status
     * @param string $resource The resource name: roles, users, brands, etc.
     * @return bool
     */
    public function canPerformAction(string $action, string $resource): bool
    {
        $permissionSlug = "{$resource}.{$action}";
        return $this->hasPermission($permissionSlug);
    }
}
