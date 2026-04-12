<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Traits\LogsRbacChanges;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    use LogsRbacChanges;
    /**
     * Display user list with realtime search and status filtering.
     */
    public function index(Request $request): View
    {
        $search     = trim((string) $request->string('q', ''));
        $status     = (string) $request->string('status', 'all');
        $roleFilter = trim((string) $request->string('role', ''));

        $usersQuery = User::query()
            ->with([
                'brand:id,user_id',
                'influencer:id,user_id',
                'roles:id,name'
            ])
            ->whereIn('user_type', ['brand', 'influencer', 'moderator', 'admin'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('phone', 'like', '%' . $search . '%')
                        ->orWhere('city', 'like', '%' . $search . '%')
                        ->orWhere('country', 'like', '%' . $search . '%')
                        ->orWhere('user_type', 'like', '%' . $search . '%');
                });
            })
            ->when($status === 'active', fn($query) => $query->where('is_active', true))
            ->when($status === 'inactive', fn($query) => $query->where('is_active', false))
            ->when($roleFilter !== '', function ($query) use ($roleFilter) {
                $query->whereHas('roles', function ($roleQuery) use ($roleFilter) {
                    $roleQuery->where('roles.id', $roleFilter);
                });
            })
            ->orderByDesc('updated_at');

        $users = $usersQuery->paginate(12)->withQueryString();

        $stats = [
            'total'       => User::whereIn('user_type', ['brand', 'influencer', 'moderator', 'admin'])->count(),
            'brands'      => User::where('user_type', 'brand')->count(),
            'influencers' => User::where('user_type', 'influencer')->count(),
            'moderators'  => User::where('user_type', 'moderator')->count(),
            'admins'      => User::where('user_type', 'admin')->count(),
            'active'      => User::whereIn('user_type', ['brand', 'influencer', 'moderator', 'admin'])->where('is_active', true)->count(),
            'inactive'    => User::whereIn('user_type', ['brand', 'influencer', 'moderator', 'admin'])->where('is_active', false)->count()
        ];

        // Get all active roles for the filter dropdown
        $roles = Role::where('is_active', true)
            ->where('is_superadmin', false)
            ->orderBy('name')
            ->get();

        $payload = [
            'users'  => $users,
            'stats'  => $stats,
            'search' => $search,
            'status' => $status,
            'role'   => $roleFilter,
            'roles'  => $roles
        ];

        if ($request->ajax()) {
            return view('backend.pages.users._results', $payload);
        }

        return view('backend.pages.users.index', $payload);
    }

    /**
     * Toggle user active status.
     */
    public function toggleStatus(User $user): JsonResponse
    {
        $user->update([
            'is_active' => !$user->is_active
        ]);

        return response()->json([
            'success'   => true,
            'message'   => 'User status updated successfully.',
            'is_active' => $user->is_active
        ]);
    }

    /**
     * Show the form for assigning roles to users.
     */
    public function assignRoles(): View
    {
        // Only show moderator and admin users (exclude brand and influencer)
        $users = User::whereNotIn('user_type', ['brand', 'influencer'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $roles = Role::where('is_active', true)
            ->where('is_superadmin', false) // Don't show superadmin role in the list to prevent accidental assignment
            ->withCount('permissions')
            ->orderBy('name')
            ->get();

        return view('backend.pages.users.assign-roles', compact('users', 'roles'));
    }

    /**
     * Get user's current roles (AJAX).
     */
    public function getUserRoles(User $user): JsonResponse
    {
        try {
            $roleIds = $user->roles()->pluck('roles.id')->toArray();

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
    public function assignRolesStore(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'user_id' => ['required', 'exists:users,id'],
                'roles'   => ['nullable', 'array'],
                'roles.*' => ['exists:roles,id']
            ]);

            $user = User::findOrFail($request->user_id);

            // Prevent modification of superadmin users
            if ($user->isSuperadmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot modify superadmin user roles.'
                ], 403);
            }

            // Prevent current user from removing own dashboard access
            if ($user->id === auth()->id() && $request->has('roles') && is_array($request->roles)) {
                $hasDashboardAccess = Role::whereIn('id', $request->roles)
                    ->whereHas('permissions', fn($q) => $q->where('slug', 'dashboard.view'))
                    ->exists();

                if (!$hasDashboardAccess) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You cannot remove your own dashboard access.'
                    ], 403);
                }
            }

            if ($request->has('roles') && is_array($request->roles)) {
                // Prevent assignment of superadmin roles
                $superadminRoles = Role::where('is_superadmin', true)->pluck('id')->toArray();
                $attemptedRoles  = array_intersect($request->roles, $superadminRoles);

                if (!empty($attemptedRoles)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot assign superadmin roles through this interface.'
                    ], 403);
                }

                // Get the primary role (first role) to set as user_type
                $role = Role::findOrFail($request->roles[0]);

                $user->roles()->sync($request->roles);
                // Sync user_type with the primary role name
                $user->update(['user_type' => strtolower($role->name)]);
                
                // Log the role sync
                $this->logRoleSync($user->id, $request->roles);
                
                $message = 'Roles assigned to user successfully.';
            } else {
                $user->roles()->sync([]);
                // Log empty roles assignment
                $this->logRoleSync($user->id, []);
                $message = 'All roles removed from user.';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'user'    => [
                    'id'        => $user->id,
                    'name'      => $user->name,
                    'email'     => $user->email,
                    'user_type' => $user->user_type,
                    'roles'     => $user->roles()->pluck('name')->toArray()
                ]
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
     * Show the form for creating a new user.
     */
    public function create(): View
    {
        $roles = Role::where('is_active', true)
            ->where('is_superadmin', false)
            ->orderBy('name')
            ->get();

        return view('backend.pages.users.create', compact('roles'));
    }

    /**
     * Store a newly created user with role assignment.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name'               => ['required', 'string', 'max:255'],
                'email'              => ['required', 'email', 'unique:users,email'],
                'password'           => ['required', 'string', 'min:8', 'confirmed'],
                'phone'              => ['nullable', 'string', 'max:20'],
                'address_line'       => ['nullable', 'string', 'max:500'],
                'profile_image_path' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'],
                'cover_image_path'   => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'],
                'role_id'            => ['required', 'exists:roles,id']
            ]);

            // Prevent superadmin role assignment through this interface
            $role = Role::findOrFail($request->role_id);
            if ($role->is_superadmin) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot assign superadmin role through this interface.'
                    ], 403);
                }

                return back()->withErrors(['role_id' => 'Cannot assign superadmin role.']);
            }

            // Prepare user data
            $userData = [
                'name'         => $request->name,
                'email'        => $request->email,
                'password'     => bcrypt($request->password),
                'user_type'    => strtolower($role->name), // Set user_type to the role name
                'is_active'    => true,
                'phone'        => $request->phone,
                'address_line' => $request->address_line
            ];

            // Handle profile image upload
            if ($request->hasFile('profile_image_path')) {
                $profileImage                   = $request->file('profile_image_path');
                $profilePath                    = $profileImage->store('users/profiles', 'public');
                $userData['profile_image_path'] = $profilePath;
            }

            // Handle cover image upload
            if ($request->hasFile('cover_image_path')) {
                $coverImage                   = $request->file('cover_image_path');
                $coverPath                    = $coverImage->store('users/covers', 'public');
                $userData['cover_image_path'] = $coverPath;
            }

            // Create the user
            $user = User::create($userData);

            // Assign the selected role to the user
            $user->roles()->attach($role->id);

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
                ->with('success', "User '{$user->name}' created successfully with {$role->name} role.");

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors'  => $e->errors()
                ], 422);
            }

            return back()->withErrors($e->errors())->withInput();
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
        $roles = Role::where('is_active', true)
            ->where('is_superadmin', false)
            ->orderBy('name')
            ->get();

        $userRoles = $user->roles()->pluck('roles.id')->toArray();

        return view('backend.pages.users.edit', compact('user', 'roles', 'userRoles'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        try {
            // Prevent editing of superadmin users
            if ($user->isSuperadmin()) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot edit superadmin users.'
                    ], 403);
                }

                return back()->with('error', 'Cannot edit superadmin users.');
            }

            $request->validate([
                'name'               => ['required', 'string', 'max:255'],
                'email'              => ['required', 'email', 'unique:users,email,' . $user->id],
                'password'           => ['nullable', 'string', 'min:8', 'confirmed'],
                'phone'              => ['nullable', 'string', 'max:20'],
                'address_line'       => ['nullable', 'string', 'max:500'],
                'profile_image_path' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'],
                'cover_image_path'   => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'],
                'role_id'            => ['nullable', 'exists:roles,id']
            ]);

            // Prevent superadmin role assignment through this interface
            if ($request->filled('role_id')) {
                $role = Role::findOrFail($request->role_id);
                if ($role->is_superadmin) {
                    if ($request->expectsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Cannot assign superadmin role through this interface.'
                        ], 403);
                    }

                    return back()->withErrors(['role_id' => 'Cannot assign superadmin role.']);
                }

                // Prevent current user from removing own dashboard access
                if ($user->id === auth()->id()) {
                    if (!$role->permissions()->where('slug', 'dashboard.view')->exists()) {
                        if ($request->expectsJson()) {
                            return response()->json([
                                'success' => false,
                                'message' => 'You cannot remove your own dashboard access.'
                            ], 403);
                        }

                        return back()->withErrors(['role_id' => 'You cannot remove your own dashboard access.']);
                    }
                }
            }

            // Prepare user data
            $userData = [
                'name'         => $request->name,
                'email'        => $request->email,
                'phone'        => $request->phone,
                'address_line' => $request->address_line
            ];

            // Only update password if provided
            if ($request->filled('password')) {
                $userData['password'] = bcrypt($request->password);
            }

            // Handle profile image upload
            if ($request->hasFile('profile_image_path')) {
                $profileImage                   = $request->file('profile_image_path');
                $profilePath                    = $profileImage->store('users/profiles', 'public');
                $userData['profile_image_path'] = $profilePath;
            }

            // Handle cover image upload
            if ($request->hasFile('cover_image_path')) {
                $coverImage                   = $request->file('cover_image_path');
                $coverPath                    = $coverImage->store('users/covers', 'public');
                $userData['cover_image_path'] = $coverPath;
            }

            // Update the user
            $user->update($userData);

            // Update roles if provided
            if ($request->filled('role_id')) {
                $role = Role::find($request->role_id);
                $user->roles()->sync([$request->role_id]);
                // Sync user_type with the role name
                $user->update(['user_type' => strtolower($role->name)]);
            }

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

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors'  => $e->errors()
                ], 422);
            }

            return back()->withErrors($e->errors())->withInput();
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
