<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Interface UserRepositoryInterface
 *
 * Defines the contract for user data access operations.
 */
interface UserRepositoryInterface
{
    /**
     * Get all users.
     *
     * @return Collection<int, User>
     */
    public function getAll(): Collection;

    /**
     * Find a user by ID.
     *
     * @param int $id
     * @return User|null
     */
    public function findById(int $id): ?User;

    /**
     * Find a user by email.
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User;

    /**
     * Create a new user.
     *
     * @param array<string, mixed> $data
     * @return User
     */
    public function create(array $data): User;

    /**
     * Update an existing user.
     *
     * @param User $user
     * @param array<string, mixed> $data
     * @return User
     */
    public function update(User $user, array $data): User;

    /**
     * Delete a user.
     *
     * @param User $user
     * @return bool
     */
    public function delete(User $user): bool;

    public function paginateForDashboard(string $search, string $status, ?int $roleId, int $perPage): LengthAwarePaginator;

    /**
     * @return array{total:int,brands:int,influencers:int,moderators:int,admins:int,active:int,inactive:int}
     */
    public function getDashboardStats(): array;

    /**
     * @return Collection<int, User>
     */
    public function getAssignableUsers(): Collection;

    /**
     * @return array<int, int>
     */
    public function getRoleIds(User $user): array;

    /**
     * @param array<int, int> $roleIds
     */
    public function syncRoles(User $user, array $roleIds): void;

    public function toggleStatus(User $user): User;
}
