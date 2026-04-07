<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Influencer;
use App\Models\Message;
use App\Models\ModeratorAssignment;
use App\Services\Auth\PendingPostAuthActionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConversationController extends Controller
{
    /**
     * Show conversation list
     */
    public function index(): View
    {
        $user = auth()->user();

        if ($user->user_type === 'brand') {
            // Brand sees conversations with influencers/moderators
            $conversations = Conversation::forBrand($user->id)->paginate(15);
        } elseif ($user->user_type === 'admin') {
            // Admins see ALL conversations to manage and assign moderators
            $conversations = Conversation::forAdmin()->paginate(15);
        } elseif ($user->user_type === 'moderator') {
            // Moderators see conversations assigned to them
            $conversations = Conversation::forModerator($user->id)->paginate(15);
        } else {
            // Influencers don't see any conversations (they use moderators)
            abort(403, 'Influencers cannot access conversations directly.');
        }

        return view('backend.pages.conversations.index', [
            'conversations' => $conversations
        ]);
    }

    /**
     * Start a negotiation with an influencer by creating or retrieving a conversation
     */
    public function startNegotiation(Influencer $influencer): RedirectResponse
    {
        $influencerProfileUrl = route('influencer.profile', ['slug' => $influencer->user->slug]);

        if (!auth()->check()) {
            app(PendingPostAuthActionService::class)->rememberNegotiate($influencer->id, $influencerProfileUrl);

            return redirect()
                ->route('login')
                ->with('warning', 'Please login first to negotiate with this influencer.');
        }

        $user = auth()->user();

        // Only brands can start negotiations
        if ($user->user_type !== 'brand') {
            return redirect($influencerProfileUrl)
                ->with('warning', 'Only brand accounts can add to cart or negotiate packages.')
                ->with('brand_action_required_modal', true)
                ->with('brand_action_required_message', 'Only brand accounts can add to cart or negotiate packages.');
        }

        // Find or create a conversation with this influencer
        $conversation = Conversation::where('brand_user_id', $user->id)
            ->where('influencer_id', $influencer->id)
            ->first();

        if (!$conversation) {
            // Create a new conversation
            $conversation = Conversation::create([
                'brand_user_id' => $user->id,
                'influencer_id' => $influencer->id
            ]);
        }

        $influencerName = $influencer->display_name ?: ($influencer->user?->name ?? 'there');
        $messageText    = sprintf('hello %s,i want to discuss with you for a custom package', $influencerName);

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_user_id'  => $user->id,
            'sender_role'     => $user->user_type,
            'message'         => $messageText,
            'read_at'         => null
        ]);

        $conversation->touch();

        return redirect()->route('frontend.conversations.show', ['conversation' => $conversation->public_id]);
    }

    /**
     * Show single conversation
     */
    public function show(Conversation $conversation): View
    {
        $user = auth()->user();

        // Authorization: only brand or assigned moderator can view
        if ($user->user_type === 'brand' && $conversation->brand_user_id !== $user->id) {
            abort(403);
        }

        // Moderators can only view conversations assigned to them
        if ($user->user_type === 'moderator' && $conversation->handled_by_user_id !== $user->id) {
            abort(403);
        }

        // Admins can view all conversations

        $conversation->load(['influencer.user', 'handledBy', 'brandUser']);
        $messages = Message::forConversation($conversation->id);

        return view('backend.pages.conversations.show', [
            'conversation'  => $conversation,
            'messages'      => $messages,
            'isCreatorView' => false, // Influencer never sees chats directly
            'isModerator'   => $user->user_type === 'moderator' || $user->user_type === 'admin'
        ]);
    }

    /**
     * Store a new message
     * If no moderator assigned for this influencer, admin is notified
     */
    public function storeMessage(Request $request, Conversation $conversation): RedirectResponse
    {
        $user = auth()->user();

        // Authorization: only brand or assigned moderator can message
        if ($user->user_type === 'brand' && $conversation->brand_user_id !== $user->id) {
            abort(403);
        }

        // Moderators can only message conversations assigned to them
        if ($user->user_type === 'moderator' && $conversation->handled_by_user_id !== $user->id) {
            abort(403);
        }

        // Admins can message any conversation

        $validated = $request->validate([
            'message' => 'required|string|min:1|max:5000'
        ]);

        // If brand is messaging and no moderator assigned, notify admin
        if ($user->user_type === 'brand' && is_null($conversation->handled_by_user_id)) {
            // Get active moderator for this influencer, or assign one
            $moderatorAssignment = ModeratorAssignment::where('influencer_id', $conversation->influencer_id)
                ->whereNull('unassigned_at')
                ->first();

            if ($moderatorAssignment) {
                $conversation->update(['handled_by_user_id' => $moderatorAssignment->moderator_user_id]);
            } else {
                // Admin will handle assignment - for now just record message
                // In real scenario, send notification to admin
            }
        }

        // Create message
        Message::create([
            'conversation_id' => $conversation->id,
            'sender_user_id'  => $user->id,
            'sender_role'     => $user->user_type,
            'message'         => $validated['message'],
            'read_at'         => null
        ]);

        return redirect()
            ->route('dashboard.conversations.show', $conversation->public_id)
            ->with('success', 'Message sent');
    }

    /**
     * Create conversation for package order with moderator handling
     * Workflow B: Package Order
     */
    public static function createForPackageOrder($brandUserId, $influencerId, $orderId): Conversation
    {
        // Get active moderator for this influencer
        $moderatorAssignment = ModeratorAssignment::where('influencer_id', $influencerId)
            ->whereNull('unassigned_at')
            ->first();

        $conversation = Conversation::create([
            'conversation_type'              => 'order',
            'influencer_id'                  => $influencerId,
            'brand_user_id'                  => $brandUserId,
            'handled_by_user_id'             => $moderatorAssignment?->moderator_user_id,
            'order_id'                       => $orderId,
            'creator_direct_message_enabled' => false,
            'title'                          => 'Package Order Conversation'
        ]);

        // If no moderator yet, admin will see notification to assign one
        if (is_null($moderatorAssignment)) {
            // TODO: Send notification to admin to assign moderator
        }

        return $conversation;
    }

    /**
     * Assign moderator to conversation (admin only)
     */
    public function assignModerator(Request $request, Conversation $conversation): RedirectResponse
    {
        // Only admins can assign moderators
        if (auth()->user()->user_type !== 'admin') {
            abort(403, 'Only admins can assign moderators');
        }

        $validated = $request->validate([
            'moderator_user_id' => 'required|exists:users,id'
        ]);

        $conversation->update([
            'handled_by_user_id' => $validated['moderator_user_id']
        ]);

        return redirect()
            ->back()
            ->with('success', 'Moderator assigned to conversation');
    }

    /**
     * Get conversation where brand thinks they're talking to influencer
     * But they're actually talking to moderator
     * This is the key method for chat mediation
     */
    public function getMediatedConversationView(Conversation $conversation): array
    {
        $user = auth()->user();

        if ($user->user_type === 'brand') {
            // Brand sees influencer info, but messages come from moderator
            return [
                'influencer'        => $conversation->creator,
                'respondent_name'   => $conversation->creator->user->name,
                'respondent_type'   => 'influencer', // Brand thinks they're talking to creator
                'actual_handler_id' => $conversation->handled_by_user_id,
                'messages'          => $conversation->messages
            ];
        }

        if (in_array($user->user_type, ['moderator', 'admin'])) {
            // Moderator sees they are moderating, who is the influencer, and brand they're messaging
            return [
                'influencer'      => $conversation->creator,
                'brand_user'      => $conversation->brandUser,
                'respondent_name' => $conversation->brandUser->name,
                'respondent_type' => 'brand',
                'moderator_mode'  => true,
                'messages'        => $conversation->messages
            ];
        }

        abort(403);
    }
}
