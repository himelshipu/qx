<?php

declare (strict_types = 1);

namespace App\Repositories\Contracts;

use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface SupportTicketRepositoryInterface
{
    /**
     * @param array<string, mixed> $filters
     */
    public function paginateForDashboard(array $filters, int $perPage = 15): LengthAwarePaginator;

    /**
     * @return array{total:int,new:int,resolved:int,closed:int}
     */
    public function stats(): array;

    /**
     * @return Collection<int, User>
     */
    public function getAssignableAdmins(): Collection;

    /**
     * @return Collection<int, mixed>
     */
    public function getCategories(): Collection;

    /**
     * @param array<string, mixed> $data
     */
    public function update(SupportTicket $ticket, array $data): SupportTicket;

    public function delete(SupportTicket $ticket): bool;

    /**
     * @param array<int> $ids
     * @param array<string, mixed> $data
     */
    public function bulkUpdate(array $ids, array $data): int;
}
