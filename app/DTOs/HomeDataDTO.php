<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Class HomeDataDTO
 *
 * Data Transfer Object for homepage data.
 * Immutable - creates new instances with with* methods.
 */
final class HomeDataDTO
{
    /**
     * @param Collection<int, User> $users
     * @param string $appName
     * @param string $appVersion
     * @param int $totalUsers
     */
    public function __construct(
        public readonly Collection $users,
        public readonly string $appName,
        public readonly string $appVersion,
        public readonly int $totalUsers,
    ) {}

    /**
     * Create a new instance from an array.
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            users: $data['users'] ?? collect(),
            appName: $data['appName'] ?? config('app.name', 'Laravel'),
            appVersion: $data['appVersion'] ?? '1.0.0',
            totalUsers: $data['totalUsers'] ?? 0,
        );
    }

    /**
     * Create a new instance from a model.
     *
     * @param Collection<int, User> $users
     * @return self
     */
    public static function fromUsersCollection(Collection $users): self
    {
        return new self(
            users: $users,
            appName: config('app.name', 'Laravel'),
            appVersion: config('app.version', '1.0.0'),
            totalUsers: $users->count(),
        );
    }

    /**
     * Convert to array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'users' => $this->users,
            'appName' => $this->appName,
            'appVersion' => $this->appVersion,
            'totalUsers' => $this->totalUsers,
        ];
    }
}
