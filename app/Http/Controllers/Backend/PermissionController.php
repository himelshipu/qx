<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Traits\LogsRbacChanges;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PermissionController extends Controller
{
    use LogsRbacChanges;
    /**
     * Display a listing of the permissions.
     */
    public function index()
    {
        $permissions = Permission::with('roles')
            ->orderBy('module')
            ->orderBy('name')
            ->paginate(20);
        
        $groupedPermissions = Permission::with('roles')
            ->where('is_active', true)
            ->orderBy('module')
            ->orderBy('name')
            ->get()
            ->groupBy('module');
        
        return view('backend.pages.permissions.index', compact('permissions', 'groupedPermissions'));
    }

    /**
     * Show the form for creating a new permission.
     */
    public function create()
    {
        return view('backend.pages.permissions.create');
    }

    /**
     * Store a newly created permission in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name'],
            'description' => ['nullable', 'string'],
            'module' => ['required', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ]);

        Permission::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'module' => $request->module,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('dashboard.permissions.index')
            ->with('success', 'Permission created successfully.');
    }

    /**
     * Display the specified permission.
     */
    public function show(string $id)
    {
        $permission = Permission::with('roles')->findOrFail($id);
        
        return view('backend.pages.permissions.show', compact('permission'));
    }

    /**
     * Show the form for editing the specified permission.
     */
    public function edit(string $id)
    {
        $permission = Permission::findOrFail($id);
        
        return view('backend.pages.permissions.edit', compact('permission'));
    }

    /**
     * Update the specified permission in storage.
     */
    public function update(Request $request, string $id)
    {
        $permission = Permission::findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name,'.$permission->id],
            'description' => ['nullable', 'string'],
            'module' => ['required', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ]);

        $permission->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'module' => $request->module,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('dashboard.permissions.index')
            ->with('success', 'Permission updated successfully.');
    }

    /**
     * Remove the specified permission from storage.
     */
    public function destroy(string $id)
    {
        $permission = Permission::findOrFail($id);
        
        // Detach from all roles first
        $permission->roles()->detach();
        
        $permission->delete();

        return redirect()->route('dashboard.permissions.index')
            ->with('success', 'Permission deleted successfully.');
    }

    /**
     * Toggle permission status.
     */
    public function toggleStatus(Request $request, string $id)
    {
        $permission = Permission::findOrFail($id);
        $permission->update(['is_active' => !$permission->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully.',
            'is_active' => $permission->is_active
        ]);
    }

    /**
     * Show the form for assigning permissions to roles.
     */
    public function assign()
    {
        $roles = Role::with('permissions')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        $permissions = Permission::where('is_active', true)
            ->orderBy('module')
            ->orderBy('name')
            ->get()
            ->groupBy('module');
        
        return view('backend.pages.permissions.assign', compact('roles', 'permissions'));
    }

    /**
     * Store the role-permission assignments.
     */
    public function assignStore(Request $request)
    {
        $request->validate([
            'role_id' => ['required', 'exists:roles,id'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);

        $role = Role::findOrFail($request->role_id);
        
        // Get current permissions for audit trail
        $previousPermissions = $role->permissions()->pluck('id')->toArray();
        
        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        } else {
            $role->syncPermissions([]);
        }

        // Log permission changes
        $newPermissions = $request->permissions ?? [];
        
        // Log removed permissions
        $removedPermissions = array_diff($previousPermissions, $newPermissions);
        foreach ($removedPermissions as $permissionId) {
            $this->logPermissionRemoval($role->id, $permissionId);
        }
        
        // Log added permissions
        $addedPermissions = array_diff($newPermissions, $previousPermissions);
        foreach ($addedPermissions as $permissionId) {
            $this->logPermissionAddition($role->id, $permissionId);
        }

        return redirect()->route('dashboard.permissions.assign')
            ->with('success', 'Permissions assigned to role successfully.');
    }
}
