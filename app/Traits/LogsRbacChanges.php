<?php

namespace App\Traits;

use App\Models\RbacAuditLog;
use Illuminate\Support\Facades\Request;

trait LogsRbacChanges
{
    /**
     * Log when a role is assigned to a user
     */
    protected function logRoleAssignment(int $targetUserId, int $roleId, string $action = 'assigned'): void
    {
        RbacAuditLog::create([
            'admin_user_id' => auth()->id(),
            'action_type' => $action === 'removed' ? 'role_removed' : 'role_assigned',
            'target_user_id' => $targetUserId,
            'role_id' => $roleId,
            'after_data' => $this->getCurrentUserRoles($targetUserId),
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Log when user roles are synchronized
     */
    protected function logRoleSync(int $targetUserId, array $roleIds): void
    {
        RbacAuditLog::create([
            'admin_user_id' => auth()->id(),
            'action_type' => 'user_roles_synced',
            'target_user_id' => $targetUserId,
            'after_data' => ['role_ids' => $roleIds],
            'description' => 'User roles were synchronized with ' . count($roleIds) . ' role(s)',
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Log when a permission is added to a role
     */
    protected function logPermissionAddition(int $roleId, int $permissionId): void
    {
        RbacAuditLog::create([
            'admin_user_id' => auth()->id(),
            'action_type' => 'permission_added',
            'role_id' => $roleId,
            'permission_id' => $permissionId,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Log when a permission is removed from a role
     */
    protected function logPermissionRemoval(int $roleId, int $permissionId): void
    {
        RbacAuditLog::create([
            'admin_user_id' => auth()->id(),
            'action_type' => 'permission_removed',
            'role_id' => $roleId,
            'permission_id' => $permissionId,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Log when a role is created
     */
    protected function logRoleCreation(int $roleId, array $data = []): void
    {
        RbacAuditLog::create([
            'admin_user_id' => auth()->id(),
            'action_type' => 'role_created',
            'role_id' => $roleId,
            'after_data' => $data,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Log when a role is updated
     */
    protected function logRoleUpdate(int $roleId, array $before = [], array $after = []): void
    {
        RbacAuditLog::create([
            'admin_user_id' => auth()->id(),
            'action_type' => 'role_updated',
            'role_id' => $roleId,
            'before_data' => $before,
            'after_data' => $after,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Log when a role is deleted
     */
    protected function logRoleDeletion(int $roleId, array $data = []): void
    {
        RbacAuditLog::create([
            'admin_user_id' => auth()->id(),
            'action_type' => 'role_deleted',
            'role_id' => $roleId,
            'before_data' => $data,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Get current roles for a user (for logging)
     */
    protected function getCurrentUserRoles(int $userId): array
    {
        $user = \App\Models\User::find($userId);
        
        if (!$user) {
            return [];
        }

        return $user->roles()->get()->map(fn($role) => [
            'id' => $role->id,
            'name' => $role->name,
            'slug' => $role->slug,
        ])->toArray();
    }

    /**
     * Get role data for logging
     */
    protected function getRoleData(int $roleId): array
    {
        $role = \App\Models\Role::find($roleId);
        
        if (!$role) {
            return [];
        }

        return [
            'id' => $role->id,
            'name' => $role->name,
            'slug' => $role->slug,
            'description' => $role->description,
            'is_active' => $role->is_active,
            'permissions_count' => $role->permissions()->count(),
        ];
    }
}
