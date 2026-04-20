<?php

declare (strict_types = 1);

namespace App\Repositories\Eloquent;

use App\Models\SupportCategory;
use App\Models\SupportTicket;
use App\Models\User;
use App\Repositories\Contracts\SupportTicketRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

final class EloquentSupportTicketRepository implements SupportTicketRepositoryInterface
{
    public function paginateForDashboard(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return SupportTicket::query()
            ->forDashboard()
            ->search((string) ($filters['search'] ?? ''))
            ->dashboardStatus($filters['status'] ?? null)
            ->dashboardCategory(isset($filters['category']) ? (int) $filters['category'] : null)
            ->dashboardPriority($filters['priority'] ?? null)
            ->dashboardOrder()
            ->paginate($perPage);
    }

    public function stats(): array
    {
        return [
            'total' => SupportTicket::count(),
            'new' => SupportTicket::newForSidebar()->count(),
            'resolved' => SupportTicket::resolved()->count(),
            'closed' => SupportTicket::query()->where('status', 'closed')->count(),
        ];
    }

    public function getAssignableAdmins(): Collection
    {
        return User::query()
            ->whereHas('roles', function ($query): void {
                $query->whereIn('name', ['admin', 'moderator']);
            })
            ->get();
    }

    public function getCategories(): Collection
    {
        return SupportCategory::query()->get(['id', 'name']);
    }

    public function update(SupportTicket $ticket, array $data): SupportTicket
    {
        $ticket->update($data);

        return $ticket->refresh();
    }

    public function delete(SupportTicket $ticket): bool
    {
        return (bool) $ticket->delete();
    }

    public function bulkUpdate(array $ids, array $data): int
    {
        return SupportTicket::query()->whereIn('id', $ids)->update($data);
    }
}
