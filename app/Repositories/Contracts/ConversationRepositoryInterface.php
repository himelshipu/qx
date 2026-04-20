<?php

declare (strict_types = 1);

namespace App\Repositories\Contracts;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ConversationRepositoryInterface
{
    /**
     * @param array<string, mixed> $filters
     */
    public function paginateForDashboard(User $user, array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function findBrandConversation(int $brandUserId, int $influencerId): ?Conversation;

    public function create(array $data): Conversation;

    public function createMessage(array $data): Message;

    public function getMessagesForConversation(Conversation $conversation, int $perPage = 20): LengthAwarePaginator;

    public function assignModerator(Conversation $conversation, int $moderatorUserId): Conversation;

    public function findActiveModeratorForInfluencer(int $influencerId): ?int;
}
