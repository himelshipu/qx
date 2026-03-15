<?php

declare (strict_types = 1);

namespace App\Services\Admin;

use App\Models\User;
use App\Repositories\Contracts\ModeratorRepositoryInterface;

/**
 * Class ModeratorService
 *
 * Handles business rules for dashboard moderator management.
 */
final class ModeratorService
{
    public function __construct(
        private readonly ModeratorRepositoryInterface $moderatorRepository
    ) {}

    /**
     * Build moderator listing payload for dashboard index page.
     *
     * @return array{moderators:\Illuminate\Contracts\Pagination\LengthAwarePaginator,stats:array{total:int,active:int,inactive:int},search:string,status:string}
     */
    public function getListingPayload(string $search, string $status): array
    {
        return [
            'moderators' => $this->moderatorRepository->paginateForDashboard($search, $status),
            'stats'      => $this->moderatorRepository->getStats(),
            'search'     => $search,
            'status'     => $status
        ];
    }

    /**
     * Create a new moderator account.
     *
     * @param array<string, mixed> $validated
     */
    public function createModerator(array $validated, bool $isActive): User
    {
        return $this->moderatorRepository->create([
            'name'              => $validated['name'],
            'email'             => $validated['email'],
            'password'          => $validated['password'],
            'phone'             => $validated['phone'] ?? null,
            'user_type'         => 'moderator',
            'is_active'         => $isActive,
            'email_verified_at' => now()
        ]);
    }

    /**
     * Update a moderator account.
     *
     * @param array<string, mixed> $validated
     */
    public function updateModerator(User $moderator, array $validated, bool $isActive): User
    {
        $payload = [
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'phone'     => $validated['phone'] ?? null,
            'is_active' => $isActive
        ];

        if (!empty($validated['password']) && is_string($validated['password'])) {
            $payload['password'] = $validated['password'];
        }

        return $this->moderatorRepository->update($moderator, $payload);
    }

    /**
     * Delete a moderator if no critical dependencies exist.
     *
     * @return array{deleted:bool,message:string}
     */
    public function deleteModerator(User $moderator): array
    {
        $dependencyCount = $this->moderatorRepository->getDependencyCount($moderator);

        if ($dependencyCount > 0) {
            return [
                'deleted' => false,
                'message' => 'Moderator cannot be deleted because it already has related records.'
            ];
        }

        $this->moderatorRepository->delete($moderator);

        return [
            'deleted' => true,
            'message' => 'Moderator deleted successfully.'
        ];
    }

    /**
     * Toggle moderator active status.
     */
    public function toggleStatus(User $moderator): bool
    {
        return $this->moderatorRepository->toggleStatus($moderator)->is_active;
    }
}
