<?php

declare (strict_types = 1);

namespace App\Repositories\Eloquent;

use App\Models\Conversation;
use App\Models\Notification;
use App\Models\SupportTicket;
use App\Models\User;
use App\Repositories\Contracts\CommunicationBadgeRepositoryInterface;

final class EloquentCommunicationBadgeRepository implements CommunicationBadgeRepositoryInterface
{
    /**
     * @return array{conversations:int,support_tickets:int,notifications:int}
     */
    public function getSidebarBadgeCounts(User $user): array
    {
        return [
            'conversations' => $this->getConversationCount($user),
            'support_tickets' => $this->getSupportTicketCount($user),
            'notifications' => $this->getNotificationCount($user),
        ];
    }

    private function getConversationCount(User $user): int
    {
        $query = Conversation::query()
            ->forSidebarUnreadCount($user)
            ->withUnreadMessagesForUser($user);

        return match ($user->user_type) {
            'brand', 'moderator', 'admin' => $query->count(),
            default => 0,
        };
    }

    private function getSupportTicketCount(User $user): int
    {
        $query = SupportTicket::query()
            ->newForSidebar();

        return in_array($user->user_type, ['admin', 'moderator'], true)
            ? $query->count()
            : 0;
    }

    private function getNotificationCount(User $user): int
    {
        return Notification::query()
            ->where('user_id', $user->id)
            ->unread()
            ->count();
    }
}
