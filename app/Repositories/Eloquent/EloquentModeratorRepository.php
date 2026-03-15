<?php

declare (strict_types = 1);

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\ModeratorRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Class EloquentModeratorRepository
 *
 * Handles moderator data access using Eloquent ORM.
 */
class EloquentModeratorRepository implements ModeratorRepositoryInterface
{
    /**
     * Get paginated moderators for dashboard listing.
     */
    public function paginateForDashboard(string $search, string $status, int $perPage = 12): LengthAwarePaginator
    {
        return User::query()
            ->where('user_type', 'moderator')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('phone', 'like', '%' . $search . '%');
                });
            })
            ->when($status === 'active', fn($query) => $query->where('is_active', true))
            ->when($status === 'inactive', fn($query) => $query->where('is_active', false))
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Get moderator summary stats for dashboard.
     *
     * @return array{total:int,active:int,inactive:int}
     */
    public function getStats(): array
    {
        return [
            'total'    => User::where('user_type', 'moderator')->count(),
            'active'   => User::where('user_type', 'moderator')->where('is_active', true)->count(),
            'inactive' => User::where('user_type', 'moderator')->where('is_active', false)->count()
        ];
    }

    /**
     * Create a moderator user.
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): User
    {
        return User::create($data);
    }

    /**
     * Update a moderator user.
     *
     * @param array<string, mixed> $data
     */
    public function update(User $moderator, array $data): User
    {
        $moderator->update($data);

        return $moderator->refresh();
    }

    /**
     * Delete a moderator user.
     */
    public function delete(User $moderator): bool
    {
        return (bool) $moderator->delete();
    }

    /**
     * Count records that should block moderator deletion.
     */
    public function getDependencyCount(User $moderator): int
    {
        return $moderator->createdPackages()->count()
         + $moderator->sentMessages()->count();
    }

    /**
     * Toggle moderator status and return updated record.
     */
    public function toggleStatus(User $moderator): User
    {
        $moderator->update([
            'is_active' => !$moderator->is_active
        ]);

        return $moderator->refresh();
    }
}
