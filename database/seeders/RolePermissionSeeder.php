<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    /**
     * Seed role-permission mappings.
     *
     * IMPORTANT: Only the Superadmin role is assigned all permissions by default.
     * All other roles will have NO permissions initially.
     *
     * Superadmin can then create other roles and assign permissions to them dynamically via the UI:
     * 1. Go to Dashboard > Access Control > Assign Permissions
     * 2. Select a role from dropdown
     * 3. Check/uncheck permissions as needed
     * 4. Save changes
     * 
     * Superadmin Role Protection:
     * - Cannot be deleted
     * - Cannot be edited
     * - Cannot have its role_id changed
     * - Cannot be assigned to other users (except superadmin@rockies.com)
     * - Always has all permissions
     */
    public function run(): void
    {
        // Get the Superadmin role and all permissions
        $superadminRole = DB::table('roles')->where('slug', 'superadmin')->first();

        if (!$superadminRole) {
            return;
        }

        // Get all permissions
        $permissions = DB::table('permissions')->pluck('id');

        // Assign all permissions to Superadmin role
        if ($permissions->count() > 0) {
            foreach ($permissions as $permissionId) {
                DB::table('role_permissions')->updateOrInsert(
                    [
                        'role_id'       => $superadminRole->id,
                        'permission_id' => $permissionId
                    ],
                    [
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );
            }
        }

        // All other roles will be created dynamically by Superadmin and assigned permissions via UI
    }
}
