<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\SupportCategory;
use App\Models\SupportTicket;
use Illuminate\Http\Request;

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
        if (!auth()->check()) {
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
            'requester_user_id'   => auth()->id(),
            'support_category_id' => $category->id,
            'subject'             => $validated['subject'],
            'description'         => $validated['description'],
            'status'              => 'open',
            'priority'            => 'medium',
            'source'              => 'web'
        ]);

        return response()->json([
            'success'   => true,
            'message'   => 'Your support ticket has been submitted successfully. Our team will review it shortly.',
            'ticket_id' => $ticket->id
        ]);
    }
}
