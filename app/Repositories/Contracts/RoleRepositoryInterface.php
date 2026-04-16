<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;

interface RoleRepositoryInterface
{
    /**
     * @return Collection<int, Role>
     */
    public function getActiveNonSuperadmin(): Collection;

    /**
     * @return Collection<int, Role>
     */
    public function getActiveNonSuperadminWithPermissionCount(): Collection;

    /**
     * @param array<int, int> $ids
     * @return Collection<int, Role>
     */
    public function findByIds(array $ids): Collection;

    public function findById(int $id): ?Role;

    public function hasPermission(int $roleId, string $permissionSlug): bool;
}