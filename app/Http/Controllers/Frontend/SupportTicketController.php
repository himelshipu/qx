<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\SupportCategory;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SupportTicketController extends Controller
{
    /**
     * Show the support page
     */
    public function index()
    {
        return view('frontend.pages.support', [
            'title' => 'Support'
        ]);
    }

    /**
     * Store a new support ticket
     */
    public function store(Request $request)
    {
        // Require authentication
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to submit a support ticket.'
            ], 401);
        }

        $validated = $request->validate([
            'subject'     => 'required|string|max:255',
            'category'    => 'required|in:Orders & Payments,General Question,Feedback,Other',
            'description' => 'required|string|min:10'
        ]);

        // Get or create category
        $category = SupportCategory::where('name', $validated['category'])->first();
        if (!$category) {
            $category = SupportCategory::create([
                'name'      => $validated['category'],
                'slug'      => Str::slug(strtolower($validated['category'])),
                'is_active' => true
            ]);
        }

        $ticket = SupportTicket::create([
            'requester_user_id'   => Auth::id(),
            'support_category_id' => $category->id,
            'subject'             => $validated['subject'],
            'description'         => $validated['description'],
            'status'              => 'open',
            'priority'            => 'medium',
            'source'              => 'web'
        ]);

        $adminAndModeratorIds = User::query()
            ->whereIn('user_type', ['admin', 'moderator'])
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->unique()
            ->values();

        foreach ($adminAndModeratorIds as $recipientId) {
            Notification::create([
                'user_id' => $recipientId,
                'type' => 'support',
                'title' => 'New support ticket submitted',
                'body' => sprintf('Ticket %s: %s', $ticket->ticket_number, $ticket->subject),
                'data_json' => [
                    'action_url' => route('dashboard.support-tickets.show', $ticket),
                    'ticket_id' => $ticket->id,
                    'ticket_number' => $ticket->ticket_number,
                ],
                'notifiable_type' => SupportTicket::class,
                'notifiable_id' => $ticket->id,
                'is_read' => false,
            ]);
        }

        return response()->json([
            'success'   => true,
            'message'   => 'Your support ticket has been submitted successfully. Our team will review it shortly.',
            'ticket_id' => $ticket->id
        ]);
    }
}
