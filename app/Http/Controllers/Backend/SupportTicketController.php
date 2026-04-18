<?php

declare (strict_types = 1);

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\SupportTicket\BulkUpdateSupportTicketRequest;
use App\Http\Requests\Backend\SupportTicket\UpdateSupportTicketRequest;
use App\Models\SupportTicket;
use App\Services\Admin\SupportTicketService;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class SupportTicketController extends Controller
{
    public function __construct(
        private readonly SupportTicketService $service,
    ) {
    }

    /**
     * Display a listing of support tickets
     */
    public function index(Request $request): View
    {
        $payload = $this->service->getIndexPayload($request->only(['search', 'status', 'category', 'priority']));

        return view('backend.pages.support-tickets.index', $payload);
    }

    public function table(Request $request): View
    {
        $payload = $this->service->getTablePayload($request->only(['search', 'status', 'category', 'priority']));

        return view('backend.pages.support-tickets._results', $payload);
    }

    /**
     * Show details of a specific support ticket
     */
    public function show(SupportTicket $ticket): View
    {
        $ticket->load(['requester', 'assignedTo', 'category']);

        $payload = $this->service->getIndexPayload([]);

        return view('backend.pages.support-tickets.show', [
            'ticket' => $ticket,
            'admins' => $payload['admins'],
            'statuses' => $payload['statuses'],
            'priorities' => $payload['priorities'],
        ]);
    }

    /**
     * Update the specified support ticket
     */
    public function update(UpdateSupportTicketRequest $request, SupportTicket $ticket)
    {
        $ticket = $this->service->updateTicket($ticket, $request->validated());

        return redirect()->route('dashboard.support-tickets.show', $ticket)
            ->with('success', 'Support ticket updated successfully.');
    }

    /**
     * Delete a support ticket
     */
    public function destroy(SupportTicket $ticket)
    {
        $this->service->deleteTicket($ticket);

        return redirect()->route('dashboard.support-tickets.index')
            ->with('success', 'Support ticket deleted successfully.');
    }

    /**
     * Bulk update tickets
     */
    public function bulkUpdate(BulkUpdateSupportTicketRequest $request)
    {
        $validated = $request->validated();
        $this->service->bulkUpdate($validated['ids'], $validated);

        return redirect()->back()->with('success', 'Tickets updated successfully.');
    }
}
