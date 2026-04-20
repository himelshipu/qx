<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\AdminMenu;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * Get the dashboard menu for the authenticated user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getDashboardMenu(Request $request): JsonResponse
    {
        $user = auth()->user();

        // Get menus accessible by the user
        $menus = AdminMenu::getForUser($user);

        return response()->json([
            'success' => true,
            'menus' => $menus,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'user_type' => $user->user_type,
                'is_superadmin' => $user->isSuperadmin(),
            ]
        ]);
    }

    /**
     * Get user permissions for rendering UI elements.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getUserPermissions(Request $request): JsonResponse
    {
        $user = auth()->user();

        return response()->json([
            'success' => true,
            'permissions' => $user->getPermissionsForApiResponse(),
            'modules' => $user->getAccessibleModules(),
            'all_permissions' => $user->getAllPermissionsWithStatus(),
        ]);
    }

    /**
     * Check if user has specific permission.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function checkPermission(Request $request): JsonResponse
    {
        $request->validate([
            'permission' => 'required|string',
        ]);

        $user = auth()->user();
        $permission = $request->get('permission');
        $hasPermission = $user->hasPermission($permission);

        return response()->json([
            'success' => true,
            'permission' => $permission,
            'has_permission' => $hasPermission,
        ]);
    }

    /**
     * Check if user can perform action on resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function checkAction(Request $request): JsonResponse
    {
        $request->validate([
            'action' => 'required|string|in:view,create,edit,delete,toggle-status,show,update,store,destroy',
            'resource' => 'required|string',
        ]);

        $user = auth()->user();
        $action = $request->get('action');
        $resource = $request->get('resource');

        $canPerform = $user->canPerformAction($action, $resource);

        return response()->json([
            'success' => true,
            'action' => $action,
            'resource' => $resource,
            'can_perform' => $canPerform,
        ]);
    }
}
