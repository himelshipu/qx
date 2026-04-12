<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Seed the roles table.
     *
     * IMPORTANT: Only Superadmin role is created by seeder.
     * All other roles (Admin, Moderator, etc.) are created dynamically by Superadmin.
     *
     * Brand and Influencer are NOT roles - they are user_type values in the users table.
     */
    public function run(): void
    {
        // Only create Superadmin role
        DB::table('roles')->updateOrInsert(
            ['slug' => 'superadmin'],
            [
                'name'          => 'Superadmin',
                'slug'          => 'superadmin',
                'description'   => 'Supreme administrator with complete platform control.',
                'is_active'     => true,
                'is_superadmin' => true,
                'created_at'    => now(),
                'updated_at'    => now()
            ]
        );
    }
}
