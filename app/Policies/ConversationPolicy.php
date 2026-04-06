<?php

namespace App\Policies;

use App\Models\Conversation;
use App\Models\User;

class ConversationPolicy
{
    /**
     * Determine if the user can view the conversation.
     */
    public function view(User $user, Conversation $conversation): bool
    {
        // Admin can view all
        if (in_array($user->user_type, ['admin', 'superadmin'])) {
            return true;
        }

        // Brand can view their own conversations
        if ($user->user_type === 'brand' && $conversation->brand_user_id === $user->id) {
            return true;
        }

        // Moderator can view conversations assigned to them
        if ($user->user_type === 'moderator' && $conversation->handled_by_user_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can send messages in the conversation.
     */
    public function sendMessage(User $user, Conversation $conversation): bool
    {
        // Admin can message all
        if (in_array($user->user_type, ['admin', 'superadmin'])) {
            return true;
        }

        // Brand can message their own conversations
        if ($user->user_type === 'brand' && $conversation->brand_user_id === $user->id) {
            return true;
        }

        // Moderator can message assigned conversations
        if ($user->user_type === 'moderator' && $conversation->handled_by_user_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can assign a moderator.
     */
    public function assignModerator(User $user, Conversation $conversation): bool
    {
        // Only admin/superadmin can assign moderators
        return in_array($user->user_type, ['admin', 'superadmin']);
    }
}
