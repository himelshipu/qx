<?php

namespace App\Repositories\Eloquent;

use App\Models\Setting;
use App\Repositories\Contracts\SettingRepositoryInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EloquentSettingRepository implements SettingRepositoryInterface
{
    private const CACHE_KEY = 'settings.all';
    private const CACHE_TTL = 3600; // 1 hour

    /**
     * Get all settings as a collection.
     */
    public function all()
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return Setting::all()->keyBy('key');
        });
    }

    /**
     * Get a single setting by key.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $settings = $this->all();
        $setting = $settings->get($key);

        return $setting ? $setting->value : $default;
    }

    /**
     * Set a single setting.
     */
    public function set(string $key, mixed $value): bool
    {
        Setting::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        $this->clearCache();

        return true;
    }

    /**
     * Delete a single setting by key.
     */
    public function delete(string $key): bool
    {
        Setting::where('key', $key)->delete();
        $this->clearCache();

        return true;
    }

    /**
     * Get all settings for a section (branding, email, platform, footer).
     */
    public function getSection(string $section): array
    {
        $prefix = "{$section}.";
        $settings = $this->all();

        return $settings
            ->filter(fn ($item) => Str::startsWith($item->key, $prefix))
            ->map(fn ($item) => $item->value)
            ->toArray();
    }

    /**
     * Update all settings for a section.
     */
    public function updateSection(string $section, array $data): bool
    {
        foreach ($data as $key => $value) {
            $fullKey = "{$section}.{$key}";
            $this->set($fullKey, $value);
        }

        return true;
    }

    /**
     * Get file URL for a setting that contains a file path.
     */
    public function fileUrl(string $key, string $default = ''): string
    {
        $value = $this->get($key, $default);

        if (!$value) {
            return $default;
        }

        if (Str::startsWith($value, ['http://', 'https://', '/'])) {
            return $value;
        }

        return Storage::url($value);
    }

    /**
     * Clear all cached settings.
     */
    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
