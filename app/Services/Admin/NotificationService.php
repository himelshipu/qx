<?php

declare (strict_types = 1);

namespace App\Services\Admin;

use App\Models\Notification;
use App\Models\User;
use App\Repositories\Contracts\NotificationRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

final class NotificationService
{
    public function __construct(
        private readonly NotificationRepositoryInterface $repository,
    ) {
    }

    /**
     * @return array{notifications:LengthAwarePaginator,stats:array{total:int,unread:int,read:int}}
     */
    public function getIndexPayload(User $user, array $filters): array
    {
        return [
            'notifications' => $this->repository->paginateForDashboard($user, $filters),
            'stats' => $this->repository->stats($user),
        ];
    }

    /**
     * @return array{notifications:Collection<int,Notification>,unreadCount:int,hasUnread:bool}
     */
    public function getUnreadPayload(User $user): array
    {
        $notifications = $this->repository->getUnreadForUser($user);

        return [
            'notifications' => $notifications,
            'unreadCount' => $notifications->count(),
            'hasUnread' => $notifications->isNotEmpty(),
        ];
    }

    public function markAsRead(Notification $notification): Notification
    {
        return $this->repository->markAsRead($notification);
    }

    public function markAsUnread(Notification $notification): Notification
    {
        return $this->repository->markAsUnread($notification);
    }

    public function markAllAsRead(User $user): int
    {
        return $this->repository->markAllAsRead($user);
    }

    public function delete(Notification $notification): bool
    {
        return $this->repository->delete($notification);
    }

    public function clearAll(User $user): int
    {
        return $this->repository->clearAll($user);
    }
}
