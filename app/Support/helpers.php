<?php

use App\Helpers\ImageHelper;

if (!function_exists('image_url')) {
    function image_url(?string $path, string $fallback = 'default.webp'): string
    {
        return ImageHelper::url($path, $fallback);
    }
}
