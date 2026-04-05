<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Creator;
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
            // Brand sees conversations with creators/moderators
            $conversations = Conversation::where('brand_user_id', $user->id)
                ->with(['creator.user', 'handledBy', 'messages' => function ($query) {
                    $query->orderByDesc('created_at')->limit(1);
                }])
                ->orderByDesc('updated_at')
                ->paginate(15);
        } elseif ($user->user_type === 'admin') {
            // Admins see ALL conversations to manage and assign moderators
            $conversations = Conversation::with(['creator.user', 'brandUser', 'handledBy', 'messages' => function ($query) {
                $query->orderByDesc('created_at')->limit(1);
            }])
                ->orderByDesc('updated_at')
                ->paginate(15);
        } elseif ($user->user_type === 'moderator') {
            // Moderators see conversations assigned to them
            $conversations = Conversation::where('handled_by_user_id', $user->id)
                ->with(['creator.user', 'brandUser', 'messages' => function ($query) {
                    $query->orderByDesc('created_at')->limit(1);
                }])
                ->orderByDesc('updated_at')
                ->paginate(15);
        } else {
            // Creators don't see any conversations (they use moderators)
            abort(403, 'Creators cannot access conversations directly.');
        }

        return view('backend.pages.conversations.index', [
            'conversations' => $conversations
        ]);
    }

    /**
     * Start a negotiation with a creator by creating or retrieving a conversation
     */
    public function startNegotiation(Creator $creator): RedirectResponse
    {
        if (!auth()->check()) {
            app(PendingPostAuthActionService::class)->rememberNegotiate($creator->id);

            return redirect()
                ->route('login')
                ->with('warning', 'Please login first to negotiate with this creator.');
        }

        $user = auth()->user();

        // Only brands can start negotiations
        if ($user->user_type !== 'brand') {
            return redirect()->route('creator.profile', ['slug' => $creator->user->slug])
                ->with('error', 'Only brands can negotiate with creators');
        }

        // Find or create a conversation with this creator
        $conversation = Conversation::where('brand_user_id', $user->id)
            ->where('creator_id', $creator->id)
            ->first();

        if (!$conversation) {
            // Create a new conversation
            $conversation = Conversation::create([
                'brand_user_id' => $user->id,
                'creator_id'    => $creator->id
            ]);
        }

        return redirect()->route('dashboard.conversations.show', ['conversation' => $conversation]);
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

        $conversation->load(['creator.user', 'handledBy', 'brandUser']);
        $messages = $conversation->messages()
            ->with('sender')
            ->orderBy('created_at')
            ->paginate(20);

        return view('backend.pages.conversations.show', [
            'conversation'  => $conversation,
            'messages'      => $messages,
            'isCreatorView' => false, // Creator never sees chats directly
            'isModerator'   => $user->user_type === 'moderator' || $user->user_type === 'admin'
        ]);
    }

    /**
     * Store a new message
     * If no moderator assigned for this creator, admin is notified
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
            // Get active moderator for this creator, or assign one
            $moderatorAssignment = ModeratorAssignment::where('creator_id', $conversation->creator_id)
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
            ->route('dashboard.conversations.show', $conversation)
            ->with('success', 'Message sent');
    }

    /**
     * Create conversation for package order with moderator handling
     * Workflow B: Package Order
     */
    public static function createForPackageOrder($brandUserId, $creatorId, $orderId): Conversation
    {
        // Get active moderator for this creator
        $moderatorAssignment = ModeratorAssignment::where('creator_id', $creatorId)
            ->whereNull('unassigned_at')
            ->first();

        $conversation = Conversation::create([
            'conversation_type'              => 'order',
            'creator_id'                     => $creatorId,
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
     * Get conversation where brand thinks they're talking to creator
     * But they're actually talking to moderator
     * This is the key method for chat mediation
     */
    public function getMediatedConversationView(Conversation $conversation): array
    {
        $user = auth()->user();

        if ($user->user_type === 'brand') {
            // Brand sees creator info, but messages come from moderator
            return [
                'creator'           => $conversation->creator,
                'respondent_name'   => $conversation->creator->user->name,
                'respondent_type'   => 'creator', // Brand thinks they're talking to creator
                'actual_handler_id' => $conversation->handled_by_user_id,
                'messages'          => $conversation->messages
            ];
        }

        if (in_array($user->user_type, ['moderator', 'admin'])) {
            // Moderator sees they are moderating, who is the creator, and brand they're messaging
            return [
                'creator'         => $conversation->creator,
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
