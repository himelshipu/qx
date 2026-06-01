<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Influencer;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestAccountsSeeder extends Seeder
{
    /**
     * Run the database seeds - add specific test accounts with proper schema
     */
    public function run(): void
    {
        // Ensure SuperAdmin role exists with all permissions
        $this->ensureSuperAdminRole();

        // Create SuperAdmin account (SUPREME LEADER)
        $this->createSuperAdmin();

        // Create test admin account
        $this->createTestAdmin();

        // Create test moderator account
        $this->createTestModerator();

        // Create test brand accounts with profiles
        $this->createTestBrandAccounts();

        // Create test influencer accounts with profiles
        $this->createTestInfluencerAccounts();

        $this->command->info('✅ Test accounts created successfully');
        $this->command->table(
            ['Account Type', 'Email', 'Password', 'Authority Level'],
            [
                ['👑 SuperAdmin', 'superadmin@rockiesconnect.com', 'password', 'Supreme - All Access'],
                ['Admin', 'testadmin@system.local', 'password', 'Full Control'],
                ['Moderator', 'testmoderator@system.local', 'password', 'Moderation'],
                ['Brand 1', 'testbrand1@system.local', 'password', 'Campaign Owner'],
                ['Brand 2', 'testbrand2@system.local', 'password', 'Campaign Owner'],
                ['Influencer 1', 'testinfluencer1@system.local', 'password', 'Creator'],
                ['Influencer 2', 'testinfluencer2@system.local', 'password', 'Creator']
            ]
        );
        $this->command->info('');
        $this->command->warn('⭐ SuperAdmin is the SUPREME LEADER with complete system access');
    }

    /**
     * Ensure SuperAdmin role exists with ALL permissions
     */
    private function ensureSuperAdminRole(): void
    {
        // Check if SuperAdmin role already exists
        $superAdminRole = Role::where('name', 'SuperAdmin')->first();
        if ($superAdminRole) {
            $this->command->info('SuperAdmin role already exists');

            return;
        }

        // Create SuperAdmin role
        $superAdminRole = Role::create([
            'name'       => 'SuperAdmin',
            'guard_name' => 'web'
        ]);

        // Assign ALL permissions to SuperAdmin
        $allPermissions = \App\Models\Permission::all();
        $superAdminRole->permissions()->sync($allPermissions->pluck('id'));

        $this->command->info("✓ SuperAdmin role created with all {$allPermissions->count()} permissions");
    }

    /**
     * Create SuperAdmin account - SUPREME LEADER
     */
    private function createSuperAdmin(): void
    {
        $email = 'superadmin@rockiesconnect.com';

        $user = User::where('email', $email)->first();
        if ($user) {
            $this->command->info("SuperAdmin account already exists: $email");

            return;
        }

        $user = User::create([
            'name'              => 'SuperAdmin',
            'email'             => $email,
            'password'          => Hash::make('password'),
            'slug'              => 'superadmin',
            'user_type'         => 'admin',
            'email_verified_at' => now()
        ]);

        // Assign SuperAdmin role
        $superAdminRole = Role::where('name', 'SuperAdmin')->first();
        if ($superAdminRole) {
            $user->roles()->sync([$superAdminRole->id]);
        }

        $this->command->info("👑 SuperAdmin account created: $email (SUPREME LEADER - Full System Access)");
    }

    /**
     * Create test admin user
     */
    private function createTestAdmin(): void
    {
        $email = 'testadmin@system.local';

        $user = User::where('email', $email)->first();
        if ($user) {
            $this->command->info("Admin account already exists: $email");

            return;
        }

        $user = User::create([
            'name'              => 'Platform Admin',
            'email'             => $email,
            'password'          => Hash::make('password'),
            'slug'              => 'platform-admin',
            'user_type'         => 'admin',
            'email_verified_at' => now()
        ]);

        // Assign admin role
        $adminRole = Role::where('name', 'Administrator')->first();
        if ($adminRole) {
            $user->roles()->sync([$adminRole->id]);
        }

        $this->command->info("✓ Admin account created: $email");
    }

    /**
     * Create test moderator user
     */
    private function createTestModerator(): void
    {
        $email = 'testmoderator@system.local';

        $user = User::where('email', $email)->first();
        if ($user) {
            $this->command->info("Moderator account already exists: $email");

            return;
        }

        $user = User::create([
            'name'              => 'Platform Moderator',
            'email'             => $email,
            'password'          => Hash::make('password'),
            'slug'              => 'platform-moderator',
            'user_type'         => 'moderator',
            'email_verified_at' => now()
        ]);

        // Assign moderator role
        $modRole = Role::where('name', 'Moderator')->first();
        if ($modRole) {
            $user->roles()->sync([$modRole->id]);
        }

        $this->command->info("✓ Moderator account created: $email");
    }

    /**
     * Create test brand accounts with associated brand profiles
     */
    private function createTestBrandAccounts(): void
    {
        $brands = [
            [
                'name'     => 'Urban Fashion Inc',
                'email'    => 'testbrand1@system.local',
                'industry' => 'Fashion'
            ],
            [
                'name'     => 'Wellness & Health Co',
                'email'    => 'testbrand2@system.local',
                'industry' => 'Health & Wellness'
            ]
        ];

        $brandRole = Role::where('name', 'Brand')->first();

        foreach ($brands as $brandData) {
            $user = User::where('email', $brandData['email'])->first();

            if (!$user) {
                $slug = str()->slug($brandData['name']);
                $user = User::create([
                    'name'              => $brandData['name'],
                    'email'             => $brandData['email'],
                    'password'          => Hash::make('password'),
                    'slug'              => $slug,
                    'user_type'         => 'brand',
                    'email_verified_at' => now()
                ]);

                if ($brandRole) {
                    $user->roles()->sync([$brandRole->id]);
                }

                // Create Brand profile
                Brand::create([
                    'user_id'     => $user->id,
                    'brand_name'  => $brandData['name'],
                    'industry'    => $brandData['industry'],
                    'website'     => 'https://' . $slug . '.test',
                    'is_verified' => true
                ]);

                $this->command->info("✓ Brand account created: {$brandData['email']}");
            } else {
                $this->command->info("Brand account already exists: {$brandData['email']}");
            }
        }
    }

    /**
     * Create test influencer accounts with associated influencer profiles
     */
    private function createTestInfluencerAccounts(): void
    {
        $influencers = [
            [
                'name'         => 'Sarah Fashion',
                'email'        => 'testinfluencer1@system.local',
                'display_name' => '@sarah_style'
            ],
            [
                'name'         => 'Marcus Fitness',
                'email'        => 'testinfluencer2@system.local',
                'display_name' => '@marcus_fit'
            ]
        ];

        $influencerRole = Role::where('name', 'Influencer')->first();

        foreach ($influencers as $infData) {
            $user = User::where('email', $infData['email'])->first();

            if (!$user) {
                $slug = str()->slug($infData['name']);
                $user = User::create([
                    'name'              => $infData['name'],
                    'email'             => $infData['email'],
                    'password'          => Hash::make('password'),
                    'slug'              => $slug,
                    'user_type'         => 'influencer',
                    'email_verified_at' => now()
                ]);

                if ($influencerRole) {
                    $user->roles()->sync([$influencerRole->id]);
                }

                // Create Influencer profile
                Influencer::create([
                    'user_id'      => $user->id,
                    'display_name' => $infData['display_name'],
                    'is_active'    => true
                ]);

                $this->command->info("✓ Influencer account created: {$infData['email']}");
            } else {
                $this->command->info("Influencer account already exists: {$infData['email']}");
            }
        }
    }
}
