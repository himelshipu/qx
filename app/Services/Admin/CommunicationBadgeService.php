<?php

declare (strict_types = 1);

namespace App\Services\Admin;

use App\Models\User;
use App\Repositories\Contracts\CommunicationBadgeRepositoryInterface;

final class CommunicationBadgeService
{
    public function __construct(
        private readonly CommunicationBadgeRepositoryInterface $repository,
    ) {
    }

    /**
     * @return array{conversations:int,support_tickets:int,notifications:int}
     */
    public function getSidebarBadgeCounts(User $user): array
    {
        return $this->repository->getSidebarBadgeCounts($user);
    }
}
