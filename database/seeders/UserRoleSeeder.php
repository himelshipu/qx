<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserRoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = DB::table('roles')->pluck('id', 'slug');
        $users = DB::table('users')->get(['id', 'user_type']);

        foreach ($users as $user) {
            $roleSlug = match ($user->user_type) {
                'admin' => 'admin',
                'moderator' => 'moderator',
                'influencer' => 'influencer',
                default => 'brand',
            };

            $roleId = $roles[$roleSlug] ?? null;
            if (! $roleId) {
                continue;
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
