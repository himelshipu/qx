<?php

declare (strict_types = 1);

namespace App\Services\Auth;

use App\Models\Conversation;
use App\Models\Influencer;
use App\Models\ModeratorAssignment;
use App\Models\User;
use App\Repositories\Contracts\ConversationRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class ConversationService
{
    public function __construct(
        private readonly ConversationRepositoryInterface $repository,
        private readonly PendingPostAuthActionService $pendingPostAuthActionService,
    ) {
    }

    /**
     * @param array<string, mixed> $filters
     */
    public function paginateForDashboard(User $user, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginateForDashboard($user, $filters, $perPage);
    }

    public function getMessagesForConversation(Conversation $conversation, int $perPage = 20): LengthAwarePaginator
    {
        return $this->repository->getMessagesForConversation($conversation, $perPage);
    }

    public function startNegotiation(Influencer $influencer, User $user): Conversation
    {
        $conversation = $this->repository->findBrandConversation($user->id, $influencer->id);

        if (!$conversation) {
            $conversation = $this->repository->create([
                'brand_user_id' => $user->id,
                'influencer_id' => $influencer->id,
            ]);
        }

        $influencerName = $influencer->display_name ?: ($influencer->user?->name ?? 'there');

        $this->repository->createMessage([
            'conversation_id' => $conversation->id,
            'sender_user_id' => $user->id,
            'sender_role' => $user->user_type,
            'message' => sprintf('hello %s,i want to discuss with you for a custom package', $influencerName),
            'read_at' => null,
        ]);

        $conversation->touch();

        return $conversation->refresh();
    }

    public function storeMessage(Conversation $conversation, User $user, string $message): Conversation
    {
        if ($user->user_type === 'brand' && $conversation->brand_user_id !== $user->id) {
            abort(403);
        }

        if ($user->user_type === 'moderator' && $conversation->handled_by_user_id !== $user->id) {
            abort(403);
        }

        if ($user->user_type === 'brand' && is_null($conversation->handled_by_user_id)) {
            $moderatorUserId = $this->repository->findActiveModeratorForInfluencer((int) $conversation->influencer_id);
            if ($moderatorUserId) {
                $this->repository->assignModerator($conversation, $moderatorUserId);
            }
        }

        $this->repository->createMessage([
            'conversation_id' => $conversation->id,
            'sender_user_id' => $user->id,
            'sender_role' => $user->user_type,
            'message' => $message,
            'read_at' => null,
        ]);

        $conversation->touch();

        return $conversation->refresh();
    }

    public static function createForPackageOrder(int $brandUserId, int $influencerId, ?int $orderId): Conversation
    {
        $moderatorAssignment = ModeratorAssignment::query()
            ->where('influencer_id', $influencerId)
            ->whereNull('unassigned_at')
            ->value('moderator_user_id');

        return Conversation::create([
            'conversation_type' => 'order',
            'influencer_id' => $influencerId,
            'brand_user_id' => $brandUserId,
            'handled_by_user_id' => $moderatorAssignment,
            'order_id' => $orderId,
            'influencer_direct_message_enabled' => false,
            'title' => 'Package Order Conversation',
        ]);
    }

    public function assignModerator(Conversation $conversation, int $moderatorUserId): Conversation
    {
        return $this->repository->assignModerator($conversation, $moderatorUserId);
    }

    /**
     * @return array{influencer:\App\Models\Influencer,respondent_name:string,respondent_type:string,actual_handler_id?:int|null,brand_user?:\App\Models\User,moderator_mode?:bool,messages:mixed}
     */
    public function getMediatedConversationView(Conversation $conversation, User $user): array
    {
        if ($user->user_type === 'brand') {
            return [
                'influencer' => $conversation->influencer,
                'respondent_name' => $conversation->influencer->user->name,
                'respondent_type' => 'influencer',
                'actual_handler_id' => $conversation->handled_by_user_id,
                'messages' => $conversation->messages,
            ];
        }

        if (in_array($user->user_type, ['moderator', 'admin'], true)) {
            return [
                'influencer' => $conversation->influencer,
                'brand_user' => $conversation->brandUser,
                'respondent_name' => $conversation->brandUser->name,
                'respondent_type' => 'brand',
                'moderator_mode' => true,
                'messages' => $conversation->messages,
            ];
        }

        abort(403);
    }
}
