<?php

declare (strict_types = 1);

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection as SupportCollection;

interface SettingRepositoryInterface
{
    /**
      * @return SupportCollection<string, \App\Models\Setting>
     */
    public function all(): SupportCollection;

    /**
     * Get a single setting by key.
     */
    public function get(string $key, mixed $default = null): mixed;

    /**
     * Set a single setting.
     */
    public function set(string $key, mixed $value): bool;

    /**
     * Delete a single setting by key.
     */
    public function delete(string $key): bool;

    /**
     * Get all settings for a section (branding, email, platform, footer).
        *
        * @return array<string,mixed>
     */
    public function getSection(string $section): array;

    /**
     * Update all settings for a section.
        *
        * @param array<string,mixed> $data
     */
    public function updateSection(string $section, array $data): bool;

    /**
     * Get file URL for a setting that contains a file path.
     */
    public function fileUrl(string $key, string $default = ''): string;

    /**
     * Clear all cached settings.
     */
    public function clearCache(): void;

    /**
      * @return Collection<int, \App\Models\StaticPage>
     */
    public function getAllStaticPages(): Collection;

    /**
     * @param class-string<Model> $modelClass
     * @return Collection<int, Model>
     */
    public function getTrashedRecords(string $modelClass, int $limit): Collection;

    /**
     * @param class-string<Model> $modelClass
     */
    public function restoreTrashedRecord(string $modelClass, int $id): void;
}
