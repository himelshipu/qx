<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

final class ImageHelper
{
    /**
     * Resolve image URL for display in views.
     * Properly handles stored paths from Storage facade.
     *
     * @param ?string $path Database image path (stored via Storage::store())
     * @param string $fallback Fallback image asset path
     * @return string Resolvable URL for img src attribute
     */
    public static function url(?string $path, string $fallback = 'default.webp'): string
    {
        $fallbackPath = ltrim($fallback, '/');
        $fallbackUrl  = asset($fallbackPath);

        $normalizedPath = trim((string) $path);

        if ($normalizedPath === '' || $normalizedPath === '0' || strtolower($normalizedPath) === 'null') {
            return $fallbackUrl;
        }

        // If already an external URL, return as-is
        if (str_starts_with($normalizedPath, 'http://') || str_starts_with($normalizedPath, 'https://')) {
            return $normalizedPath;
        }

        // If data URI, return as-is
        if (str_starts_with($normalizedPath, 'data:image/')) {
            return $normalizedPath;
        }

        $normalizedPath = ltrim($normalizedPath, '/');

        // Handle legacy paths with 'storage/' prefix (backward compatibility)
        if (str_starts_with($normalizedPath, 'storage/')) {
            $normalizedPath = ltrim(str_replace('storage/', '', $normalizedPath, 1), '/');
            return Storage::disk('public')->url($normalizedPath);
        }

        // Handle public asset paths that should not use Storage::url()
        if (str_starts_with($normalizedPath, 'images/') || str_starts_with($normalizedPath, 'build/')) {
            return asset($normalizedPath);
        }

        // For relative storage paths, use Storage::url() to generate proper public URL
        // This handles paths like 'brand-profile-images/filename.jpg'
        return Storage::disk('public')->url($normalizedPath);
    }

    /**
     * Get image URLs for both profile and cover with fallback logic.
     * Useful for getting preview images from a Brand/User model.
     *
     * @param ?string $profilePath
     * @param ?string $coverPath
     * @param string $profileFallback
     * @param string $coverFallback
     * @return array{profile: string, cover: string}
     */
    public static function getImageUrls(
        ?string $profilePath,
        ?string $coverPath,
        string $profileFallback = 'default.webp',
        string $coverFallback = 'default.webp'
    ): array {
        return [
            'profile' => self::url($profilePath, $profileFallback),
            'cover' => self::url($coverPath, $coverFallback),
        ];
    }
}
