<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminMenu extends Model
{
    protected $table = 'admin_menus';

    protected $fillable = [
        'label',
        'icon',
        'route',
        'permission',
        'module',
        'parent_id',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Get child menu items.
     */
    public function children()
    {
        return $this->hasMany(AdminMenu::class, 'parent_id')->orderBy('order');
    }

    /**
     * Get parent menu item.
     */
    public function parent()
    {
        return $this->belongsTo(AdminMenu::class, 'parent_id');
    }

    /**
     * Get the permission associated with this menu.
     */
    public function permission()
    {
        return $this->belongsTo(Permission::class, 'permission');
    }

    /**
     * Scope to get only active menus.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get only top-level menus (no parent).
     */
    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Get menu filtered by user permissions.
     *
     * @param $query
     * @param User $user The user to filter permissions for
     * @return mixed
     */
    public function scopeForUser($query, User $user)
    {
        // If superadmin, return all
        if ($user->isSuperadmin()) {
            return $query->active()->with('children');
        }

        // Otherwise, filter by permission
        return $query->active()
            ->where(function ($q) use ($user) {
                // Include items without permission requirement
                $q->whereNull('permission')
                    // Or include items where user has the permission
                    ->orWhere(function ($q2) use ($user) {
                        $q2->whereNotNull('permission')
                            ->whereHas('permission', function ($q3) use ($user) {
                                $q3->whereHas('roles', function ($q4) use ($user) {
                                    $q4->whereIn('role_id', 
                                        $user->roles()->pluck('roles.id')
                                    );
                                });
                            });
                    });
            })
            ->with(['children' => function ($query) use ($user) {
                $query->forUser($user);
            }]);
    }

    /**
     * Get visible menus for user as array.
     *
     * @param User $user
     * @return array
     */
    public static function getForUser(User $user): array
    {
        $menus = self::topLevel()
            ->forUser($user)
            ->get()
            ->toArray();

        return array_map(function ($menu) {
            return [
                'id' => $menu['id'],
                'label' => $menu['label'],
                'icon' => $menu['icon'],
                'route' => $menu['route'],
                'permission' => $menu['permission'],
                'children' => $menu['children'] ?? [],
            ];
        }, $menus);
    }
}
