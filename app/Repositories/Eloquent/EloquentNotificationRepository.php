<?php

declare (strict_types = 1);

namespace App\Repositories\Eloquent;

use App\Models\Notification;
use App\Models\User;
use App\Repositories\Contracts\NotificationRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

final class EloquentNotificationRepository implements NotificationRepositoryInterface
{
    public function paginateForDashboard(User $user, array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = Notification::query()->where('user_id', $user->id);

        if (($filters['filter'] ?? null) === 'unread') {
            $query->unread();
        } elseif (($filters['filter'] ?? null) === 'read') {
            $query->read();
        }

        if (!empty($filters['type'])) {
            $query->byType((string) $filters['type']);
        }

        return $query->orderByDesc('created_at')->paginate($perPage);
    }

    public function stats(User $user): array
    {
        return [
            'total' => Notification::where('user_id', $user->id)->count(),
            'unread' => Notification::where('user_id', $user->id)->unread()->count(),
            'read' => Notification::where('user_id', $user->id)->read()->count(),
        ];
    }

    public function getUnreadForUser(User $user, int $limit = 8): Collection
    {
        return Notification::query()
            ->where('user_id', $user->id)
            ->unread()
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    public function markAsRead(Notification $notification): Notification
    {
        $notification->markAsRead();

        return $notification->refresh();
    }

    public function markAsUnread(Notification $notification): Notification
    {
        $notification->markAsUnread();

        return $notification->refresh();
    }

    public function markAllAsRead(User $user): int
    {
        return Notification::where('user_id', $user->id)
            ->unread()
            ->update(['is_read' => true, 'read_at' => now()]);
    }

    public function delete(Notification $notification): bool
    {
        return (bool) $notification->delete();
    }

    public function clearAll(User $user): int
    {
        return (int) Notification::where('user_id', $user->id)->delete();
    }
}
