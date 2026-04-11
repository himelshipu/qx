<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Influencer;
use App\Models\Message;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ConversationController extends Controller
{
    /**
     * Display conversations for frontend users.
     * Brands see their conversations.
     * Influencers don't directly access conversations (handled by moderators).
     */
    public function index(): View
    {
        $user = Auth::user();

        if ($user->user_type === 'brand') {
            // Brands see their conversations with influencers
            $conversations = Conversation::forBrand($user->id)->paginate(15);

            return view('frontend.conversations.index', compact('conversations'));
        } else {
            // Only brands can view conversations in frontend
            abort(403, 'Unauthorized');
        }
    }

    /**
     * Show a single conversation (frontend view).
     */
    public function show(Conversation $conversation): View
    {
        $user = Auth::user();

        // Authorization: only the brand who initiated can view
        if ($user->user_type !== 'brand' || $conversation->brand_user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $conversation->load(['influencer.user', 'handledBy', 'brandUser', 'order']);

        $messages = Message::forConversation($conversation->id);

        return view('frontend.conversations.show', compact('conversation', 'messages'));
    }

    /**
     * Store a message in the conversation (frontend).
     */
    public function storeMessage(Request $request, Conversation $conversation): RedirectResponse
    {
        $user = Auth::user();

        // Only brands can send messages in frontend
        if ($user->user_type !== 'brand' || $conversation->brand_user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'message' => 'required|string|min:1|max:5000'
        ]);

        // Create message
        Message::create([
            'conversation_id' => $conversation->id,
            'sender_user_id'  => $user->id,
            'sender_role'     => $user->user_type,
            'message'         => $validated['message'],
            'read_at'         => null
        ]);

        // Update conversation timestamp
        $conversation->touch();

        return redirect()
            ->route('frontend.conversations.show', $conversation->public_id)
            ->with('success', 'Message sent');
    }

    /**
     * Open conversation for a specific order/influencer pair without sending an auto-message.
     */
    public function openOrderConversation(Influencer $influencer, ?Order $order = null): RedirectResponse
    {
        $user = Auth::user();

        if ($user->user_type !== 'brand') {
            abort(403, 'Unauthorized');
        }

        $conversationQuery = Conversation::query()
            ->where('brand_user_id', $user->id)
            ->where('influencer_id', $influencer->id);

        if ($order !== null) {
            if ((int) $order->buyer_user_id !== (int) $user->id) {
                abort(403, 'Unauthorized');
            }

            $relatedOrderIds = $order->parent_order_id === null
                ? $order->childOrders()->pluck('id')->push($order->id)->filter()->unique()->values()
                : collect([$order->id, $order->parent_order_id])->filter()->unique()->values();

            $conversationQuery->where(function ($query) use ($relatedOrderIds) {
                $query->whereIn('order_id', $relatedOrderIds)
                    ->orWhereNull('order_id');
            });
        }

        $conversation = $conversationQuery->orderByDesc('updated_at')->first();

        if (! $conversation) {
            $conversation = Conversation::query()
                ->where('brand_user_id', $user->id)
                ->where('influencer_id', $influencer->id)
                ->orderByDesc('updated_at')
                ->first();
        }

        if (! $conversation) {
            $conversation = Conversation::create([
                'brand_user_id' => $user->id,
                'influencer_id' => $influencer->id,
                'conversation_type' => 'order',
                'order_id' => $order?->id,
                'title' => 'Order Conversation',
            ]);
        }

        return redirect()->route('frontend.conversations.show', $conversation->public_id);
    }
}
