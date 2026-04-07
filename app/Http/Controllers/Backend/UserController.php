<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display user list with realtime search and status filtering.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->string('q', ''));
        $status = (string) $request->string('status', 'all');

        $usersQuery = User::query()
            ->with([
                'brand:id,user_id',
                'creator:id,user_id'
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

        $payload = [
            'users'  => $users,
            'stats'  => $stats,
            'search' => $search,
            'status' => $status
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
}
