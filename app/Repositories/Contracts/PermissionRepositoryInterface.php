<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Permission;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

interface PermissionRepositoryInterface
{
    public function paginateWithRoles(int $perPage = 20): LengthAwarePaginator;

    /**
     * @return SupportCollection<string, Collection<int, Permission>>
     */
    public function getActiveGroupedByModule(): SupportCollection;

    /**
     * @return SupportCollection<string, Collection<int, Permission>>
     */
    public function getActiveGroupedByModuleWithRoles(): SupportCollection;

    /**
     * @return Collection<int, Permission>
     */
    public function getActive(): Collection;

    public function findByIdOrFail(int $id): Permission;

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): Permission;

    /**
     * @param array<string, mixed> $data
     */
    public function update(Permission $permission, array $data): Permission;

    public function delete(Permission $permission): bool;

    public function toggleStatus(Permission $permission): Permission;
}