<?php

declare (strict_types = 1);

namespace App\Repositories\Contracts;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface NotificationRepositoryInterface
{
    /**
     * @param array<string, mixed> $filters
     */
    public function paginateForDashboard(User $user, array $filters, int $perPage = 15): LengthAwarePaginator;

    /**
     * @return array{total:int,unread:int,read:int}
     */
    public function stats(User $user): array;

    /**
     * @return Collection<int, Notification>
     */
    public function getUnreadForUser(User $user, int $limit = 8): Collection;

    public function markAsRead(Notification $notification): Notification;

    public function markAsUnread(Notification $notification): Notification;

    public function markAllAsRead(User $user): int;

    public function delete(Notification $notification): bool;

    public function clearAll(User $user): int;
}
