<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RbacAuditLog extends Model
{
    protected $table = 'rbac_audit_logs';

    protected $fillable = [
        'admin_user_id',
        'action_type',
        'target_user_id',
        'role_id',
        'permission_id',
        'before_data',
        'after_data',
        'description',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'before_data' => 'array',
        'after_data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the admin user who made the change
     */
    public function adminUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }

    /**
     * Get the target user (if applicable)
     */
    public function targetUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }

    /**
     * Get the role (if applicable)
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the permission (if applicable)
     */
    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class);
    }

    /**
     * Scope to get recent logs
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope to filter by action type
     */
    public function scopeByActionType($query, $actionType)
    {
        return $query->where('action_type', $actionType);
    }

    /**
     * Scope to filter by admin user
     */
    public function scopeByAdminUser($query, $userId)
    {
        return $query->where('admin_user_id', $userId);
    }

    /**
     * Scope to filter by target user
     */
    public function scopeByTargetUser($query, $userId)
    {
        return $query->where('target_user_id', $userId);
    }

    /**
     * Get human-readable action description
     */
    public function getActionLabelAttribute(): string
    {
        $labels = [
            'role_assigned' => 'Role Assigned',
            'role_removed' => 'Role Removed',
            'permission_added' => 'Permission Added',
            'permission_removed' => 'Permission Removed',
            'role_created' => 'Role Created',
            'role_updated' => 'Role Updated',
            'role_deleted' => 'Role Deleted',
            'user_roles_synced' => 'User Roles Synchronized',
        ];

        return $labels[$this->action_type] ?? ucfirst(str_replace('_', ' ', $this->action_type));
    }

    /**
     * Get a summary of the change
     */
    public function getSummaryAttribute(): string
    {
        $admin = $this->adminUser?->name ?? 'Unknown Admin';

        return match ($this->action_type) {
            'role_assigned' => "{$admin} assigned {$this->role?->name} to {$this->targetUser?->name}",
            'role_removed' => "{$admin} removed {$this->role?->name} from {$this->targetUser?->name}",
            'permission_added' => "{$admin} added {$this->permission?->name} to {$this->role?->name}",
            'permission_removed' => "{$admin} removed {$this->permission?->name} from {$this->role?->name}",
            'role_created' => "{$admin} created role {$this->role?->name}",
            'role_updated' => "{$admin} updated role {$this->role?->name}",
            'role_deleted' => "{$admin} deleted role {$this->role?->name}",
            'user_roles_synced' => "{$admin} synchronized roles for {$this->targetUser?->name}",
            default => $this->description ?? 'RBAC Change',
        };
    }
}
