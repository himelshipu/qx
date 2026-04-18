<?php

declare (strict_types = 1);

namespace App\Repositories\Eloquent;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\ModeratorAssignment;
use App\Models\User;
use App\Repositories\Contracts\ConversationRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class EloquentConversationRepository implements ConversationRepositoryInterface
{
    public function paginateForDashboard(User $user, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Conversation::query();

        if (!empty($filters['search'])) {
            $search = (string) $filters['search'];
            $query->where(function ($builder) use ($search): void {
                $builder->where('title', 'like', "%{$search}%")
                    ->orWhereHas('brandUser', function ($brandQuery) use ($search): void {
                        $brandQuery->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('influencer.user', function ($influencerQuery) use ($search): void {
                        $influencerQuery->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('handledBy', function ($handlerQuery) use ($search): void {
                        $handlerQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if (($filters['assignment'] ?? null) === 'assigned') {
            $query->whereNotNull('handled_by_user_id');
        } elseif (($filters['assignment'] ?? null) === 'unassigned') {
            $query->whereNull('handled_by_user_id');
        }

        return match ($user->user_type) {
            'brand' => $query->forBrand($user->id)->paginate($perPage),
            'moderator' => $query->forModerator($user->id)->paginate($perPage),
            'admin' => $query->forAdmin()->paginate($perPage),
            default => abort(403, 'Influencers cannot access conversations directly.'),
        };
    }

    public function findBrandConversation(int $brandUserId, int $influencerId): ?Conversation
    {
        return Conversation::query()
            ->where('brand_user_id', $brandUserId)
            ->where('influencer_id', $influencerId)
            ->first();
    }

    public function create(array $data): Conversation
    {
        return Conversation::create($data);
    }

    public function createMessage(array $data): Message
    {
        return Message::create($data);
    }

    public function getMessagesForConversation(Conversation $conversation, int $perPage = 20): LengthAwarePaginator
    {
        return Message::query()
            ->where('conversation_id', $conversation->id)
            ->with('sender')
            ->orderBy('created_at')
            ->paginate($perPage);
    }

    public function assignModerator(Conversation $conversation, int $moderatorUserId): Conversation
    {
        $conversation->update([
            'handled_by_user_id' => $moderatorUserId,
        ]);

        return $conversation->refresh();
    }

    public function findActiveModeratorForInfluencer(int $influencerId): ?int
    {
        return ModeratorAssignment::query()
            ->where('influencer_id', $influencerId)
            ->whereNull('unassigned_at')
            ->value('moderator_user_id');
    }
}
