<?php

namespace App\Traits;

use Illuminate\Auth\Access\AuthorizationException;

/**
 * PermissionChecker Trait
 * 
 * Provides methods for checking user permissions in controllers.
 * 
 * Usage in controller:
 *   use PermissionChecker;
 * 
 *   public function destroy(Item $item)
 *   {
 *       $this->checkPermission('items.destroy');
 *       // or
 *       $this->authorize('items.destroy');
 *   }
 */
trait PermissionChecker
{
    /**
     * Check if the authenticated user has a specific permission
     *
     * @param string $permission
     * @param string|null $message
     * @return void
     * @throws AuthorizationException
     */
    protected function checkPermission(string $permission, ?string $message = null): void
    {
        $user = auth()->user();

        if (!$user) {
            throw new AuthorizationException('Unauthenticated');
        }

        // Superadmin bypasses all permission checks
        if ($user->roles()->where('is_superadmin', true)->exists()) {
            return;
        }

        if (!$user->hasPermission($permission)) {
            throw new AuthorizationException(
                $message ?? "You do not have permission to: {$permission}"
            );
        }
    }

    /**
     * Check multiple permissions (all required)
     *
     * @param array $permissions
     * @param string|null $message
     * @return void
     * @throws AuthorizationException
     */
    protected function checkPermissions(array $permissions, ?string $message = null): void
    {
        foreach ($permissions as $permission) {
            $this->checkPermission($permission);
        }
    }

    /**
     * Check if user has any of the given permissions
     *
     * @param array $permissions
     * @param string|null $message
     * @return void
     * @throws AuthorizationException
     */
    protected function checkPermissionsAny(array $permissions, ?string $message = null): void
    {
        $user = auth()->user();

        if (!$user) {
            throw new AuthorizationException('Unauthenticated');
        }

        // Superadmin bypasses all permission checks
        if ($user->roles()->where('is_superadmin', true)->exists()) {
            return;
        }

        $hasAnyPermission = false;
        foreach ($permissions as $permission) {
            if ($user->hasPermission($permission)) {
                $hasAnyPermission = true;
                break;
            }
        }

        if (!$hasAnyPermission) {
            $permissionList = implode(', ', $permissions);
            throw new AuthorizationException(
                $message ?? "You do not have any of these permissions: {$permissionList}"
            );
        }
    }

    /**
     * Get the authenticated user's permissions
     *
     * @return array
     */
    protected function getUserPermissions(): array
    {
        $user = auth()->user();

        if (!$user) {
            return [];
        }

        return $user->getPermissions();
    }

    /**
     * Check if user can perform an action on a specific resource
     *
     * @param string $action (create, read, update, delete)
     * @param string $resource (items, categories, users, etc.)
     * @return void
     * @throws AuthorizationException
     */
    protected function checkResourcePermission(string $action, string $resource): void
    {
        $permission = "{$resource}.{$action}";
        $this->checkPermission($permission, "You do not have permission to {$action} {$resource}");
    }
}
