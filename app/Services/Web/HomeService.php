<?php

declare(strict_types=1);

namespace App\Services\Web;

use App\DTOs\HomeDataDTO;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Collection;

/**
 * Class HomeService
 *
 * Handles business logic for the homepage.
 */
final class HomeService
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
     * Get homepage data.
     *
     * @return HomeDataDTO
     */
    public function getHomePageData(): HomeDataDTO
    {
        $users = $this->getRecentUsers();

        return HomeDataDTO::fromUsersCollection($users);
    }

    /**
     * Get recent users from repository.
     *
     * @return Collection<int, \App\Models\User>
     */
    private function getRecentUsers(): Collection
    {
        return $this->userRepository->getAll();
    }

    /**
     * Get featured users for homepage.
     *
     * @param int $limit
     * @return Collection<int, \App\Models\User>
     */
    public function getFeaturedUsers(int $limit = 5): Collection
    {
        return $this->userRepository->getAll()->take($limit);
    }
}
