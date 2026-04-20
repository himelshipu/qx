<?php

declare (strict_types = 1);

namespace App\Repositories\Contracts;

use App\Models\User;

interface CommunicationBadgeRepositoryInterface
{
    /**
     * @return array{conversations:int,support_tickets:int,notifications:int}
     */
    public function getSidebarBadgeCounts(User $user): array;
}
