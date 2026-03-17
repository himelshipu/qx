<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Http\Request;

class SupportTicketController extends Controller
{
    /**
     * Display a listing of support tickets
     */
    public function index(Request $request)
    {
        $query = SupportTicket::with(['requester', 'assignedTo', 'category']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('support_category_id', $request->category);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Search by subject or description
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('ticket_number', 'like', "%{$search}%");
            });
        }

        $tickets    = $query->orderBy('created_at', 'desc')->paginate(15);
        $statuses   = ['open', 'in_progress', 'waiting_user', 'resolved', 'closed'];
        $categories = \App\Models\SupportCategory::get(['id', 'name']);
        $priorities = ['low', 'medium', 'high', 'urgent'];
        $admins     = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['admin', 'moderator']);
        })->get();

        return view('backend.pages.support-tickets.index', compact('tickets', 'statuses', 'categories', 'priorities', 'admins'));
    }

    /**
     * Show details of a specific support ticket
     */
    public function show(SupportTicket $ticket)
    {
        $ticket->load(['requester', 'assignedTo', 'category']);
        $admins = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['admin', 'moderator']);
        })->get();
        $statuses   = ['open', 'in_progress', 'waiting_user', 'resolved', 'closed'];
        $priorities = ['low', 'medium', 'high', 'urgent'];

        return view('backend.pages.support-tickets.show', compact('ticket', 'admins', 'statuses', 'priorities'));
    }

    /**
     * Update the specified support ticket
     */
    public function update(Request $request, SupportTicket $ticket)
    {
        $validated = $request->validate([
            'status'              => 'required|in:open,in_progress,waiting_user,resolved,closed',
            'priority'            => 'required|in:low,medium,high,urgent',
            'assigned_to_user_id' => 'nullable|exists:users,id',
            'resolved_at'         => 'nullable|date',
            'closed_at'           => 'nullable|date'
        ]);

        $ticket->update([
            'status'              => $validated['status'],
            'priority'            => $validated['priority'],
            'assigned_to_user_id' => $validated['assigned_to_user_id']
        ]);

        // If status is resolved or closed, set the timestamps
        if ($validated['status'] === 'resolved' && !$ticket->resolved_at) {
            $ticket->update(['resolved_at' => now()]);
        }
        if ($validated['status'] === 'closed' && !$ticket->closed_at) {
            $ticket->update(['closed_at' => now()]);
        }

        return redirect()->route('dashboard.support-tickets.show', $ticket)
            ->with('success', 'Support ticket updated successfully.');
    }

    /**
     * Delete a support ticket
     */
    public function destroy(SupportTicket $ticket)
    {
        $ticket->delete();

        return redirect()->route('dashboard.support-tickets.index')
            ->with('success', 'Support ticket deleted successfully.');
    }

    /**
     * Bulk update tickets
     */
    public function bulkUpdate(Request $request)
    {
        $validated = $request->validate([
            'ids'                 => 'required|array',
            'ids.*'               => 'exists:support_tickets,id',
            'status'              => 'nullable|in:open,in_progress,waiting_user,resolved,closed',
            'priority'            => 'nullable|in:low,medium,high,urgent',
            'assigned_to_user_id' => 'nullable|exists:users,id'
        ]);

        $tickets = SupportTicket::whereIn('id', $validated['ids']);

        if ($request->filled('status')) {
            $tickets->update(['status' => $validated['status']]);

            // If status is resolved or closed, set the timestamps
            if ($validated['status'] === 'resolved') {
                SupportTicket::whereIn('id', $validated['ids'])
                    ->whereNull('resolved_at')
                    ->update(['resolved_at' => now()]);
            }
            if ($validated['status'] === 'closed') {
                SupportTicket::whereIn('id', $validated['ids'])
                    ->whereNull('closed_at')
                    ->update(['closed_at' => now()]);
            }
        }

        if ($request->filled('priority')) {
            $tickets->update(['priority' => $validated['priority']]);
        }

        if ($request->filled('assigned_to_user_id')) {
            $tickets->update(['assigned_to_user_id' => $validated['assigned_to_user_id']]);
        }

        return redirect()->back()->with('success', 'Tickets updated successfully.');
    }
}
