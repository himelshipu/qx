<?php

namespace App\Helpers;

final class ImageHelper
{
    /**
     * Resolve image URL with a global default fallback.
     */
    public static function url(?string $path, string $fallback = 'default.webp'): string
    {
        $fallbackPath = ltrim($fallback, '/');
        $fallbackUrl  = asset($fallbackPath);

        $normalizedPath = trim((string) $path);

        if ($normalizedPath === '' || $normalizedPath === '0' || strtolower($normalizedPath) === 'null') {
            return $fallbackUrl;
        }

        if (str_starts_with($normalizedPath, 'http://') || str_starts_with($normalizedPath, 'https://')) {
            return $normalizedPath;
        }

        if (str_starts_with($normalizedPath, 'data:image/')) {
            return $normalizedPath;
        }

        $normalizedPath = ltrim($normalizedPath, '/');

        if (str_starts_with($normalizedPath, 'storage/')) {
            return asset($normalizedPath);
        }

        if (str_starts_with($normalizedPath, 'images/') || str_starts_with($normalizedPath, 'build/')) {
            return asset($normalizedPath);
        }

        return asset('storage/' . $normalizedPath);
    }
}
