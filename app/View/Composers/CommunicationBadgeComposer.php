<?php

declare(strict_types=1);

namespace App\View\Composers;

use App\Models\Conversation;
use App\Repositories\Contracts\NotificationRepositoryInterface;
use App\Services\Admin\CommunicationBadgeService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

final class CommunicationBadgeComposer
{
    public function __construct(
        private readonly CommunicationBadgeService $communicationBadgeService,
        private readonly NotificationRepositoryInterface $notificationRepository,
    ) {
    }

    public function compose(View $view): void
    {
        $user = Auth::user();

        $dashboardBadges = [
            'conversations' => 0,
            'support_tickets' => 0,
            'notifications' => 0,
        ];

        $frontendNotificationCount = 0;
        $frontendMessageCount = 0;

        if ($user) {
            $dashboardBadges = $this->communicationBadgeService->getSidebarBadgeCounts($user);
            $frontendNotificationCount = (int) ($this->notificationRepository->stats($user)['unread'] ?? 0);

            if ($user->user_type === 'brand') {
                $frontendMessageCount = Conversation::query()
                    ->forBrand($user->id)
                    ->withUnreadMessagesForUser($user)
                    ->count();
            }
        }

        $view->with([
            'dashboardBadgeCounts' => $dashboardBadges,
            'dashboardUnreadNotifications' => (int) ($dashboardBadges['notifications'] ?? 0),
            'dashboardUnreadConversations' => (int) ($dashboardBadges['conversations'] ?? 0),
            'frontendUnreadNotifications' => $frontendNotificationCount,
            'frontendUnreadMessages' => $frontendMessageCount,
        ]);
    }
}