<?php

declare(strict_types=1);

namespace App\Services\Admin;

use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Collection;

/**
 * Class AdminDashboardService
 *
 * Handles business logic for the admin dashboard.
 */
final class AdminDashboardService
{
    /**
     * Create a new service instance.
     *
     * @param UserRepositoryInterface $userRepository
     * @return void
     */
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {}

    /**
     * Get dashboard statistics.
     *
     * @return array<string, int>
     */
    public function getDashboardStats(): array
    {
        return [
            'totalUsers' => $this->getTotalUsers(),
            'recentUsers' => $this->getRecentUsers()->count(),
        ];
    }

    /**
     * Get total users count.
     *
     * @return int
     */
    public function getTotalUsers(): int
    {
        return $this->userRepository->getAll()->count();
    }

    /**
     * Get recent users.
     *
     * @return Collection<int, \App\Models\User>
     */
    public function getRecentUsers(): Collection
    {
        return $this->userRepository->getAll()->take(10);
    }
}
