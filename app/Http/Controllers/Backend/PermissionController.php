<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Permission\AssignRolePermissionsRequest;
use App\Http\Requests\Backend\Permission\StorePermissionRequest;
use App\Http\Requests\Backend\Permission\UpdatePermissionRequest;
use App\Services\Admin\PermissionService;
use App\Services\Admin\PermissionAssignmentService;
use App\Traits\LogsRbacChanges;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    use LogsRbacChanges;

    public function __construct(
        private readonly PermissionService $permissionService,
        private readonly PermissionAssignmentService $permissionAssignmentService
    ) {
    }
    /**
     * Display a listing of the permissions.
     */
    public function index()
    {
        return view('backend.pages.permissions.index', $this->permissionService->getIndexPayload());
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
    public function store(StorePermissionRequest $request)
    {
        $this->permissionService->createPermission(
            $request->validated(),
            $request->boolean('is_active', true)
        );

        return redirect()->route('dashboard.permissions.index')
            ->with('success', 'Permission created successfully.');
    }

    /**
     * Display the specified permission.
     */
    public function show(string $id)
    {
        $permission = $this->permissionService->getPermissionById((int) $id);
        
        return view('backend.pages.permissions.show', compact('permission'));
    }

    /**
     * Show the form for editing the specified permission.
     */
    public function edit(string $id)
    {
        $permission = $this->permissionService->getPermissionById((int) $id);
        
        return view('backend.pages.permissions.edit', compact('permission'));
    }

    /**
     * Update the specified permission in storage.
     */
    public function update(UpdatePermissionRequest $request, string $id)
    {
        $this->permissionService->updatePermission(
            (int) $id,
            $request->validated(),
            $request->boolean('is_active', true)
        );

        return redirect()->route('dashboard.permissions.index')
            ->with('success', 'Permission updated successfully.');
    }

    /**
     * Remove the specified permission from storage.
     */
    public function destroy(string $id)
    {
        $this->permissionService->deletePermission((int) $id);

        return redirect()->route('dashboard.permissions.index')
            ->with('success', 'Permission deleted successfully.');
    }

    /**
     * Toggle permission status.
     */
    public function toggleStatus(Request $request, string $id)
    {
        $isActive = $this->permissionService->toggleStatus((int) $id);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully.',
            'is_active' => $isActive
        ]);
    }

    /**
     * Show the form for assigning permissions to roles.
     */
    public function assign()
    {
        return view('backend.pages.permissions.assign', $this->permissionAssignmentService->getAssignPayload());
    }

    /**
     * Store the role-permission assignments.
     */
    public function assignStore(AssignRolePermissionsRequest $request)
    {
        $validated = $request->validated();

        $result = $this->permissionAssignmentService->assignPermissions(
            (int) $validated['role_id'],
            array_map('intval', $validated['permissions'] ?? [])
        );

        $previousPermissions = $result['previousPermissions'];
        $newPermissions = $result['newPermissions'];

        // Log removed permissions
        $removedPermissions = array_diff($previousPermissions, $newPermissions);
        foreach ($removedPermissions as $permissionId) {
            $this->logPermissionRemoval($result['role']->id, $permissionId);
        }
        
        // Log added permissions
        $addedPermissions = array_diff($newPermissions, $previousPermissions);
        foreach ($addedPermissions as $permissionId) {
            $this->logPermissionAddition($result['role']->id, $permissionId);
        }

        return redirect()->route('dashboard.permissions.assign')
            ->with('success', 'Permissions assigned to role successfully.');
    }
}
