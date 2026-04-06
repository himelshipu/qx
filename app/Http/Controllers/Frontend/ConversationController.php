<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Creator;
use App\Models\Message;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConversationController extends Controller
{
    /**
     * Display conversations for frontend users.
     * Brands see their conversations.
     * Creators don't directly access conversations (handled by moderators).
     */
    public function index(): View
    {
        $user = auth()->user();

        if ($user->user_type === 'brand') {
            // Brands see their conversations with creators
            $conversations = Conversation::where('brand_user_id', $user->id)
                ->with(['creator.user', 'handledBy', 'messages' => function ($query) {
                    $query->orderByDesc('created_at')->limit(1);
                }])
                ->orderByDesc('updated_at')
                ->paginate(15);

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
        $user = auth()->user();

        // Authorization: only the brand who initiated can view
        if ($user->user_type !== 'brand' || $conversation->brand_user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $conversation->load(['creator.user', 'handledBy', 'brandUser', 'order']);

        $messages = $conversation->messages()
            ->with('sender')
            ->orderBy('created_at')
            ->paginate(20);

        return view('frontend.conversations.show', compact('conversation', 'messages'));
    }

    /**
     * Store a message in the conversation (frontend).
     */
    public function storeMessage(Request $request, Conversation $conversation): RedirectResponse
    {
        $user = auth()->user();

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
            ->route('frontend.conversations.show', $conversation)
            ->with('success', 'Message sent');
    }
}
