<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserRoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = DB::table('roles')->pluck('id', 'slug');
        $users = DB::table('users')->get(['id', 'email', 'user_type']);

        foreach ($users as $user) {
            // Special case: superadmin@rockiesconnect.com gets superadmin role
            if ($user->email === 'superadmin@rockiesconnect.com') {
                $roleSlug = 'superadmin';
            } else {
                // For other users, map based on user_type
                // Note: brand and influencer don't get roles (they don't have dashboard access)
                $roleSlug = match ($user->user_type) {
                    'admin' => 'admin',
                    'moderator' => 'moderator',
                    default => null, // brand and influencer get no role
                };
            }

            if (!$roleSlug) {
                continue; // Skip if no role to assign
            }

            $roleId = $roles[$roleSlug] ?? null;
            if (!$roleId) {
                continue; // Skip if role doesn't exist
            }

            DB::table('user_roles')->updateOrInsert(
                [
                    'user_id' => $user->id,
                    'role_id' => $roleId,
                ],
                [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
