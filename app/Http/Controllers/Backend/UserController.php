<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\User\AssignUserRolesRequest;
use App\Http\Requests\Backend\User\StoreUserRequest;
use App\Http\Requests\Backend\User\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use App\Services\Admin\UserService;
use App\Traits\LogsRbacChanges;
use App\Traits\PermissionChecker;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    use LogsRbacChanges, PermissionChecker;

    public function __construct(
        private readonly UserService $userService
    ) {
    }

    /**
     * Display user list with realtime search and status filtering.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->string('q', ''));
        $status = (string) $request->string('status', 'all');
        $role = trim((string) $request->string('role', ''));

        return view('backend.pages.users.index', $this->userService->getListingPayload($search, $status, $role));
    }

    /**
     * Return only dashboard user table HTML for faster filter updates.
     */
    public function table(Request $request): JsonResponse
    {
        $search = trim((string) $request->string('q', ''));
        $status = (string) $request->string('status', 'all');
        $role = trim((string) $request->string('role', ''));

        $payload = $this->userService->getListingPayload($search, $status, $role);

        $html = view('backend.pages.users._results', [
            'users' => $payload['users'],
        ])->render();

        return response()->json([
            'success' => true,
            'html' => $html,
        ]);
    }

    /**
     * Toggle user active status.
     */
    public function toggleStatus(User $user): JsonResponse
    {
        $isActive = $this->userService->toggleStatus($user);

        return response()->json([
            'success'   => true,
            'message'   => 'User status updated successfully.',
            'is_active' => $isActive
        ]);
    }

    /**
     * Show the form for assigning roles to users.
     */
    public function assignRoles(): View
    {
        return view('backend.pages.users.assign-roles', $this->userService->getAssignRolePayload());
    }

    /**
     * Get user's current roles (AJAX).
     */
    public function getUserRoles(User $user): JsonResponse
    {
        try {
            $roleIds = $this->userService->getUserRoleIds($user);

            return response()->json([
                'success' => true,
                'roleIds' => $roleIds
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Store the user-role assignments.
     */
    public function assignRolesStore(AssignUserRolesRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $user = User::findOrFail((int) $validated['user_id']);
            $roleIds = array_map('intval', $validated['roles'] ?? []);

            $result = $this->userService->assignRoles(
                $user,
                $roleIds,
                (int) auth()->id()
            );

            $this->logRoleSync($user->id, $roleIds);

            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'user'    => [
                    'id'        => $result['user']->id,
                    'name'      => $result['user']->name,
                    'email'     => $result['user']->email,
                    'user_type' => $result['user']->user_type,
                    'roles'     => $result['user']->roles()->pluck('name')->toArray()
                ]
            ]);
        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 403);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): View
    {
        return view('backend.pages.users.create', $this->userService->getCreatePayload());
    }

    /**
     * Store a newly created user with role assignment.
     */
    public function store(StoreUserRequest $request): JsonResponse|RedirectResponse
    {
        try {
            $validated = $request->validated();
            $user = $this->userService->createUser(
                $validated,
                $request->file('profile_image_path'),
                $request->file('cover_image_path')
            );

            // Return based on request type
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'User created successfully with role assigned.',
                    'user'    => [
                        'id'        => $user->id,
                        'name'      => $user->name,
                        'email'     => $user->email,
                        'phone'     => $user->phone,
                        'user_type' => $user->user_type,
                        'roles'     => $user->roles()->pluck('name')->toArray()
                    ]
                ], 201);
            }

            return redirect()
                ->route('dashboard.users.index')
                ->with('success', "User '{$user->name}' created successfully.");

        } catch (\RuntimeException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 403);
            }

            return back()->withErrors(['role_id' => $e->getMessage()])->withInput();
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
            }

            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Show the form for editing a user.
     */
    public function edit(User $user): View
    {
        $roles = $this->userService->getCreatePayload()['roles'];

        $userRoles = $user->roles()->pluck('roles.id')->toArray();

        return view('backend.pages.users.edit', compact('user', 'roles', 'userRoles'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(UpdateUserRequest $request, User $user): JsonResponse|RedirectResponse
    {
        try {
            $validated = $request->validated();

            $user = $this->userService->updateUser(
                $user,
                $validated,
                $request->file('profile_image_path'),
                $request->file('cover_image_path'),
                (int) auth()->id()
            );

            // Return based on request type
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'User updated successfully.',
                    'user'    => [
                        'id'        => $user->id,
                        'name'      => $user->name,
                        'email'     => $user->email,
                        'phone'     => $user->phone,
                        'user_type' => $user->user_type,
                        'roles'     => $user->roles()->pluck('name')->toArray()
                    ]
                ], 200);
            }

            return redirect()
                ->route('dashboard.users.index')
                ->with('success', "User '{$user->name}' updated successfully.");

        } catch (\RuntimeException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 403);
            }

            return back()->withErrors(['role_id' => $e->getMessage()])->withInput();
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
            }

            return back()->with('error', $e->getMessage())->withInput();
        }
    }
}
