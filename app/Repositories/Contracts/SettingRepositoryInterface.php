<?php

namespace App\Repositories\Contracts;

interface SettingRepositoryInterface
{
    /**
     * Get all settings as a collection.
     */
    public function all();

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
     */
    public function getSection(string $section): array;

    /**
     * Update all settings for a section.
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
}
