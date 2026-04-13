<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Traits\LogsRbacChanges;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    use LogsRbacChanges {
        getRoleData as protected getRoleDataForAudit;
    }
    /**
     * Display a listing of the roles.
     */
    public function index()
    {
        $roles = Role::with('permissions')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('backend.pages.roles.index', compact('roles'));
    }

    /**
     * Store a newly created role in storage (AJAX).
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name'      => ['required', 'string', 'max:255', 'unique:roles,name'],
                'description' => ['nullable', 'string', 'max:2000'],
                'is_active' => ['boolean']
            ]);

            $role = Role::create([
                'name'      => $validated['name'],
                'slug'      => Str::slug($validated['name']),
                'description' => $validated['description'] ?? null,
                'is_active' => $request->boolean('is_active', true)
            ]);

            // Log role creation
            $this->logRoleCreation($role->id, $this->getRoleAuditData($role->id));

            return response()->json([
                'success' => true,
                'message' => 'Role created successfully.',
                'role'    => $role
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified role in storage (AJAX).
     */
    public function update(Request $request, string $id)
    {
        try {
            $role = Role::findOrFail($id);

            // Prevent editing superadmin role
            if ($role->isProtected()) {
                return response()->json([
                    'success' => false,
                    'message' => 'The superadmin role is protected and cannot be edited.'
                ], 403);
            }

            $validated = $request->validate([
                'name'      => ['required', 'string', 'max:255', 'unique:roles,name,' . $role->id],
                'description' => ['nullable', 'string', 'max:2000'],
                'is_active' => ['boolean']
            ]);

            // Get before data for audit trail
            $beforeData = $this->getRoleAuditData($role->id);

            $role->update([
                'name'      => $validated['name'],
                'slug'      => Str::slug($validated['name']),
                'description' => $validated['description'] ?? null,
                'is_active' => $request->boolean('is_active', true)
            ]);

            // Log role update
            $afterData = $this->getRoleAuditData($role->id);
            $this->logRoleUpdate($role->id, $beforeData, $afterData);

            return response()->json([
                'success' => true,
                'message' => 'Role updated successfully.',
                'role'    => $role
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified role from storage (AJAX - Soft Delete).
     */
    public function destroy(string $id)
    {
        try {
            $role = Role::findOrFail($id);

            // Prevent deleting superadmin role
            if ($role->isProtected()) {
                return response()->json([
                    'success' => false,
                    'message' => 'The superadmin role is protected and cannot be deleted.'
                ], 403);
            }

            // Check if role has users
            if ($role->users()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete this role. It is assigned to ' . $role->users()->count() . ' user(s).'
                ], 422);
            }

            $role->delete();

            return response()->json([
                'success' => true,
                'message' => 'Role deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle role status (AJAX).
     */
    public function toggleStatus(Request $request, string $id)
    {
        try {
            $role = Role::findOrFail($id);

            // Prevent toggling superadmin role status
            if ($role->isProtected()) {
                return response()->json([
                    'success' => false,
                    'message' => 'The superadmin role is protected and its status cannot be toggled.'
                ], 403);
            }

            $role->update(['is_active' => !$role->is_active]);

            return response()->json([
                'success'   => true,
                'message'   => 'Role status updated successfully.',
                'is_active' => $role->is_active
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get role data for edit modal (AJAX).
     */
    public function getRoleData(string $id)
    {
        try {
            $role = Role::findOrFail($id);

            return response()->json([
                'success'   => true,
                'role'      => $role,
                'protected' => $role->isProtected()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Get permission names for a role (AJAX).
     */
    public function getPermissions(string $id)
    {
        try {
            $role        = Role::findOrFail($id);
            $permissions = $role->permissions()->pluck('slug')->toArray();

            return response()->json([
                'success'     => true,
                'permissions' => $permissions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Build role payload for audit logs without colliding with the public JSON endpoint.
     */
    private function getRoleAuditData(int $roleId): array
    {
        return $this->getRoleDataForAudit($roleId);
    }
}
