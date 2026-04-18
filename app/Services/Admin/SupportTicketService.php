<?php

declare (strict_types = 1);

namespace App\Services\Admin;

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
        return $this->repository->bulkUpdate($ids, array_filter([
            'status' => $validated['status'] ?? null,
            'priority' => $validated['priority'] ?? null,
            'assigned_to_user_id' => $validated['assigned_to_user_id'] ?? null,
        ], static fn ($value): bool => $value !== null));
    }
}
