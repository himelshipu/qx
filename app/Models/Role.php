<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
        'is_superadmin'
    ];

    protected $casts = [
        'is_active'     => 'boolean',
        'is_superadmin' => 'boolean'
    ];

    /**
     * The permissions that belong to the role.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permissions')->withTimestamps();
    }

    /**
     * The users that belong to the role.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_roles')->withTimestamps();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeNonSuperadmin(Builder $query): Builder
    {
        return $query->where('is_superadmin', false);
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        if ($search === '') {
            return $query;
        }

        return $query->where(function (Builder $builder) use ($search): void {
            $builder
                ->where('name', 'like', "%{$search}%")
                ->orWhere('slug', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        });
    }

    /**
     * Check if the role has a specific permission.
     */
    public function hasPermission(string $permissionSlug): bool
    {
        return $this->permissions()->where('slug', $permissionSlug)->exists();
    }

    /**
     * Check if the role is a superadmin role.
     */
    public function isSuperadmin(): bool
    {
        return (bool) $this->is_superadmin;
    }

    /**
     * Give a permission to the role.
     */
    public function givePermissionTo(Permission $permission): void
    {
        if (!$this->hasPermission($permission->slug)) {
            $this->permissions()->attach($permission);
        }
    }

    /**
     * Revoke a permission from the role.
     */
    public function revokePermissionTo(Permission $permission): void
    {
        $this->permissions()->detach($permission);
    }

    /**
     * Check if role is superadmin and protected
     */
    public function isProtected(): bool
    {
        return $this->is_superadmin === true;
    }

    /**
     * Prevent deletion of superadmin role
     */
    protected static function booted(): void
    {
        static::deleting(function (self $role) {
            if ($role->isProtected()) {
                throw new \Exception('Cannot delete superadmin role. It is protected.');
            }
        });
    }

    /**
     * Sync permissions for the role.
     */
    public function syncPermissions(array $permissionIds): void
    {
        $this->permissions()->sync($permissionIds);
    }
}
