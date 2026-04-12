<?php

namespace Tests\Unit\Models;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperadminRoleTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Test that superadmin role can be created.
     */
    public function test_superadmin_role_can_be_created(): void
    {
        $role = Role::create([
            'name'          => 'Test Superadmin',
            'slug'          => 'test-superadmin',
            'description'   => 'Test superadmin role',
            'is_active'     => true,
            'is_superadmin' => true
        ]);

        $this->assertTrue($role->isSuperadmin());
        $this->assertDatabaseHas('roles', [
            'slug'          => 'test-superadmin',
            'is_superadmin' => true
        ]);
    }

    /**
     * Test that regular role is not a superadmin.
     */
    public function test_regular_role_is_not_superadmin(): void
    {
        $role = Role::create([
            'name'          => 'Regular Role',
            'slug'          => 'regular-role',
            'description'   => 'Regular role',
            'is_active'     => true,
            'is_superadmin' => false
        ]);

        $this->assertFalse($role->isSuperadmin());
    }

    /**
     * Test that superadmin role is marked correctly.
     */
    public function test_superadmin_role_is_marked_correctly(): void
    {
        // Create superadmin role since it might not be seeded in tests
        $role = Role::first(); // Get any role or create if needed
        if (!$role) {
            $role = Role::create([
                'name'          => 'Superadmin',
                'slug'          => 'superadmin',
                'description'   => 'Supreme administrator with complete platform control.',
                'is_active'     => true,
                'is_superadmin' => true
            ]);
        }

        $this->assertNotNull($role);
        $this->assertTrue($role->isSuperadmin());
    }

    /**
     * Test that user with superadmin role can be identified.
     */
    public function test_user_with_superadmin_role(): void
    {
        // Create a superadmin role
        $superadminRole = Role::create([
            'name'          => 'Admin',
            'slug'          => 'admin-test',
            'description'   => 'Admin role',
            'is_active'     => true,
            'is_superadmin' => true
        ]);

        // Create a user
        $user = User::create([
            'name'      => 'Test User',
            'email'     => 'test@example.com',
            'password'  => bcrypt('password'),
            'user_type' => 'brand'
        ]);

        // Assign superadmin role
        $user->assignRole($superadminRole);

        // Verify the user has superadmin role
        $this->assertTrue($user->hasSuperadminRole());
        $this->assertTrue($user->isSuperadmin());
    }

    /**
     * Test user without superadmin role.
     */
    public function test_user_without_superadmin_role(): void
    {
        // Create regular role
        $regularRole = Role::create([
            'name'          => 'Editor',
            'slug'          => 'editor-test',
            'description'   => 'Editor role',
            'is_active'     => true,
            'is_superadmin' => false
        ]);

        // Create user
        $user = User::create([
            'name'      => 'Test User 2',
            'email'     => 'test2@example.com',
            'password'  => bcrypt('password'),
            'user_type' => 'influencer'
        ]);

        // Assign regular role
        $user->assignRole($regularRole);

        // Verify user does not have superadmin role
        $this->assertFalse($user->hasSuperadminRole());
        $this->assertFalse($user->isSuperadmin());
    }

    /**
     * Test user with multiple roles including superadmin.
     */
    public function test_user_with_multiple_roles_including_superadmin(): void
    {
        // Create roles
        $superadminRole = Role::create([
            'name'          => 'Super',
            'slug'          => 'super-test',
            'is_superadmin' => true
        ]);

        $editorRole = Role::create([
            'name'          => 'Editor',
            'slug'          => 'editor-test2',
            'is_superadmin' => false
        ]);

        // Create user
        $user = User::create([
            'name'      => 'Test User 3',
            'email'     => 'test3@example.com',
            'password'  => bcrypt('password'),
            'user_type' => 'brand'
        ]);

        // Assign both roles
        $user->assignRole($superadminRole);
        $user->assignRole($editorRole);

        // Verify user has superadmin
        $this->assertTrue($user->hasSuperadminRole());
        $this->assertTrue($user->isSuperadmin());
        $this->assertTrue($user->hasRole('super-test'));
        $this->assertTrue($user->hasRole('editor-test2'));
    }

    /**
     * Test getting superadmin roles for a user.
     */
    public function test_get_superadmin_roles_for_user(): void
    {
        // Create roles
        $superadminRole1 = Role::create([
            'name'          => 'Super1',
            'slug'          => 'super1-test',
            'is_superadmin' => true
        ]);

        $superadminRole2 = Role::create([
            'name'          => 'Super2',
            'slug'          => 'super2-test',
            'is_superadmin' => true
        ]);

        // Create user
        $user = User::create([
            'name'      => 'Test User 4',
            'email'     => 'test4@example.com',
            'password'  => bcrypt('password'),
            'user_type' => 'brand'
        ]);

        // Assign roles
        $user->assignRole($superadminRole1);
        $user->assignRole($superadminRole2);

        // Get superadmin roles
        $superadminRoles = $user->getSuperadminRoles();

        $this->assertCount(2, $superadminRoles);
    }
}
