<?php

declare (strict_types = 1);

namespace App\Services\Admin;

use App\Models\Notification;
use App\Models\SupportTicket;
use App\Repositories\Contracts\SupportTicketRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

final class SupportTicketService
{
    public function __construct(
        private readonly SupportTicketRepositoryInterface $repository,
    ) {
    }

    /**
     * @return array{tickets:LengthAwarePaginator,stats:array{total:int,new:int,resolved:int,closed:int},statuses:array<int,string>,categories:Collection<int,mixed>,priorities:array<int,string>,admins:Collection<int,\App\Models\User>}
     */
    public function getIndexPayload(array $filters): array
    {
        return [
            'tickets' => $this->repository->paginateForDashboard($filters, (int) config('support-ticket.pagination', 15)),
            'stats' => $this->repository->stats(),
            'statuses' => ['open', 'in_progress', 'waiting_user', 'resolved', 'closed'],
            'categories' => $this->repository->getCategories(),
            'priorities' => ['low', 'medium', 'high', 'urgent'],
            'admins' => $this->repository->getAssignableAdmins(),
            'filters' => $filters,
        ];
    }

    public function getTablePayload(array $filters): array
    {
        return [
            'tickets' => $this->repository->paginateForDashboard($filters, (int) config('support-ticket.pagination', 15)),
        ];
    }

    public function updateTicket(SupportTicket $ticket, array $validated): SupportTicket
    {
        $originalStatus = (string) $ticket->status;
        $originalAssigneeId = (int) ($ticket->assigned_to_user_id ?? 0);

        $ticket = $this->repository->update($ticket, [
            'status' => $validated['status'],
            'priority' => $validated['priority'],
            'assigned_to_user_id' => $validated['assigned_to_user_id'] ?? null,
        ]);

        if (in_array($validated['status'], ['resolved', 'closed'], true)) {
            $timestampField = $validated['status'] === 'resolved' ? 'resolved_at' : 'closed_at';
            if (is_null($ticket->{$timestampField})) {
                $ticket->update([$timestampField => now()]);
            }
        }

        $this->notifyTicketParticipants(
            $ticket,
            $originalStatus,
            $originalAssigneeId,
            (string) $validated['status'],
            (int) ($validated['assigned_to_user_id'] ?? 0)
        );

        return $ticket->refresh();
    }

    public function deleteTicket(SupportTicket $ticket): bool
    {
        return $this->repository->delete($ticket);
    }

    /**
     * @param array<int> $ids
     * @param array<string, mixed> $validated
     */
    public function bulkUpdate(array $ids, array $validated): int
    {
        $tickets = SupportTicket::query()->whereIn('id', $ids)->get();

        $updated = $this->repository->bulkUpdate($ids, array_filter([
            'status' => $validated['status'] ?? null,
            'priority' => $validated['priority'] ?? null,
            'assigned_to_user_id' => $validated['assigned_to_user_id'] ?? null,
        ], static fn ($value): bool => $value !== null));

        if ($updated > 0) {
            foreach ($tickets as $ticket) {
                $this->notifyTicketParticipants(
                    $ticket,
                    (string) $ticket->status,
                    (int) ($ticket->assigned_to_user_id ?? 0),
                    (string) ($validated['status'] ?? $ticket->status),
                    (int) ($validated['assigned_to_user_id'] ?? 0)
                );
            }
        }

        return $updated;
    }

    private function notifyTicketParticipants(
        object $ticket,
        string $previousStatus,
        int $previousAssigneeId,
        string $newStatus,
        int $newAssigneeId
    ): void {
        $requesterUserId = (int) $ticket->requester_user_id;

        if ($requesterUserId > 0 && ($previousStatus !== $newStatus || $previousAssigneeId !== $newAssigneeId)) {
            Notification::create([
                'user_id' => $requesterUserId,
                'type' => 'support',
                'title' => 'Support ticket updated',
                'body' => sprintf('Your ticket "%s" is now %s.', $ticket->subject, str_replace('_', ' ', $newStatus)),
                'data_json' => [
                    'action_url' => route('frontend.support.index'),
                    'ticket_id' => $ticket->id,
                    'status' => $newStatus,
                ],
                'notifiable_type' => SupportTicket::class,
                'notifiable_id' => $ticket->id,
                'is_read' => false,
            ]);
        }

        if ($newAssigneeId > 0 && $newAssigneeId !== $previousAssigneeId) {
            Notification::create([
                'user_id' => $newAssigneeId,
                'type' => 'support',
                'title' => 'Support ticket assigned to you',
                'body' => sprintf('Ticket "%s" was assigned to you.', $ticket->subject),
                'data_json' => [
                    'action_url' => route('dashboard.support-tickets.show', $ticket),
                    'ticket_id' => $ticket->id,
                ],
                'notifiable_type' => SupportTicket::class,
                'notifiable_id' => $ticket->id,
                'is_read' => false,
            ]);
        }
    }
}
