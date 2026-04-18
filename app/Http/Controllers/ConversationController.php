<?php

declare (strict_types = 1);

namespace App\Http\Controllers;

use App\Http\Requests\Backend\Conversation\AssignModeratorRequest;
use App\Http\Requests\Backend\Conversation\StoreConversationMessageRequest;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Influencer;
use App\Services\Auth\ConversationService;
use App\Services\Auth\PendingPostAuthActionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ConversationController extends Controller
{
    public function __construct(
        private readonly ConversationService $service,
    ) {
    }

    /**
     * Show conversation list
     */
    public function index(): View
    {
        $user = Auth::user();

        return view('backend.pages.conversations.index', [
            'conversations' => $this->service->paginateForDashboard($user, request()->only(['search', 'assignment'])),
        ]);
    }

    /**
     * Start a negotiation with an influencer by creating or retrieving a conversation
     */
    public function startNegotiation(Influencer $influencer): RedirectResponse
    {
        $influencerProfileUrl = route('influencer.profile', ['slug' => $influencer->user->slug]);

        if (!Auth::check()) {
            app(PendingPostAuthActionService::class)->rememberNegotiate($influencer->id, $influencerProfileUrl);

            return redirect()
                ->route('login')
                ->with('warning', 'Please login first to negotiate with this influencer.');
        }

        $user = Auth::user();

        if ($user->user_type !== 'brand') {
            return redirect($influencerProfileUrl)
                ->with('brand_action_required_modal', true)
                ->with('brand_action_required_message', 'Only brand accounts can add to cart or negotiate packages.');
        }

        $conversation = $this->service->startNegotiation($influencer, $user);

        return redirect()->route('frontend.conversations.show', ['conversation' => $conversation->public_id]);
    }

    /**
     * Show single conversation
     */
    public function show(Conversation $conversation): View
    {
        $user = Auth::user();

        if ($user->user_type === 'brand' && $conversation->brand_user_id !== $user->id) {
            abort(403);
        }

        if ($user->user_type === 'moderator' && $conversation->handled_by_user_id !== $user->id) {
            abort(403);
        }

        $conversation->load(['influencer.user', 'handledBy', 'brandUser']);

        $this->markConversationMessagesAsRead($conversation, $user);

        return view('backend.pages.conversations.show', [
            'conversation' => $conversation,
            'messages' => $this->service->getMessagesForConversation($conversation),
            'isInfluencerView' => false,
            'isModerator' => $user->user_type === 'moderator' || $user->user_type === 'admin',
        ]);
    }

    /**
     * Store a new message
     * If no moderator assigned for this influencer, admin is notified
     */
    public function storeMessage(StoreConversationMessageRequest $request, Conversation $conversation): RedirectResponse
    {
        $conversation = $this->service->storeMessage($conversation, $request->user(), $request->validated()['message']);

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
        return app(ConversationService::class)::createForPackageOrder((int) $brandUserId, (int) $influencerId, $orderId !== null ? (int) $orderId : null);
    }

    /**
     * Assign moderator to conversation (admin only)
     */
    public function assignModerator(AssignModeratorRequest $request, Conversation $conversation): RedirectResponse
    {
        $this->service->assignModerator($conversation, (int) $request->validated()['moderator_user_id']);

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
        return $this->service->getMediatedConversationView($conversation, Auth::user());
    }

    private function markConversationMessagesAsRead(Conversation $conversation, $user): void
    {
        Message::query()
            ->where('conversation_id', $conversation->id)
            ->whereNull('read_at')
            ->where('sender_user_id', '!=', $user->id)
            ->update(['read_at' => now()]);
    }
}
