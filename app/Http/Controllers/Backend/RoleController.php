<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    /**
     * Display a listing of the roles.
     */
    public function index()
    {
        $roles = Role::with('permissions')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('backend.pages.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new role.
     */
    public function create()
    {
        $permissions = Permission::where('is_active', true)
            ->orderBy('module')
            ->orderBy('name')
            ->get()
            ->groupBy('module');
        
        return view('backend.pages.roles.create', compact('permissions'));
    }

    /**
     * Store a newly created role in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'description' => ['nullable', 'string'],
            'permissions' => ['nullable', 'array'],
            'is_active' => ['boolean'],
        ]);

        $role = Role::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'is_active' => $request->boolean('is_active', true),
        ]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('dashboard.roles.index')
            ->with('success', 'Role created successfully.');
    }

    /**
     * Display the specified role.
     */
    public function show(string $id)
    {
        $role = Role::with('permissions', 'users')->findOrFail($id);
        
        return view('backend.pages.roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(string $id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        
        $permissions = Permission::where('is_active', true)
            ->orderBy('module')
            ->orderBy('name')
            ->get()
            ->groupBy('module');
        
        return view('backend.pages.roles.edit', compact('role', 'permissions'));
    }

    /**
     * Update the specified role in storage.
     */
    public function update(Request $request, string $id)
    {
        $role = Role::findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name,'.$role->id],
            'description' => ['nullable', 'string'],
            'permissions' => ['nullable', 'array'],
            'is_active' => ['boolean'],
        ]);

        $role->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'is_active' => $request->boolean('is_active', true),
        ]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        } else {
            $role->syncPermissions([]);
        }

        return redirect()->route('dashboard.roles.index')
            ->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified role from storage.
     */
    public function destroy(string $id)
    {
        $role = Role::findOrFail($id);
        
        // Check if role has users
        if ($role->users()->count() > 0) {
            return redirect()->route('dashboard.roles.index')
                ->with('error', 'Cannot delete role. It has assigned users.');
        }

        $role->delete();

        return redirect()->route('dashboard.roles.index')
            ->with('success', 'Role deleted successfully.');
    }

    /**
     * Toggle role status.
     */
    public function toggleStatus(Request $request, string $id)
    {
        $role = Role::findOrFail($id);
        $role->update(['is_active' => !$role->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully.',
            'is_active' => $role->is_active
        ]);
    }

    /**
     * Get role permissions for AJAX request.
     */
    public function getPermissions(string $id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        
        return response()->json([
            'permissions' => $role->permissions->pluck('id')->toArray()
        ]);
    }
}
